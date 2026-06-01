<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function index(Request $request)
    {
        $query = NewsletterSubscriber::query()->orderByDesc('subscribed_at');

        if ($request->filled('q')) {
            $q = '%' . $request->q . '%';
            $query->where(fn ($w) => $w->where('email', 'like', $q)->orWhere('name', 'like', $q));
        }

        if ($request->filled('status')) {
            $query->where('active', $request->status === 'active');
        }

        $subscribers = $query->paginate(50)->withQueryString();

        return view('admin.newsletter.index', compact('subscribers'));
    }

    public function destroy(NewsletterSubscriber $subscriber)
    {
        $subscriber->delete();
        return back()->with('success', 'Subscriber deleted.');
    }
}
