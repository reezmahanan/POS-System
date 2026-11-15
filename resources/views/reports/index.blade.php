@extends('layouts.admin')

@section('title', 'POS - Reports')
@section('page_title', 'Reports & Analytics')

@section('content')
<!-- Sales Performance Overview Cards -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card border-0 shadow-sm bg-white py-3 px-4">
            <div class="d-flex align-items-center">
                <div class="bg-success-subtle p-3 rounded-circle text-success me-3">
                    <i class="bi bi-calendar-event fs-3"></i>
                </div>
                <div>
                    <span class="text-muted d-block"><small>Today's Sales Revenue</small></span>
                    <h3 class="mb-0 fw-bold">Rs. {{ number_format($dailySales, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="card border-0 shadow-sm bg-white py-3 px-4">
            <div class="d-flex align-items-center">
                <div class="bg-primary-subtle p-3 rounded-circle text-primary me-3">
                    <i class="bi bi-calendar-range fs-3"></i>
                </div>
                <div>
                    <span class="text-muted d-block"><small>Weekly Sales Revenue (This Week)</small></span>
                    <h3 class="mb-0 fw-bold">Rs. {{ number_format($weeklySales, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card border-0 shadow-sm bg-white py-3 px-4">
            <div class="d-flex align-items-center">
                <div class="bg-info-subtle p-3 rounded-circle text-info me-3">
                    <i class="bi bi-calendar-month fs-3"></i>
                </div>
                <div>
                    <span class="text-muted d-block"><small>Monthly Sales Revenue (This Month)</small></span>
                    <h3 class="mb-0 fw-bold">Rs. {{ number_format($monthlySales, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs Container -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <!-- Bootstrap Tab Navs -->
        <ul class="nav nav-tabs card-header-tabs" id="reportTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-semibold" id="bestsellers-tab" data-bs-toggle="tab" data-bs-target="#bestsellers" type="button" role="tab" aria-controls="bestsellers" aria-selected="true">
                    <i class="bi bi-star-fill me-1 text-warning"></i> Top Selling Products
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="valuation-tab" data-bs-toggle="tab" data-bs-target="#valuation" type="button" role="tab" aria-controls="valuation" aria-selected="false">
                    <i class="bi bi-currency-dollar me-1 text-success"></i> Stock Valuation
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="exports-tab" data-bs-toggle="tab" data-bs-target="#exports" type="button" role="tab" aria-controls="exports" aria-selected="false">
                    <i class="bi bi-download me-1 text-primary"></i> Export Registers
                </button>
            </li>
        </ul>
    </div>
    
    <div class="card-body">
        <div class="tab-content" id="reportTabsContent">
            
            <!-- Tab 1: Bestsellers -->
            <div class="tab-pane fade show active" id="bestsellers" role="tabpanel" aria-labelledby="bestsellers-tab">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h6 class="fw-bold mb-0 text-secondary">Top-Selling Products (Bestsellers)</h6>
                    <a href="{{ route('reports.export.products') }}" class="btn btn-sm btn-outline-success">
                        <i class="bi bi-file-earmark-excel me-1"></i> Export Bestsellers CSV
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Product</th>
                                <th>SKU</th>
                                <th>Category</th>
                                <th class="text-center">Quantity Sold</th>
                                <th class="text-end pe-3">Revenue Generated</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topSellingProducts as $item)
                                <tr>
                                    <td class="ps-3 fw-semibold">{{ $item->product->name ?? 'N/A' }}</td>
                                    <td><code>{{ $item->product->sku ?? 'N/A' }}</code></td>
                                    <td>{{ $item->product->category->name ?? 'N/A' }}</td>
                                    <td class="text-center fw-bold">{{ $item->total_qty }}</td>
                                    <td class="text-end pe-3 text-success fw-bold">Rs. {{ number_format($item->total_revenue, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No product sales recorded yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab 2: Stock Valuation -->
            <div class="tab-pane fade" id="valuation" role="tabpanel" aria-labelledby="valuation-tab">
                <!-- Valuation Stats Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="border rounded p-3 bg-light text-center">
                            <span class="text-muted d-block"><small>Total Stock Cost Value</small></span>
                            <h4 class="mb-0 fw-bold text-dark">Rs. {{ number_format($totalCostValuation, 2) }}</h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 bg-light text-center">
                            <span class="text-muted d-block"><small>Total Stock Retail Value</small></span>
                            <h4 class="mb-0 fw-bold text-success">Rs. {{ number_format($totalRetailValuation, 2) }}</h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 bg-light text-center">
                            <span class="text-muted d-block"><small>Estimated Profit Margin</small></span>
                            <h4 class="mb-0 fw-bold text-primary">Rs. {{ number_format($expectedProfitValuation, 2) }}</h4>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h6 class="fw-bold mb-0 text-secondary">Inventory Value Status</h6>
                    <a href="{{ route('reports.export.stock') }}" class="btn btn-sm btn-outline-success">
                        <i class="bi bi-file-earmark-excel me-1"></i> Export Stock Value CSV
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Product</th>
                                <th>Category</th>
                                <th class="text-end">Unit Cost</th>
                                <th class="text-end">Retail Price</th>
                                <th class="text-center">Stock</th>
                                <th class="text-end">Total Cost</th>
                                <th class="text-end pe-3">Total Retail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                @php
                                    $costSum = $product->purchase_price * $product->stock_quantity;
                                    $retailSum = $product->selling_price * $product->stock_quantity;
                                @endphp
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-semibold">{{ $product->name }}</div>
                                        <small class="text-muted">SKU: {{ $product->sku }}</small>
                                    </td>
                                    <td>{{ $product->category->name }}</td>
                                    <td class="text-end">Rs. {{ number_format($product->purchase_price, 2) }}</td>
                                    <td class="text-end text-success">Rs. {{ number_format($product->selling_price, 2) }}</td>
                                    <td class="text-center">
                                        <span class="badge {{ $product->stock_quantity <= $product->low_stock_alert ? 'bg-danger' : 'bg-secondary' }}">
                                            {{ $product->stock_quantity }}
                                        </span>
                                    </td>
                                    <td class="text-end">Rs. {{ number_format($costSum, 2) }}</td>
                                    <td class="text-end pe-3 fw-bold text-primary">Rs. {{ number_format($retailSum, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No products registered.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab 3: Exports -->
            <div class="tab-pane fade" id="exports" role="tabpanel" aria-labelledby="exports-tab">
                <h6 class="fw-bold mb-4 text-secondary">Download System Excel/CSV Spreadsheets</h6>
                <div class="row g-3">
                    <!-- Export Sales Log Card -->
                    <div class="col-md-6">
                        <div class="card border border-light-subtle h-100 p-3 shadow-sm">
                            <div class="card-body text-center">
                                <i class="bi bi-wallet2 text-primary fs-1 mb-3"></i>
                                <h5 class="card-title fw-bold">Transactions Register</h5>
                                <p class="card-text text-muted mb-4">Downloads a full history log of every sale transaction including cashier, totals, tax, and discount parameters.</p>
                                <a href="{{ route('reports.export.sales') }}" class="btn btn-primary w-100 py-2">
                                    <i class="bi bi-download me-1"></i> Download Sales CSV
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Export Inventory Stock Value Card -->
                    <div class="col-md-6">
                        <div class="card border border-light-subtle h-100 p-3 shadow-sm">
                            <div class="card-body text-center">
                                <i class="bi bi-box-seam text-success fs-1 mb-3"></i>
                                <h5 class="card-title fw-bold">Bestsellers Metrics</h5>
                                <p class="card-text text-muted mb-4">Downloads a report of items ranked by popularity, listing quantities sold and total revenues generated.</p>
                                <a href="{{ route('reports.export.products') }}" class="btn btn-success w-100 py-2">
                                    <i class="bi bi-download me-1"></i> Download Bestsellers CSV
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
