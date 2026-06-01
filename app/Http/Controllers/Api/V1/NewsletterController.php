<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'name'  => ['nullable', 'string', 'max:255'],
        ]);

        $subscriber = NewsletterSubscriber::where('email', $data['email'])->first();

        if ($subscriber) {
            if ($subscriber->active) {
                return response()->json([
                    'success' => true,
                    'message' => 'already_subscribed',
                ]);
            }
            // Re-subscribe
            $subscriber->update([
                'active'           => true,
                'subscribed_at'    => now(),
                'unsubscribed_at'  => null,
                'name'             => $data['name'] ?? $subscriber->name,
            ]);
        } else {
            NewsletterSubscriber::create([
                'email'          => $data['email'],
                'name'           => $data['name'] ?? null,
                'source'         => 'website',
                'active'         => true,
                'subscribed_at'  => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'subscribed',
        ]);
    }

    public function unsubscribe(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        NewsletterSubscriber::where('email', $data['email'])->update([
            'active'          => false,
            'unsubscribed_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'unsubscribed']);
    }
}
