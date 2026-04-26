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

        if (!in_array($code, $supported, true)) {
            return back();
        }

        if ($user = $request->user()) {
            $user->forceFill(['locale' => $code])->save();
        }

        Cookie::queue(SetLocale::COOKIE, $code, 60 * 24 * 365);

        return back();
    }
}
