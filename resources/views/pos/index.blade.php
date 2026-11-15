@extends('layouts.admin')

@section('title', 'POS - Terminal')
@section('page_title', 'POS Terminal')

@section('styles')
<style>
    .product-grid {
        max-height: 60vh;
        overflow-y: auto;
    }
    .product-card {
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
        border: 1px solid #e9ecef;
    }
    .product-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.08);
    }
    .cart-container {
        max-height: 50vh;
        overflow-y: auto;
    }
    .category-btn {
        white-space: nowrap;
    }
</style>
@endsection

@section('content')
<div class="row">
    <!-- Left Column: Products Listing -->
    <div class="col-lg-7 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <!-- Search & Categories Filter -->
                <div class="mb-3">
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" id="product-search" class="form-control" placeholder="Search product by name or SKU..." onkeyup="filterProducts()">
                    </div>
                    
                    <!-- Categories Carousel/Pills -->
                    <div class="d-flex gap-2 overflow-x-auto pb-2 border-bottom" id="category-pills">
                        <button class="btn btn-sm btn-primary category-btn active" onclick="filterCategory('all', this)">All</button>
                        @foreach($categories as $category)
                            <button class="btn btn-sm btn-outline-secondary category-btn" onclick="filterCategory('{{ $category->id }}', this)">
                                {{ $category->name }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="row row-cols-2 row-cols-md-3 g-3 product-grid p-1" id="product-catalog">
                    @forelse($products as $product)
                        <div class="col product-item" data-category="{{ $product->category_id }}" data-name="{{ strtolower($product->name) }}" data-sku="{{ strtolower($product->sku) }}">
                            <div class="card h-100 product-card" onclick="addToCart({
                                id: {{ $product->id }},
                                name: '{{ addslashes($product->name) }}',
                                sku: '{{ $product->sku }}',
                                selling_price: {{ $product->selling_price }},
                                stock_quantity: {{ $product->stock_quantity }}
                            })">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" style="height: 120px; object-fit: cover;" alt="{{ $product->name }}">
                                @else
                                    <div class="bg-light text-secondary d-flex align-items-center justify-content-center" style="height: 120px;">
                                        <i class="bi bi-box fs-1"></i>
                                    </div>
                                @endif
                                <div class="card-body p-2 d-flex flex-column justify-content-between">
                                    <div>
                                        <h6 class="card-title fw-bold mb-1 text-truncate">{{ $product->name }}</h6>
                                        <small class="text-muted d-block mb-1">SKU: {{ $product->sku }}</small>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mt-2">
                                        <span class="fw-bold text-success">Rs. {{ number_format($product->selling_price, 2) }}</span>
                                        <span class="badge bg-secondary-subtle text-dark">Qty: {{ $product->stock_quantity }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5 text-muted">
                            <i class="bi bi-box-seam fs-1 d-block mb-2"></i>
                            No products available in stock.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Cart & Totals -->
    <div class="col-lg-5">
        <form action="{{ route('pos.checkout') }}" method="POST" id="checkout-form">
            @csrf
            <!-- Hidden Input to transmit Cart data as JSON -->
            <input type="hidden" name="cart_items" id="cart-items-input">

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-secondary"><i class="bi bi-cart3 me-1"></i> Cart</h5>
                </div>
                <div class="card-body p-0">
                    <!-- Cart Table -->
                    <div class="table-responsive cart-container">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Item</th>
                                    <th style="width: 120px;">Qty</th>
                                    <th>Price</th>
                                    <th style="width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody id="cart-table-body">
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="bi bi-basket fs-2 d-block mb-2"></i>
                                        Your cart is empty. Click products on the left to add.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Checkout Calculations -->
                    <div class="p-3 border-top bg-light">
                        <div class="row g-2 mb-2">
                            <div class="col-6 text-muted">Subtotal</div>
                            <div class="col-6 text-end fw-semibold" id="lbl-subtotal">Rs. 0.00</div>
                            <input type="hidden" name="subtotal" id="input-subtotal" value="0.00">
                        </div>

                        <!-- Discount and Tax settings -->
                        <div class="row g-2 align-items-center mb-2">
                            <div class="col-6">
                                <label for="input-discount-pct" class="text-muted col-form-label py-0">Discount (%)</label>
                            </div>
                            <div class="col-6">
                                <input type="number" name="discount_percent" id="input-discount-pct" class="form-control form-control-sm text-end" min="0" max="100" value="0" onchange="calculateTotals()">
                            </div>
                        </div>

                        <div class="row g-2 align-items-center mb-2">
                            <div class="col-6">
                                <label for="input-tax-pct" class="text-muted col-form-label py-0">Tax (%)</label>
                            </div>
                            <div class="col-6">
                                <input type="number" name="tax_percent" id="input-tax-pct" class="form-control form-control-sm text-end" min="0" max="100" value="10" onchange="calculateTotals()">
                            </div>
                        </div>

                        <hr class="my-2">

                        <div class="row g-2 mb-3">
                            <div class="col-6 h5 fw-bold text-dark">Grand Total</div>
                            <div class="col-6 h5 fw-bold text-end text-primary" id="lbl-grand-total">Rs. 0.00</div>
                            <input type="hidden" name="grand_total" id="input-grand-total" value="0.00">
                        </div>

                        <!-- Cash Received & Change Calculations -->
                        <div class="row g-2 align-items-center mb-2">
                            <div class="col-6">
                                <label for="input-cash-received" class="h6 fw-semibold text-secondary mb-0">Cash Received <span class="text-danger">*</span></label>
                            </div>
                            <div class="col-6">
                                <input type="number" step="0.01" name="cash_received" id="input-cash-received" class="form-control text-end fw-bold text-success border-success" min="0" value="0.00" onkeyup="calculateTotals()" onchange="calculateTotals()" required>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6 h6 fw-semibold text-secondary">Change Due</div>
                            <div class="col-6 h6 fw-bold text-end text-danger" id="lbl-change-due">Rs. 0.00</div>
                            <input type="hidden" name="change_returned" id="input-change-due" value="0.00">
                        </div>

                        <button type="submit" class="btn btn-success w-100 py-3 fw-bold fs-5 shadow" id="btn-checkout" disabled>
                            <i class="bi bi-check2-circle me-1"></i> Complete Checkout
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let cart = [];
    let activeCategory = 'all';

    // 1. Add product to cart
    function addToCart(product) {
        // Check if product is already in cart
        let cartItem = cart.find(item => item.id === product.id);

        if (cartItem) {
            if (cartItem.qty >= product.stock_quantity) {
                alert(`Insufficient stock! Only ${product.stock_quantity} unit(s) available.`);
                return;
            }
            cartItem.qty++;
        } else {
            cart.push({
                id: product.id,
                name: product.name,
                sku: product.sku,
                price: product.selling_price,
                stock: product.stock_quantity,
                qty: 1
            });
        }

        renderCart();
    }

    // 2. Render Cart items table
    function renderCart() {
        const tbody = document.getElementById('cart-table-body');
        
        if (cart.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center py-5 text-muted">
                        <i class="bi bi-basket fs-2 d-block mb-2"></i>
                        Your cart is empty. Click products on the left to add.
                    </td>
                </tr>
            `;
            document.getElementById('btn-checkout').disabled = true;
            calculateTotals();
            return;
        }

        tbody.innerHTML = '';
        cart.forEach((item, index) => {
            tbody.innerHTML += `
                <tr data-id="${item.id}">
                    <td class="ps-3">
                        <div class="fw-semibold text-truncate" style="max-width: 150px;">${item.name}</div>
                        <small class="text-muted">Rs. ${item.price.toFixed(2)}</small>
                    </td>
                    <td>
                        <div class="input-group input-group-sm" style="width: 100px;">
                            <button type="button" class="btn btn-outline-secondary" onclick="adjustQty(${item.id}, -1)">-</button>
                            <input type="text" class="form-control text-center p-0" value="${item.qty}" readonly>
                            <button type="button" class="btn btn-outline-secondary" onclick="adjustQty(${item.id}, 1)">+</button>
                        </div>
                    </td>
                    <td class="fw-semibold">Rs. ${(item.price * item.qty).toFixed(2)}</td>
                    <td class="text-end pe-3">
                        <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="removeFromCart(${item.id})">
                            <i class="bi bi-trash fs-5"></i>
                        </button>
                    </td>
                </tr>
            `;
        });

        document.getElementById('btn-checkout').disabled = false;
        calculateTotals();
    }

    // 3. Adjust Quantity (+/- buttons)
    function adjustQty(productId, amount) {
        let item = cart.find(item => item.id === productId);
        if (!item) return;

        let newQty = item.qty + amount;
        if (newQty <= 0) {
            removeFromCart(productId);
            return;
        }

        if (newQty > item.stock) {
            alert(`Cannot exceed available stock of ${item.stock} items.`);
            return;
        }

        item.qty = newQty;
        renderCart();
    }

    // 4. Remove item from cart
    function removeFromCart(productId) {
        cart = cart.filter(item => item.id !== productId);
        renderCart();
    }

    // 5. Calculate subtotal, discount, tax, grand total and change due
    function calculateTotals() {
        let subtotal = 0;
        cart.forEach(item => {
            subtotal += item.price * item.qty;
        });

        // Get percentages
        let discountPct = parseFloat(document.getElementById('input-discount-pct').value) || 0;
        let taxPct = parseFloat(document.getElementById('input-tax-pct').value) || 0;

        // Ensure values are within range
        if (discountPct < 0) discountPct = 0;
        if (discountPct > 100) discountPct = 100;
        if (taxPct < 0) taxPct = 0;
        if (taxPct > 100) taxPct = 100;

        // Apply discount
        let discountAmount = subtotal * (discountPct / 100);
        let discountedSubtotal = subtotal - discountAmount;

        // Apply tax
        let taxAmount = discountedSubtotal * (taxPct / 100);

        // Grand Total
        let grandTotal = discountedSubtotal + taxAmount;

        // Update Labels
        document.getElementById('lbl-subtotal').innerText = `Rs. ${subtotal.toFixed(2)}`;
        document.getElementById('lbl-grand-total').innerText = `Rs. ${grandTotal.toFixed(2)}`;

        // Update Hidden Inputs
        document.getElementById('input-subtotal').value = subtotal.toFixed(2);
        document.getElementById('input-grand-total').value = grandTotal.toFixed(2);
        document.getElementById('cart-items-input').value = JSON.stringify(cart);

        // Calculate Change
        let cashReceived = parseFloat(document.getElementById('input-cash-received').value) || 0;
        let changeDue = cashReceived - grandTotal;

        if (changeDue < 0) {
            document.getElementById('lbl-change-due').innerText = `Rs. 0.00`;
            document.getElementById('lbl-change-due').className = 'col-6 h6 fw-bold text-end text-danger';
            document.getElementById('input-change-due').value = '0.00';
            // Disable complete button if cash received is less than grand total
            if (cart.length > 0) {
                document.getElementById('btn-checkout').disabled = true;
            }
        } else {
            document.getElementById('lbl-change-due').innerText = `Rs. ${changeDue.toFixed(2)}`;
            document.getElementById('lbl-change-due').className = 'col-6 h6 fw-bold text-end text-success';
            document.getElementById('input-change-due').value = changeDue.toFixed(2);
            if (cart.length > 0) {
                document.getElementById('btn-checkout').disabled = false;
            }
        }
    }

    // 6. Filter products based on search input
    function filterProducts() {
        const query = document.getElementById('product-search').value.toLowerCase().trim();
        const items = document.querySelectorAll('.product-item');

        items.forEach(item => {
            const name = item.getAttribute('data-name');
            const sku = item.getAttribute('data-sku');
            const catId = item.getAttribute('data-category');

            const matchesQuery = name.includes(query) || sku.includes(query);
            const matchesCategory = (activeCategory === 'all' || catId === activeCategory);

            if (matchesQuery && matchesCategory) {
                item.style.setProperty('display', 'block', 'important');
            } else {
                item.style.setProperty('display', 'none', 'important');
            }
        });
    }

    // 7. Filter products based on category buttons
    function filterCategory(catId, button) {
        // Toggle active class on pills
        const pills = document.querySelectorAll('#category-pills button');
        pills.forEach(pill => {
            pill.classList.remove('btn-primary', 'active');
            pill.classList.add('btn-outline-secondary');
        });

        button.classList.remove('btn-outline-secondary');
        button.classList.add('btn-primary', 'active');

        activeCategory = catId;
        filterProducts();
    }
</script>
@endsection
