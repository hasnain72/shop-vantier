<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\InventoryLevel;
use App\Models\Order;
use App\Models\OrderLineItem;
use App\Models\ProductVariant;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function sales(Request $request)
    {
        [$start, $end] = $this->dateRange($request);

        $rows = Order::whereBetween('created_at', [$start, $end])
            ->where('financial_status', 'paid')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as orders, SUM(total_price) as revenue')
            ->groupByRaw('DATE(created_at)')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $dates = [];
        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            $key = $d->toDateString();
            $dates[] = [
                'date'    => $key,
                'orders'  => $rows->has($key) ? (int)  $rows[$key]->orders  : 0,
                'revenue' => $rows->has($key) ? (float) $rows[$key]->revenue : 0,
            ];
        }

        $totals = Order::whereBetween('created_at', [$start, $end])
            ->where('financial_status', 'paid')
            ->selectRaw('COUNT(*) as total_orders, SUM(total_price) as total_revenue, AVG(total_price) as avg_order_value')
            ->first();

        return response()->json(compact('dates', 'totals'));
    }

    public function products(Request $request)
    {
        [$start, $end] = $this->dateRange($request);

        $rows = OrderLineItem::join('orders', 'orders.id', '=', 'order_line_items.order_id')
            ->whereBetween('orders.created_at', [$start, $end])
            ->where('orders.financial_status', 'paid')
            ->selectRaw('order_line_items.product_id, order_line_items.title,
                         SUM(order_line_items.quantity) as qty_sold,
                         SUM(order_line_items.price * order_line_items.quantity) as revenue')
            ->groupBy('order_line_items.product_id', 'order_line_items.title')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        return response()->json(['products' => $rows]);
    }

    public function customers(Request $request)
    {
        [$start, $end] = $this->dateRange($request);

        $newCustomers = Customer::whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupByRaw('DATE(created_at)')
            ->orderBy('date')
            ->get();

        $topBySpend = Customer::orderByDesc('total_spent')
            ->limit(10)
            ->get(['id', 'first_name', 'last_name', 'email', 'total_spent', 'orders_count']);

        return response()->json(compact('newCustomers', 'topBySpend'));
    }

    public function inventory(Request $request)
    {
        $rows = InventoryLevel::with(['inventoryItem.variant.product', 'location'])
            ->join('inventory_items', 'inventory_items.id', '=', 'inventory_levels.inventory_item_id')
            ->join('product_variants', 'product_variants.id', '=', 'inventory_items.variant_id')
            ->selectRaw('inventory_levels.*, (inventory_levels.available * inventory_items.cost) as stock_value')
            ->orderByDesc('stock_value')
            ->limit(50)
            ->get();

        $totalValue = InventoryLevel::join('inventory_items', 'inventory_items.id', '=', 'inventory_levels.inventory_item_id')
            ->selectRaw('SUM(inventory_levels.available * inventory_items.cost) as total')
            ->value('total');

        return response()->json(['items' => $rows, 'total_value' => (float) $totalValue]);
    }

    private function dateRange(Request $request): array
    {
        $start = $request->start_date
            ? Carbon::parse($request->start_date)->startOfDay()
            : now()->subDays(29)->startOfDay();

        $end = $request->end_date
            ? Carbon::parse($request->end_date)->endOfDay()
            : now()->endOfDay();

        return [$start, $end];
    }
}
