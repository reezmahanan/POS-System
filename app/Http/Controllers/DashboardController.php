<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard home page.
     */
    public function index()
    {
        // 1. Gather stats
        $totalSales = Sale::where('status', 'completed')->sum('grand_total');
        $todaySales = Sale::where('status', 'completed')->whereDate('created_at', Carbon::today())->sum('grand_total');
        $totalProducts = Product::count();
        
        // Count products where stock is <= low stock threshold
        $lowStockProductsCount = Product::whereColumn('stock_quantity', '<=', 'low_stock_alert')->count();

        // 2. Fetch recent orders
        $recentSales = Sale::with('user')
            ->latest()
            ->take(5)
            ->get();

        // 3. Fetch low stock products list to display as warnings
        $lowStockProducts = Product::with('category')
            ->whereColumn('stock_quantity', '<=', 'low_stock_alert')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'totalSales',
            'todaySales',
            'totalProducts',
            'lowStockProductsCount',
            'recentSales',
            'lowStockProducts'
        ));
    }
}
