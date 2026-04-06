<?php

namespace App\Http\Controllers;

use App\Models\POSTransaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        // 1. Stats
        $todayRevenue = POSTransaction::whereDate('created_at', $today)->sum('total_amount');
        $yesterdayRevenue = POSTransaction::whereDate('created_at', $yesterday)->sum('total_amount');
        $revenueGrowth = $yesterdayRevenue > 0 ? (($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100 : 0;

        $todayOrders = POSTransaction::whereDate('created_at', $today)->count();
        $yesterdayOrders = POSTransaction::whereDate('created_at', $yesterday)->count();
        $ordersGrowth = $yesterdayOrders > 0 ? (($todayOrders - $yesterdayOrders) / $yesterdayOrders) * 100 : 0;

        $lowStockCount = Product::where('stock', '<=', DB::raw('min_stock'))->count();
        $avgOrderValue = POSTransaction::avg('total_amount') ?? 0;

        $stats = [
            'total_revenue' => $todayRevenue,
            'revenue_growth' => round($revenueGrowth, 1),
            'today_orders' => $todayOrders,
            'orders_growth' => round($ordersGrowth, 1),
            'low_stock_count' => $lowStockCount,
            'avg_order_value' => round($avgOrderValue, 2),
        ];

        // 2. Charts Data
        // Sales Last 7 Days
        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $last7Days->push([
                'date' => $date->format('M d'),
                'revenue' => POSTransaction::whereDate('created_at', $date)->sum('total_amount')
            ]);
        }

        // Payment Methods
        $paymentMethods = POSTransaction::select('payment_method', DB::raw('count(*) as count'))
            ->groupBy('payment_method')
            ->get();

        $charts = [
            'sales_labels' => $last7Days->pluck('date'),
            'sales_data' => $last7Days->pluck('revenue'),
            'payment_labels' => $paymentMethods->pluck('payment_method'),
            'payment_data' => $paymentMethods->pluck('count'),
        ];

        // 3. Recent Transactions
        $recentTransactions = POSTransaction::latest()->take(5)->get();

        // 4. Top Products (derived from items JSON)
        $transactions = POSTransaction::select('items')->get();
        $productSales = [];
        foreach ($transactions as $trx) {
            foreach ($trx->items as $item) {
                $pid = $item['id'];
                if (!isset($productSales[$pid])) {
                    $productSales[$pid] = [
                        'name' => $item['name'],
                        'sku' => $item['sku'] ?? 'N/A',
                        'price' => $item['price'],
                        'total_qty' => 0
                    ];
                }
                $productSales[$pid]['total_qty'] += $item['qty'];
            }
        }
        $topProducts = collect($productSales)->sortByDesc('total_qty')->take(5);

        return view('dashboard', compact('stats', 'charts', 'recentTransactions', 'topProducts'));
    }
}
