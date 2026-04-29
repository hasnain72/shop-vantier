<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\StoreSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function index(): View
    {
        $settings = StoreSetting::whereIn('key', [
            'checkout.guest_checkout',
            'checkout.require_phone',
            'checkout.note_enabled',
            'checkout.tip_enabled',
            'checkout.order_notes_placeholder',
            'checkout.thank_you_message',
        ])->pluck('value', 'key');

        return view('admin.settings.checkout', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'checkout_order_notes_placeholder' => ['nullable', 'string', 'max:500'],
            'checkout_thank_you_message'       => ['nullable', 'string', 'max:1000'],
        ]);

        $booleans = [
            'checkout.guest_checkout',
            'checkout.require_phone',
            'checkout.note_enabled',
            'checkout.tip_enabled',
        ];

        foreach ($booleans as $key) {
            $inputKey = str_replace('.', '_', $key);
            StoreSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $request->boolean($inputKey) ? '1' : '0']
            );
        }

        $strings = [
            'checkout.order_notes_placeholder' => $request->input('checkout_order_notes_placeholder'),
            'checkout.thank_you_message'       => $request->input('checkout_thank_you_message'),
        ];

        foreach ($strings as $key => $value) {
            StoreSetting::updateOrCreate(['key' => $key], ['value' => $value ?? '']);
        }

        return back()->with('success', 'Checkout settings saved.');
    }
}
