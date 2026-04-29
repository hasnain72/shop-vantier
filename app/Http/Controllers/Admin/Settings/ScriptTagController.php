<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\StoreSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScriptTagController extends Controller
{
    private array $scriptKeys = [
        'scripts.head'    => 'Additional <head> scripts',
        'scripts.body'    => 'End of <body> scripts',
        'scripts.ga_id'   => 'Google Analytics Measurement ID',
        'scripts.fb_pixel'=> 'Facebook Pixel ID',
        'scripts.gtm_id'  => 'Google Tag Manager ID',
    ];

    public function index(): View
    {
        $settings = StoreSetting::whereIn('key', array_keys($this->scriptKeys))
            ->pluck('value', 'key');

        return view('admin.settings.scripts', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'scripts_ga_id'     => ['nullable', 'string', 'max:50'],
            'scripts_fb_pixel'  => ['nullable', 'string', 'max:50'],
            'scripts_gtm_id'    => ['nullable', 'string', 'max:50'],
            'scripts_head'      => ['nullable', 'string', 'max:10000'],
            'scripts_body'      => ['nullable', 'string', 'max:10000'],
        ]);

        $map = [
            'scripts.ga_id'    => $request->input('scripts_ga_id'),
            'scripts.fb_pixel' => $request->input('scripts_fb_pixel'),
            'scripts.gtm_id'   => $request->input('scripts_gtm_id'),
            'scripts.head'     => $request->input('scripts_head'),
            'scripts.body'     => $request->input('scripts_body'),
        ];

        foreach ($map as $key => $value) {
            StoreSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back()->with('success', 'Script settings saved.');
    }
}
