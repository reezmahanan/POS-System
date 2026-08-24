<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $categoryId = $request->input('category_id');

        $categories = Category::all();

        $products = Product::with('category')
            ->when($search, function ($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->when($categoryId, function ($query, $categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('products.index', compact('products', 'categories', 'search', 'categoryId'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:50', 'unique:products,sku'],
            'category_id' => ['required', 'exists:categories,id'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'low_stock_alert' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        // Automatically generate SKU if left empty
        $sku = $request->sku;
        if (empty($sku)) {
            $sku = 'PROD-' . strtoupper(Str::random(6)) . '-' . time();
            // Ensure uniqueness
            while (Product::where('sku', $sku)->exists()) {
                $sku = 'PROD-' . strtoupper(Str::random(6)) . '-' . time();
            }
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        Product::create([
            'name' => $request->name,
            'sku' => strtoupper($sku),
            'category_id' => $request->category_id,
            'purchase_price' => $request->purchase_price,
            'selling_price' => $request->selling_price,
            'stock_quantity' => $request->stock_quantity,
            'low_stock_alert' => $request->low_stock_alert ?? 10,
            'image' => $imagePath,
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully!');
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku,' . $product->id],
            'category_id' => ['required', 'exists:categories,id'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'low_stock_alert' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            // Delete the old image file if it exists
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product->update([
            'name' => $request->name,
            'sku' => strtoupper($request->sku),
            'category_id' => $request->category_id,
            'purchase_price' => $request->purchase_price,
            'selling_price' => $request->selling_price,
            'stock_quantity' => $request->stock_quantity,
            'low_stock_alert' => $request->low_stock_alert,
            'image' => $imagePath,
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully!');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        // Delete image file if exists
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully!');
    }
}
