<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class LocaleController extends Controller
{
    public function set(Request $request, string $code): RedirectResponse
    {
        $supported = array_keys(config('statalog.locales', ['en' => 'English']));

        // Accept both 'pt_BR' (catalog form) and 'pt-BR' (browser/URL form).
        $normalized = str_replace('-', '_', $code);
        $resolved = null;
        foreach ($supported as $supportedCode) {
            if (strcasecmp($supportedCode, $normalized) === 0) {
                $resolved = $supportedCode;
                break;
            }
        }

        if (!$resolved) return back();

        if ($user = $request->user()) {
            $user->forceFill(['locale' => $resolved])->save();
        }

        Cookie::queue(SetLocale::COOKIE, $resolved, 60 * 24 * 365);

        return back();
    }
}
