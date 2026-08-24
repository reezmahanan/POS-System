@extends('layouts.admin')

@section('title', 'POS - Order Details')
@section('page_title', 'Order Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-9">
        <div class="mb-3">
            <a href="{{ route('sales.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to Logs</a>
        </div>
        
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                <h5 class="mb-0 fw-bold text-secondary">Invoice #{{ $sale->invoice_no }}</h5>
                <span class="badge {{ $sale->status === 'completed' ? 'bg-success' : 'bg-danger' }} text-white text-uppercase">
                    {{ $sale->status }}
                </span>
            </div>
            
            <div class="card-body">
                <!-- Metadata Grid -->
                <div class="row mb-4 p-3 bg-light rounded">
                    <div class="col-md-4">
                        <small class="text-muted d-block">Cashier</small>
                        <strong>{{ $sale->user->name }}</strong>
                        <span class="text-muted">({{ $sale->user->email }})</span>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">Transaction Date</small>
                        <strong>{{ $sale->created_at->format('M d, Y h:i A') }}</strong>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <small class="text-muted d-block">Payment Method</small>
                        <span class="badge bg-primary text-white">Cash Payment</span>
                    </div>
                </div>

                <!-- Products Table -->
                <h6 class="fw-bold mb-3 text-secondary">Items Sold</h6>
                <div class="table-responsive mb-4">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Product Name</th>
                                <th class="text-center" style="width: 100px;">Qty</th>
                                <th class="text-end" style="width: 120px;">Unit Price</th>
                                <th class="text-end pe-3" style="width: 150px;">Total Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sale->items as $item)
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-semibold">{{ $item->product->name }}</div>
                                        <small class="text-muted">SKU: {{ $item->product->sku }}</small>
                                    </td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end">${{ number_format($item->selling_price, 2) }}</td>
                                    <td class="text-end pe-3 fw-semibold">${{ number_format($item->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Totals Alignment -->
                <div class="row justify-content-end">
                    <div class="col-md-5">
                        <div class="card border-0 bg-light p-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal:</span>
                                <span class="fw-semibold">${{ number_format($sale->subtotal, 2) }}</span>
                            </div>
                            
                            @if($sale->discount > 0)
                                <div class="d-flex justify-content-between mb-2 text-danger">
                                    <span>Discount:</span>
                                    <span>-${{ number_format($sale->discount, 2) }}</span>
                                </div>
                            @endif

                            @if($sale->tax > 0)
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Tax:</span>
                                    <span>${{ number_format($sale->tax, 2) }}</span>
                                </div>
                            @endif

                            <hr class="my-2">

                            <div class="d-flex justify-content-between mb-2">
                                <strong class="fs-5 text-dark">Grand Total:</strong>
                                <strong class="fs-5 text-primary">${{ number_format($sale->grand_total, 2) }}</strong>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Cash Received:</span>
                                <span>${{ number_format($sale->cash_received, 2) }}</span>
                            </div>

                            <div class="d-flex justify-content-between mb-0 text-success fw-semibold">
                                <span>Change Due:</span>
                                <span>${{ number_format($sale->change_returned, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action buttons -->
                <div class="mt-4 pt-3 border-top d-flex gap-2">
                    <a href="{{ route('pos.receipt', $sale->id) }}" target="_blank" class="btn btn-primary">
                        <i class="bi bi-printer me-1"></i> Print Receipt
                    </a>
                    
                    @if(Auth::user()->role === 'admin' && $sale->status === 'completed')
                        <form action="{{ route('sales.refund', $sale->id) }}" method="POST" class="ms-auto" onsubmit="return confirm('Are you sure you want to refund this transaction? Stock will be added back.');">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Refund Transaction
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
