<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\StoreSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShippingProviderSettingsController extends Controller
{
    /** Keys persisted in `store_settings`. `env` mirrors config so admin can flip sandbox↔live. */
    private const KEYS = [
        'shipping.smsa.enabled',
        'shipping.smsa.env',
        'shipping.smsa.passkey',
        'shipping.smsa.account_no',
        'shipping.smsa.sender_name',
        'shipping.smsa.sender_contact',
        'shipping.smsa.sender_phone',
        'shipping.smsa.sender_email',
        'shipping.smsa.sender_city',
        'shipping.smsa.service_code',
    ];

    public function index(): View
    {
        $settings = StoreSetting::whereIn('key', self::KEYS)->pluck('value', 'key');

        return view('admin.settings.shipping-providers', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        foreach (self::KEYS as $key) {
            $dotKey = str_replace('.', '_', $key);
            StoreSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $request->input($dotKey)]
            );
        }

        return back()->with('success', 'Shipping-provider settings saved.');
    }
}
