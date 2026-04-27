<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class LangSync extends Command
{
    protected $signature = 'lang:sync
                            {locale : Target locale code (e.g. es, pt_BR)}
                            {--dry : Report missing keys without writing files}
                            {--mark=[TRANSLATE] : Prefix used for unfilled English placeholders}';

    protected $description = 'Compare a locale against en and add any missing keys (with English values as placeholders)';

    public function handle(): int
    {
        $locale = $this->argument('locale');
        $dry    = (bool) $this->option('dry');
        $mark   = (string) $this->option('mark');

        if ($locale === 'en') {
            $this->error("Use a non-en locale.");
            return self::INVALID;
        }

        // Both OSS and cloud package lang dirs.
        $sources = [
            base_path('lang/en') => base_path("lang/{$locale}"),
        ];
        $cloudEn = base_path('packages/cloud/lang/en');
        if (is_dir($cloudEn)) {
            $sources[$cloudEn] = base_path("packages/cloud/lang/{$locale}");
        }

        $totalMissing = 0;
        $totalAdded   = 0;

        foreach ($sources as $enDir => $targetDir) {
            if (!is_dir($enDir)) continue;
            if (!is_dir($targetDir)) {
                if ($dry) {
                    $this->warn("[dry] would create: {$targetDir}");
                } else {
                    mkdir($targetDir, 0755, true);
                }
            }

            foreach (glob($enDir . '/*.php') as $enFile) {
                $name       = basename($enFile);
                $targetFile = $targetDir . '/' . $name;

                $en     = require $enFile;
                $target = is_file($targetFile) ? require $targetFile : [];

                $diff = $this->keyDiff($en, $target);
                if (empty($diff)) continue;

                $totalMissing += count($diff, COUNT_RECURSIVE) - count($diff);
                $this->line(str_pad("{$name}", 28) . count($this->flatten($diff)) . ' missing key(s)');

                if ($this->getOutput()->isVerbose()) {
                    foreach ($this->flatten($diff) as $k => $v) {
                        $this->line('  · ' . $k);
                    }
                }

                if (!$dry) {
                    $merged = $this->mergeWithMark($en, $target, $mark);
                    file_put_contents($targetFile, $this->varExport($merged));
                    $totalAdded += count($this->flatten($diff));
                }
            }
        }

        $this->newLine();
        if ($dry) {
            $this->info("Total missing in {$locale}: {$totalMissing} keys (re-run without --dry to add them)");
        } else {
            $this->info("Added {$totalAdded} placeholder keys to {$locale}. Search for '{$mark}' to find them.");
        }

        return self::SUCCESS;
    }

    /** Recursive diff: keys in $a not present in $b. */
    protected function keyDiff(array $a, array $b): array
    {
        $diff = [];
        foreach ($a as $k => $v) {
            if (!array_key_exists($k, $b)) {
                $diff[$k] = $v;
            } elseif (is_array($v) && is_array($b[$k])) {
                $sub = $this->keyDiff($v, $b[$k]);
                if (!empty($sub)) $diff[$k] = $sub;
            }
        }
        return $diff;
    }

    /** Merge: existing translations win; missing keys filled from $en with mark prefix. */
    protected function mergeWithMark(array $en, array $target, string $mark): array
    {
        $out = $target;
        foreach ($en as $k => $v) {
            if (is_array($v)) {
                $out[$k] = $this->mergeWithMark($v, $out[$k] ?? [], $mark);
            } elseif (!array_key_exists($k, $out)) {
                $out[$k] = trim($mark . ' ' . (is_string($v) ? $v : ''));
            }
        }
        return $out;
    }

    /** Flatten nested arrays into dot-notation keys for reporting. */
    protected function flatten(array $arr, string $prefix = ''): array
    {
        $out = [];
        foreach ($arr as $k => $v) {
            $key = $prefix === '' ? (string) $k : $prefix . '.' . $k;
            if (is_array($v)) {
                $out += $this->flatten($v, $key);
            } else {
                $out[$key] = $v;
            }
        }
        return $out;
    }

    /** Render a PHP array as a return-statement file. */
    protected function varExport(array $data): string
    {
        return "<?php\n\nreturn " . var_export($data, true) . ";\n";
    }
}
