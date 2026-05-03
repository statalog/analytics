<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class RealPersonName implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $value = trim($value ?? '');

        // Check vowel ratio — real names have vowels (40%+ typically)
        $vowels = strlen(preg_replace('/[^aeiouAEIOU]/i', '', $value));
        $total = strlen(preg_replace('/[^a-zA-Z]/i', '', $value));
        if ($total > 0 && $vowels / $total < 0.25) {
            $fail('The ' . $attribute . ' contains too many random characters.');
            return;
        }

        // Check for random capitalization pattern (spam: JpOpWeZx, real: John or JOHN)
        if ($this->looksLikeRandomHash($value)) {
            $fail('The ' . $attribute . ' appears to be randomly generated.');
            return;
        }

        // Reject names that look like UUIDs or hex strings
        if ($this->looksLikeUuidOrHex($value)) {
            $fail('The ' . $attribute . ' is not a valid name.');
            return;
        }

        // Require at least one space or be short enough to be a single name
        if (strlen($value) > 15 && !str_contains($value, ' ')) {
            $fail('The ' . $attribute . ' should contain a space between first and last name.');
            return;
        }
    }

    private function looksLikeRandomHash(string $value): bool
    {
        // Remove spaces and non-letters for analysis
        $letters = preg_replace('/[^a-zA-Z]/', '', $value);
        if (strlen($letters) < 4) return false;

        // Count uppercase transitions — spam has many random ones
        $upper = 0;
        for ($i = 0; $i < strlen($letters); $i++) {
            if (ctype_upper($letters[$i])) $upper++;
        }

        $upperRatio = $upper / strlen($letters);

        // Real names: ~10% uppercase (maybe a capital in middle for "McDonald")
        // Spam: 30%+ uppercase (random pattern like JpOpWeZx = 50%)
        return $upperRatio > 0.25;
    }

    private function looksLikeUuidOrHex(string $value): bool
    {
        // Remove spaces
        $value = str_replace(' ', '', $value);

        // UUID pattern (8-4-4-4-12 hex): likely if matches
        if (preg_match('/^[a-f0-9]{8}-?[a-f0-9]{4}-?[a-f0-9]{4}-?[a-f0-9]{4}-?[a-f0-9]{12}$/i', $value)) {
            return true;
        }

        // Base64-like (mixed case, numbers, length 16+): JpOpWeZxelQMvKGYgVbZlVv
        if (preg_match('/^[A-Za-z0-9+\/]{16,}={0,2}$/', $value)) {
            return true;
        }

        // Hex string (very long, mostly hex chars)
        if (strlen($value) > 20 && preg_match('/^[a-f0-9]+$/i', $value)) {
            return true;
        }

        return false;
    }
}
