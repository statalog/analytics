<?php
/**
 * Statalog - Open source web analytics.
 * https://statalog.com
 * @license AGPL-3.0
 */

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ConfigurationController extends Controller
{
    public function index(): View
    {
        return view('user.configuration.index');
    }
}
