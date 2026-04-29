<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\StoreSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoreSettingsController extends Controller
{
    private array $keys = [
        'store_name', 'store_email', 'store_phone', 'store_address',
        'currency', 'timezone', 'weight_unit', 'order_prefix',
    ];

    public function show()
    {
        $settings = StoreSetting::whereIn('key', $this->keys)
            ->pluck('value', 'key');
        return view('admin.settings.store', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'store_name'    => 'required|string|max:255',
            'store_email'   => 'required|email',
            'store_phone'   => 'nullable|string|max:50',
            'store_address' => 'nullable|string|max:500',
            'currency'      => 'required|string|size:3',
            'timezone'      => 'required|string',
            'weight_unit'   => 'required|in:kg,g,lb,oz',
            'order_prefix'  => 'nullable|string|max:10',
            'logo'          => 'nullable|image|max:2048',
            'favicon'       => 'nullable|image|max:512',
        ]);

        foreach (array_intersect_key($data, array_flip($this->keys)) as $key => $value) {
            StoreSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('settings', 'public');
            StoreSetting::updateOrCreate(['key' => 'store_logo'], ['value' => $path]);
        }

        if ($request->hasFile('favicon')) {
            $path = $request->file('favicon')->store('settings', 'public');
            StoreSetting::updateOrCreate(['key' => 'store_favicon'], ['value' => $path]);
        }

        return back()->with('success', 'Store settings saved.');
    }

    public function taxes()
    {
        $settings = StoreSetting::whereIn('key', ['taxes_included', 'tax_shipping', 'tax_rate'])
            ->pluck('value', 'key');

        $taxRates = StoreSetting::where('key', 'like', 'tax_rate_%')->get();

        return view('admin.settings.taxes', compact('settings', 'taxRates'));
    }

    public function updateTaxes(Request $request)
    {
        StoreSetting::updateOrCreate(['key' => 'taxes_included'], ['value' => $request->boolean('taxes_included') ? '1' : '0']);
        StoreSetting::updateOrCreate(['key' => 'tax_shipping'],   ['value' => $request->boolean('tax_shipping')   ? '1' : '0']);

        if ($request->tax_rate) {
            StoreSetting::updateOrCreate(['key' => 'tax_rate'], ['value' => $request->tax_rate]);
        }

        return back()->with('success', 'Tax settings saved.');
    }

    public function notifications()
    {
        $notifs = StoreSetting::where('key', 'like', 'notif_%')->pluck('value', 'key');
        return view('admin.settings.notifications', compact('notifs'));
    }

    public function updateNotifications(Request $request)
    {
        $request->validate([
            'notification_email' => ['nullable', 'email'],
        ]);

        if ($request->filled('notification_email')) {
            StoreSetting::updateOrCreate(['key' => 'notification_email'], ['value' => $request->notification_email]);
        }

        $keys = [
            'notif_order_confirmation', 'notif_order_shipped', 'notif_abandoned_cart',
            'notif_low_stock', 'notif_new_order', 'notif_refund_confirmation',
        ];

        foreach ($keys as $key) {
            StoreSetting::updateOrCreate(['key' => $key], ['value' => $request->boolean($key) ? '1' : '0']);
        }

        return back()->with('success', 'Notification settings saved.');
    }
}
