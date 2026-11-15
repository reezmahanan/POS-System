<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    /**
     * Display reports dashboard.
     */
    public function index()
    {
        // 1. Sales summaries
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();

        $dailySales = Sale::where('status', 'completed')->whereDate('created_at', $today)->sum('grand_total');
        $weeklySales = Sale::where('status', 'completed')->whereBetween('created_at', [$startOfWeek, Carbon::now()])->sum('grand_total');
        $monthlySales = Sale::where('status', 'completed')->whereBetween('created_at', [$startOfMonth, Carbon::now()])->sum('grand_total');

        // 2. Top-selling products
        $topSellingProducts = SaleItem::select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(total) as total_revenue'))
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completed')
            ->groupBy('product_id')
            ->orderBy('total_qty', 'desc')
            ->with('product.category')
            ->take(10)
            ->get();

        // 3. Stock valuation report metrics
        $products = Product::all();
        $totalCostValuation = 0;
        $totalRetailValuation = 0;
        
        foreach ($products as $product) {
            $totalCostValuation += $product->purchase_price * $product->stock_quantity;
            $totalRetailValuation += $product->selling_price * $product->stock_quantity;
        }
        
        $expectedProfitValuation = $totalRetailValuation - $totalCostValuation;

        return view('reports.index', compact(
            'dailySales',
            'weeklySales',
            'monthlySales',
            'topSellingProducts',
            'totalCostValuation',
            'totalRetailValuation',
            'expectedProfitValuation',
            'products'
        ));
    }

    /**
     * Export all completed sales to CSV format.
     */
    public function exportSales()
    {
        $headers = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename=sales_report_' . date('Y-m-d_His') . '.csv',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0'
        ];

        $sales = Sale::with('user')->latest()->get();

        $callback = function() use($sales) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Invoice No', 'Cashier Name', 'Subtotal (Rs.)', 'Tax (Rs.)', 'Discount (Rs.)', 'Grand Total (Rs.)', 'Payment Status', 'Transaction Date']);

            foreach ($sales as $sale) {
                fputcsv($file, [
                    $sale->invoice_no,
                    $sale->user->name,
                    $sale->subtotal,
                    $sale->tax,
                    $sale->discount,
                    $sale->grand_total,
                    $sale->status,
                    $sale->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export top-selling products to CSV.
     */
    public function exportProducts()
    {
        $headers = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename=product_sales_report_' . date('Y-m-d_His') . '.csv',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0'
        ];

        $topSellingProducts = SaleItem::select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(total) as total_revenue'))
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completed')
            ->groupBy('product_id')
            ->orderBy('total_qty', 'desc')
            ->with('product.category')
            ->get();

        $callback = function() use($topSellingProducts) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Product Name', 'SKU', 'Category', 'Quantity Sold', 'Revenue Generated (Rs.)']);

            foreach ($topSellingProducts as $item) {
                fputcsv($file, [
                    $item->product->name ?? 'N/A',
                    $item->product->sku ?? 'N/A',
                    $item->product->category->name ?? 'N/A',
                    $item->total_qty,
                    $item->total_revenue
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export current stock inventory valuation report to CSV.
     */
    public function exportStock()
    {
        $headers = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename=inventory_stock_valuation_' . date('Y-m-d_His') . '.csv',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0'
        ];

        $products = Product::with('category')->get();

        $callback = function() use($products) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Product Name', 'SKU', 'Category', 'Unit Cost (Rs.)', 'Unit Retail Price (Rs.)', 'Stock Quantity', 'Total Cost Value (Rs.)', 'Total Retail Value (Rs.)', 'Estimated Profit (Rs.)']);

            foreach ($products as $product) {
                $totalCost = $product->purchase_price * $product->stock_quantity;
                $totalRetail = $product->selling_price * $product->stock_quantity;
                $profitVal = $totalRetail - $totalCost;

                fputcsv($file, [
                    $product->name,
                    $product->sku,
                    $product->category->name,
                    $product->purchase_price,
                    $product->selling_price,
                    $product->stock_quantity,
                    $totalCost,
                    $totalRetail,
                    $profitVal
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
