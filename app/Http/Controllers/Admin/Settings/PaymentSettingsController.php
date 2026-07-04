<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\StoreSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentSettingsController extends Controller
{
    public function index(): View
    {
        $settings = StoreSetting::whereIn('key', [
            'payments.stripe.enabled', 'payments.stripe.key', 'payments.stripe.secret',
            'payments.paypal.enabled', 'payments.paypal.client_id', 'payments.paypal.client_secret', 'payments.paypal.mode',
            'payments.myfatoorah.enabled', 'payments.myfatoorah.env', 'payments.myfatoorah.api_key',
            'payments.cod.enabled', 'payments.cod.fee',
            'payments.bank_transfer.enabled', 'payments.bank_transfer.account_details',
        ])->pluck('value', 'key');

        return view('admin.settings.payments', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $fields = [
            'payments.stripe.enabled', 'payments.stripe.key', 'payments.stripe.secret',
            'payments.paypal.enabled', 'payments.paypal.client_id', 'payments.paypal.client_secret', 'payments.paypal.mode',
            'payments.myfatoorah.enabled', 'payments.myfatoorah.env', 'payments.myfatoorah.api_key',
            'payments.cod.enabled', 'payments.cod.fee',
            'payments.bank_transfer.enabled', 'payments.bank_transfer.account_details',
        ];

        foreach ($fields as $key) {
            $dotKey = str_replace('.', '_', $key);
            StoreSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $request->input($dotKey)]
            );
        }

        return back()->with('success', 'Payment settings saved.');
    }
}
