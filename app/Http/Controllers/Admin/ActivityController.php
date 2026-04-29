<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function index(Request $request): View
    {
        $query = ActivityLog::with('user')->latest();

        if ($request->filled('subject_type')) {
            $query->where('subject_type', 'like', '%' . $request->subject_type . '%');
        }

        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn ($q) =>
                $q->where('description', 'like', "%$s%")
                  ->orWhere('subject_type', 'like', "%$s%")
            );
        }

        $logs   = $query->paginate(50)->withQueryString();
        $events = ActivityLog::distinct()->pluck('event')->filter()->values();

        return view('admin.activity.index', compact('logs', 'events'));
    }
}
