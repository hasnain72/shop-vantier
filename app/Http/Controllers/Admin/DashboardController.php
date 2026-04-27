<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'orders_today' => 0,
                'revenue_today' => 0,
                'customers_total' => 0,
                'products_total' => 0,
            ],
            'recent_orders' => [],
            'low_stock' => [],
            'best_sellers' => [],
            'revenue_chart' => [],
        ]);
    }
}

