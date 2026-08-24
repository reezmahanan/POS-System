@extends('layouts.admin')

@section('title', 'POS - Sales History')
@section('page_title', 'Sales History')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold text-secondary">Sales Log</h5>
            </div>
            
            <div class="card-body">
                <!-- Filters -->
                <form action="{{ route('sales.index') }}" method="GET" class="row g-3 mb-4">
                    <!-- Search invoice -->
                    <div class="col-md-4">
                        <label class="form-label text-muted"><small>Search Invoice</small></label>
                        <input type="text" name="search" class="form-control" placeholder="Search Invoice No..." value="{{ $search }}">
                    </div>
                    <!-- Start Date -->
                    <div class="col-md-3">
                        <label class="form-label text-muted"><small>Start Date</small></label>
                        <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                    </div>
                    <!-- End Date -->
                    <div class="col-md-3">
                        <label class="form-label text-muted"><small>End Date</small></label>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                    </div>
                    <!-- Buttons -->
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-secondary w-100"><i class="bi bi-search me-1"></i> Filter</button>
                    </div>
                    @if($search || $startDate || $endDate)
                        <div class="col-12 mt-2">
                            <a href="{{ route('sales.index') }}" class="btn btn-sm btn-outline-danger"><i class="bi bi-x-circle me-1"></i> Clear Filters</a>
                        </div>
                    @endif
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Invoice No</th>
                                <th>Cashier</th>
                                <th>Subtotal</th>
                                <th>Discount</th>
                                <th>Tax</th>
                                <th>Grand Total</th>
                                <th>Status</th>
                                <th>Date & Time</th>
                                <th style="width: 220px;" class="text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sales as $sale)
                                <tr>
                                    <td class="ps-3 fw-semibold text-primary">#{{ $sale->invoice_no }}</td>
                                    <td>{{ $sale->user->name }}</td>
                                    <td>${{ number_format($sale->subtotal, 2) }}</td>
                                    <td class="text-danger">-${{ number_format($sale->discount, 2) }}</td>
                                    <td>${{ number_format($sale->tax, 2) }}</td>
                                    <td class="fw-bold text-success">${{ number_format($sale->grand_total, 2) }}</td>
                                    <td>
                                        @if($sale->status === 'completed')
                                            <span class="badge bg-success-subtle text-success">Completed</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger">Refunded</span>
                                        @endif
                                    </td>
                                    <td>{{ $sale->created_at->format('M d, Y h:i A') }}</td>
                                    <td class="text-end pe-3">
                                        <!-- View Details -->
                                        <a href="{{ route('sales.show', $sale->id) }}" class="btn btn-sm btn-outline-secondary me-2">
                                            <i class="bi bi-eye"></i> Details
                                        </a>

                                        @if(Auth::user()->role === 'admin' && $sale->status === 'completed')
                                            <!-- Refund button (Admin only) -->
                                            <form action="{{ route('sales.refund', $sale->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to refund this sale? Inventory quantities will be restored.');">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-arrow-counterclockwise"></i> Refund
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">No sales orders found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $sales->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
