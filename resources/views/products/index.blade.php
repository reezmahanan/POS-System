@extends('layouts.admin')

@section('title', 'POS - Products')
@section('page_title', 'Product Management')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                <h5 class="mb-0 fw-bold text-secondary">Products List</h5>
                @if(Auth::user()->role === 'admin')
                    <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-circle me-1"></i> Add Product
                    </a>
                @endif
            </div>
            
            <div class="card-body">
                <!-- Filters -->
                <form action="{{ route('products.index') }}" method="GET" class="row g-3 mb-4">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Search by name or SKU..." value="{{ $search }}">
                    </div>
                    <div class="col-md-3">
                        <select name="category_id" class="form-select">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-secondary"><i class="bi bi-search me-1"></i> Filter</button>
                        @if($search || $categoryId)
                            <a href="{{ route('products.index') }}" class="btn btn-outline-danger"><i class="bi bi-x-circle me-1"></i> Clear</a>
                        @endif
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 80px;">Image</th>
                                <th>Name & SKU</th>
                                <th>Category</th>
                                <th>Cost Price</th>
                                <th>Retail Price</th>
                                <th>Stock</th>
                                @if(Auth::user()->role === 'admin')
                                    <th style="width: 180px;" class="text-end pe-3">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                @php
                                    $isLowStock = $product->stock_quantity <= $product->low_stock_alert;
                                @endphp
                                <tr class="{{ $isLowStock ? 'table-danger-subtle' : '' }}">
                                    <td>
                                        @if($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;" alt="{{ $product->name }}">
                                        @else
                                            <div class="bg-secondary text-white rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                <i class="bi bi-box fs-4"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $product->name }}</div>
                                        <small class="text-muted">SKU: {{ $product->sku }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-dark">{{ $product->category->name }}</span>
                                    </td>
                                    <td class="fw-semibold">${{ number_format($product->purchase_price, 2) }}</td>
                                    <td class="fw-bold text-success">${{ number_format($product->selling_price, 2) }}</td>
                                    <td>
                                        @if($isLowStock)
                                            <span class="badge bg-danger text-white" data-bs-toggle="tooltip" title="Low Stock Warning!">
                                                {{ $product->stock_quantity }} (Low)
                                            </span>
                                        @else
                                            <span class="badge bg-success text-white">{{ $product->stock_quantity }}</span>
                                        @endif
                                    </td>
                                    @if(Auth::user()->role === 'admin')
                                        <td class="text-end pe-3">
                                            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary me-2">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                            
                                            <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No products found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
