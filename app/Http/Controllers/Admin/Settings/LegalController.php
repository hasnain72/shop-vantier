<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\StoreSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LegalController extends Controller
{
    private array $policyKeys = [
        'policy.privacy'  => 'Privacy Policy',
        'policy.terms'    => 'Terms of Service',
        'policy.refund'   => 'Refund Policy',
        'policy.shipping' => 'Shipping Policy',
    ];

    public function index(): View
    {
        $policies = StoreSetting::whereIn('key', array_keys($this->policyKeys))
            ->pluck('value', 'key');

        $labels = $this->policyKeys;

        return view('admin.settings.legal', compact('policies', 'labels'));
    }

    public function update(Request $request): RedirectResponse
    {
        foreach (array_keys($this->policyKeys) as $key) {
            $inputKey = str_replace('.', '_', $key);
            StoreSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $request->input($inputKey) ?? '']
            );
        }

        return back()->with('success', 'Legal policies saved.');
    }
}
