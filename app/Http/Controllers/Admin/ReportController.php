<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $categoryRevenue = DB::table('order_items')
            ->select('categories.name as category_name', DB::raw('SUM(order_items.price * order_items.quantity) as total_revenue'))
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'paid')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_revenue')
            ->get();

        $totalOrders = Order::count();
        $totalCustomers = DB::table('users')->where('role', 'customer')->count();
        $paidRevenue = Order::where('status', 'paid')->sum('total');

        $revenueByDate = Order::selectRaw('DATE(created_at) as date, SUM(total) as total_revenue')
            ->where('status', 'paid')
            ->groupByRaw('DATE(created_at)')
            ->orderByDesc('date')
            ->get();

        $revenueByMonth = Order::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(total) as total_revenue')
            ->where('status', 'paid')
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get();

        $revenueByYear = Order::selectRaw('YEAR(created_at) as year, SUM(total) as total_revenue')
            ->where('status', 'paid')
            ->groupByRaw('YEAR(created_at)')
            ->orderByDesc('year')
            ->get();

        return view('admin.reports.index', compact(
            'categoryRevenue',
            'totalOrders',
            'totalCustomers',
            'paidRevenue',
            'revenueByDate',
            'revenueByMonth',
            'revenueByYear'
        ));
    }
}
