<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderLineItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();

        $stats = [
            'orders_today'    => Order::whereDate('created_at', $today)->count(),
            'orders_week'     => Order::where('created_at', '>=', Carbon::now()->startOfWeek())->count(),
            'orders_month'    => Order::where('created_at', '>=', $monthStart)->count(),
            'revenue_today'   => Order::whereDate('created_at', $today)->sum('total_price'),
            'revenue_month'   => Order::where('created_at', '>=', $monthStart)->sum('total_price'),
            'customers_total' => Customer::count(),
            'products_total'  => Product::count(),
        ];

        $recentOrders = Order::with('customer')
            ->latest()
            ->limit(10)
            ->get();

        $lowStock = ProductVariant::with('product')
            ->where('inventory_quantity', '<', 5)
            ->where('is_active', true)
            ->orderBy('inventory_quantity')
            ->limit(10)
            ->get();

        $bestSellers = OrderLineItem::select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->where('created_at', '>=', $monthStart)
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->with('product')
            ->get();

        // Revenue per day for last 30 days
        $revenueChart = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_price) as revenue')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(29)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('revenue', 'date');

        // Fill missing days with 0
        $chartDates = [];
        for ($i = 29; $i >= 0; $i--) {
            $d = Carbon::now()->subDays($i)->toDateString();
            $chartDates[$d] = $revenueChart[$d] ?? 0;
        }

        return view('admin.dashboard', compact(
            'stats',
            'recentOrders',
            'lowStock',
            'bestSellers',
            'chartDates',
        ));
    }
}

