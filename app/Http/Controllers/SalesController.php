<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    /**
     * Display a listing of sales.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $sales = Sale::with('user')
            ->when($search, function ($query, $search) {
                return $query->where('invoice_no', 'like', "%{$search}%");
            })
            ->when($startDate, function ($query, $startDate) {
                return $query->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function ($query, $endDate) {
                return $query->whereDate('created_at', '<=', $endDate);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('sales.index', compact('sales', 'search', 'startDate', 'endDate'));
    }

    /**
     * Display the specified sale details.
     */
    public function show(Sale $sale)
    {
        $sale->load(['user', 'items.product']);
        return view('sales.show', compact('sale'));
    }

    /**
     * Cancel / Refund a completed sale and restore inventory levels.
     */
    public function refund(Sale $sale)
    {
        if ($sale->status === 'refunded') {
            return back()->with('error', 'This sale has already been refunded.');
        }

        try {
            DB::transaction(function () use ($sale) {
                // 1. Restore product stocks
                foreach ($sale->items as $item) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->increment('stock_quantity', $item->quantity);
                    }
                }

                // 2. Update sale status to refunded
                $sale->update([
                    'status' => 'refunded'
                ]);
            });

            return redirect()->route('sales.index')
                ->with('success', 'Sale transaction #' . $sale->invoice_no . ' refunded successfully and stock levels restored!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to refund sale: ' . $e->getMessage());
        }
    }
}
