<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PosController extends Controller
{
    /**
     * Show the POS sales screen.
     */
    public function index()
    {
        $categories = Category::all();
        // Load only products that are in stock (> 0)
        $products = Product::with('category')
            ->where('stock_quantity', '>', 0)
            ->get();

        return view('pos.index', compact('categories', 'products'));
    }

    /**
     * Process checkout transaction.
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'cart_items' => ['required', 'string'],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'tax_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'grand_total' => ['required', 'numeric', 'min:0'],
            'cash_received' => ['required', 'numeric', 'min:0'],
            'change_returned' => ['required', 'numeric', 'min:0'],
        ]);

        $cartItems = json_decode($request->cart_items, true);

        if (empty($cartItems)) {
            return back()->with('error', 'Your cart is empty.');
        }

        try {
            $sale = DB::transaction(function () use ($request, $cartItems) {
                // 1. Double check stock and validate items
                foreach ($cartItems as $item) {
                    $product = Product::findOrFail($item['id']);
                    if ($product->stock_quantity < $item['qty']) {
                        throw new \Exception("Insufficient stock for product: " . $product->name);
                    }
                }

                // 2. Generate unique invoice number
                $invoiceNo = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(4)) . '-' . time();
                while (Sale::where('invoice_no', $invoiceNo)->exists()) {
                    $invoiceNo = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(4)) . '-' . time();
                }

                // Calculate discount and tax values for record
                $discountValue = $request->subtotal * (($request->discount_percent ?? 0) / 100);
                $taxValue = ($request->subtotal - $discountValue) * (($request->tax_percent ?? 0) / 100);

                // 3. Create Sale record
                $sale = Sale::create([
                    'invoice_no' => $invoiceNo,
                    'user_id' => Auth::id(),
                    'subtotal' => $request->subtotal,
                    'tax' => $taxValue,
                    'discount' => $discountValue,
                    'grand_total' => $request->grand_total,
                    'cash_received' => $request->cash_received,
                    'change_returned' => $request->change_returned,
                    'status' => 'completed',
                ]);

                // 4. Create Sale Items & Decrement Stock
                foreach ($cartItems as $item) {
                    $product = Product::findOrFail($item['id']);
                    
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $product->id,
                        'quantity' => $item['qty'],
                        'purchase_price' => $product->purchase_price,
                        'selling_price' => $product->selling_price,
                        'total' => $product->selling_price * $item['qty'],
                    ]);

                    // Decrement stock
                    $product->decrement('stock_quantity', $item['qty']);
                }

                return $sale;
            });

            return redirect()->route('pos.receipt', $sale->id)
                ->with('success', 'Sale completed successfully!');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show printable receipt of a sale.
     */
    public function receipt(Sale $sale)
    {
        $sale->load(['user', 'items.product']);
        return view('pos.receipt', compact('sale'));
    }
}
