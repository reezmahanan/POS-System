@extends('layouts.admin')

@section('title', 'POS - Edit Product')
@section('page_title', 'Edit Product')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold text-secondary">Edit Product: {{ $product->name }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <!-- Product Name -->
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label text-secondary fw-semibold">Product Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $product->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- SKU -->
                        <div class="col-md-6 mb-3">
                            <label for="sku" class="form-label text-secondary fw-semibold">SKU / Barcode <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('sku') is-invalid @enderror" id="sku" name="sku" value="{{ old('sku', $product->sku) }}" required>
                            @error('sku')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <!-- Category -->
                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="form-label text-secondary fw-semibold">Category <span class="text-danger">*</span></label>
                            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Product Image -->
                        <div class="col-md-6 mb-3">
                            <label for="image" class="form-label text-secondary fw-semibold">Product Image</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image">
                            <small class="text-muted d-block mt-1">Leave blank to keep current image. Allowed: jpeg, png, jpg, gif. Max: 2MB.</small>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            @if($product->image)
                                <div class="mt-3">
                                    <label class="form-label text-secondary d-block fw-semibold">Current Image:</label>
                                    <img src="{{ asset('storage/' . $product->image) }}" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;" alt="Current Product Image">
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <!-- Purchase Price -->
                        <div class="col-md-4 mb-3">
                            <label for="purchase_price" class="form-label text-secondary fw-semibold">Purchase Price (Cost) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0" class="form-control @error('purchase_price') is-invalid @enderror" id="purchase_price" name="purchase_price" value="{{ old('purchase_price', $product->purchase_price) }}" required>
                                @error('purchase_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Selling Price -->
                        <div class="col-md-4 mb-3">
                            <label for="selling_price" class="form-label text-secondary fw-semibold">Selling Price (Retail) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0" class="form-control @error('selling_price') is-invalid @enderror" id="selling_price" name="selling_price" value="{{ old('selling_price', $product->selling_price) }}" required>
                                @error('selling_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Stock Quantity -->
                        <div class="col-md-4 mb-3">
                            <label for="stock_quantity" class="form-label text-secondary fw-semibold">Stock Quantity <span class="text-danger">*</span></label>
                            <input type="number" min="0" class="form-control @error('stock_quantity') is-invalid @enderror" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" required>
                            @error('stock_quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <!-- Low Stock Threshold -->
                        <div class="col-md-4 mb-3">
                            <label for="low_stock_alert" class="form-label text-secondary fw-semibold">Low-Stock Alert Level <span class="text-danger">*</span></label>
                            <input type="number" min="0" class="form-control @error('low_stock_alert') is-invalid @enderror" id="low_stock_alert" name="low_stock_alert" value="{{ old('low_stock_alert', $product->low_stock_alert) }}" required>
                            <small class="text-muted">Triggers alert when stock drops below this value.</small>
                            @error('low_stock_alert')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Save Changes</button>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
