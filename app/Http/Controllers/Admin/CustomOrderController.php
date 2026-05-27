<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomOrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = CustomOrder::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn ($q) =>
                $q->where('name', 'like', "%$s%")
                  ->orWhere('email', 'like', "%$s%")
                  ->orWhere('phone', 'like', "%$s%")
            );
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $sortMap = [
            'oldest'   => ['id', 'asc'],
            'newest'   => ['id', 'desc'],
            'name-asc' => ['name', 'asc'],
        ];
        [$col, $dir] = $sortMap[$request->sort] ?? ['id', 'desc'];
        $query->orderBy($col, $dir);

        $customOrders = $query->paginate(25)->withQueryString();
        $counts = [
            'all'     => CustomOrder::count(),
            'new'     => CustomOrder::where('status', 'new')->count(),
            'read'    => CustomOrder::where('status', 'read')->count(),
            'replied' => CustomOrder::where('status', 'replied')->count(),
            'closed'  => CustomOrder::where('status', 'closed')->count(),
        ];

        return view('admin.custom-orders.index', compact('customOrders', 'counts'));
    }

    public function show(CustomOrder $customOrder): View
    {
        if ($customOrder->status === 'new') {
            $customOrder->update(['status' => 'read']);
        }

        return view('admin.custom-orders.show', compact('customOrder'));
    }

    public function update(Request $request, CustomOrder $customOrder): RedirectResponse
    {
        $data = $request->validate([
            'status'      => ['required', 'in:new,read,replied,closed'],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $customOrder->update($data);

        return back()->with('success', 'Custom order updated.');
    }

    public function destroy(CustomOrder $customOrder): RedirectResponse
    {
        $customOrder->delete();

        return redirect()->route('admin.custom-orders.index')
            ->with('success', 'Custom order deleted.');
    }
}
