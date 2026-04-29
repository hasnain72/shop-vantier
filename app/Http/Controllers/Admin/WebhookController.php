<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Webhook;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebhookController extends Controller
{
    public function index(): View
    {
        $webhooks = Webhook::latest()->get();
        $topics   = Webhook::availableTopics();

        return view('admin.webhooks.index', compact('webhooks', 'topics'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'topic'   => ['required', 'string', 'in:' . implode(',', Webhook::availableTopics())],
            'address' => ['required', 'url'],
        ]);

        Webhook::create($data);

        return redirect()->route('admin.webhooks.index')
            ->with('success', 'Webhook created successfully.');
    }

    public function update(Request $request, Webhook $webhook): RedirectResponse
    {
        $data = $request->validate([
            'topic'     => ['required', 'string', 'in:' . implode(',', Webhook::availableTopics())],
            'address'   => ['required', 'url'],
            'is_active' => ['boolean'],
        ]);

        $webhook->update($data);

        return redirect()->route('admin.webhooks.index')
            ->with('success', 'Webhook updated.');
    }

    public function destroy(Webhook $webhook): RedirectResponse
    {
        $webhook->delete();

        return redirect()->route('admin.webhooks.index')
            ->with('success', 'Webhook deleted.');
    }

    public function regenerateSecret(Webhook $webhook): RedirectResponse
    {
        $webhook->update(['secret' => \Illuminate\Support\Str::random(32)]);

        return redirect()->route('admin.webhooks.index')
            ->with('success', 'Secret regenerated.');
    }
}
