@extends('layouts.admin')

@section('title', 'POS - Dashboard')
@section('page_title', 'Dashboard')

@section('content')
<!-- Dashboard Stat Cards -->
<div class="row mb-4">
    <!-- Total Sales Card -->
    <div class="col-md-3 mb-3">
        <div class="card card-stat bg-primary text-white h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-white-50 text-uppercase mb-1">Total Sales</h6>
                    <h3 class="mb-0 fw-bold">${{ number_format($totalSales, 2) }}</h3>
                </div>
                <div class="fs-1 text-white-50">
                    <i class="bi bi-wallet2"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Sales Card -->
    <div class="col-md-3 mb-3">
        <div class="card card-stat bg-success text-white h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-white-50 text-uppercase mb-1">Today's Sales</h6>
                    <h3 class="mb-0 fw-bold">${{ number_format($todaySales, 2) }}</h3>
                </div>
                <div class="fs-1 text-white-50">
                    <i class="bi bi-graph-up"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Products Card -->
    <div class="col-md-3 mb-3">
        <div class="card card-stat bg-info text-white h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-white-50 text-uppercase mb-1">Total Products</h6>
                    <h3 class="mb-0 fw-bold">{{ $totalProducts }}</h3>
                </div>
                <div class="fs-1 text-white-50">
                    <i class="bi bi-box-seam"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Low-Stock Alerts Card -->
    <div class="col-md-3 mb-3">
        <div class="card card-stat {{ $lowStockProductsCount > 0 ? 'bg-danger' : 'bg-secondary' }} text-white h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-white-50 text-uppercase mb-1">Low-Stock Alert</h6>
                    <h3 class="mb-0 fw-bold">{{ $lowStockProductsCount }}</h3>
                </div>
                <div class="fs-1 text-white-50">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Transactions -->
    <div class="col-lg-7 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between">
                <h5 class="mb-0 fw-bold text-secondary">Recent Orders</h5>
                <a href="{{ route('sales.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Invoice No</th>
                                <th>Cashier</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentSales as $sale)
                                <tr>
                                    <td class="ps-3 fw-semibold text-primary">#{{ $sale->invoice_no }}</td>
                                    <td>{{ $sale->user->name }}</td>
                                    <td class="fw-bold">${{ number_format($sale->grand_total, 2) }}</td>
                                    <td>
                                        @if($sale->status === 'completed')
                                            <span class="badge bg-success-subtle text-success">Completed</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger">Refunded</span>
                                        @endif
                                    </td>
                                    <td>{{ $sale->created_at->format('M d, Y h:i A') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No sales completed yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock Inventory Warnings -->
    <div class="col-lg-5 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between">
                <h5 class="mb-0 fw-bold text-secondary">Low Stock Warnings</h5>
                <span class="badge bg-danger">{{ $lowStockProductsCount }} Warning(s)</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Product</th>
                                <th>Stock</th>
                                <th>Min. Level</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lowStockProducts as $product)
                                <tr class="table-danger-subtle">
                                    <td class="ps-3">
                                        <div class="fw-semibold">{{ $product->name }}</div>
                                        <small class="text-muted">SKU: {{ $product->sku }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-danger text-white">{{ $product->stock_quantity }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary text-white">{{ $product->low_stock_alert }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-success fw-semibold">
                                        <i class="bi bi-shield-check me-2"></i>All product stock is healthy!
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
