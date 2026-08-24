<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #{{ $sale->invoice_no }}</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Courier New', Courier, monospace;
            padding: 20px;
        }
        .receipt-card {
            max-width: 380px;
            margin: 0 auto;
            background-color: #fff;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border-radius: 8px;
        }
        .dashed-line {
            border-top: 1px dashed #212529;
            margin: 15px 0;
        }
        @media print {
            body {
                background-color: #fff;
                padding: 0;
            }
            .receipt-card {
                box-shadow: none;
                max-width: 100%;
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>

<div class="receipt-card">
    <!-- Header -->
    <div class="text-center">
        <h4 class="fw-bold mb-1">POS RETAIL SHOP</h4>
        <p class="text-muted mb-0"><small>123 Main Street, Cityville</small></p>
        <p class="text-muted"><small>Tel: (555) 019-2834</small></p>
    </div>

    <div class="dashed-line"></div>

    <!-- Metadata -->
    <div>
        <p class="mb-1"><small><strong>Invoice:</strong> #{{ $sale->invoice_no }}</small></p>
        <p class="mb-1"><small><strong>Date:</strong> {{ $sale->created_at->format('Y-m-d H:i:s') }}</small></p>
        <p class="mb-0"><small><strong>Cashier:</strong> {{ $sale->user->name }}</small></p>
    </div>

    <div class="dashed-line"></div>

    <!-- Products Table -->
    <table class="table table-borderless table-sm mb-0">
        <thead>
            <tr class="border-bottom">
                <th><small>Item</small></th>
                <th class="text-center" style="width: 50px;"><small>Qty</small></th>
                <th class="text-end" style="width: 80px;"><small>Price</small></th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $item)
                <tr>
                    <td>
                        <small>{{ $item->product->name }}</small>
                    </td>
                    <td class="text-center">
                        <small>{{ $item->quantity }}</small>
                    </td>
                    <td class="text-end">
                        <small>${{ number_format($item->total, 2) }}</small>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="dashed-line"></div>

    <!-- Calculations Summary -->
    <div class="row g-1">
        <div class="col-7"><small>Subtotal</small></div>
        <div class="col-5 text-end"><small>${{ number_format($sale->subtotal, 2) }}</small></div>

        @if($sale->discount > 0)
            <div class="col-7"><small>Discount</small></div>
            <div class="col-5 text-end text-danger"><small>-${{ number_format($sale->discount, 2) }}</small></div>
        @endif

        @if($sale->tax > 0)
            <div class="col-7"><small>Tax</small></div>
            <div class="col-5 text-end"><small>${{ number_format($sale->tax, 2) }}</small></div>
        @endif

        <div class="col-7 h6 fw-bold mb-0 mt-2">Grand Total</div>
        <div class="col-5 text-end h6 fw-bold mb-0 mt-2">${{ number_format($sale->grand_total, 2) }}</div>

        <div class="col-7 mt-2"><small>Cash Received</small></div>
        <div class="col-5 text-end mt-2"><small>${{ number_format($sale->cash_received, 2) }}</small></div>

        <div class="col-7"><small>Change Returned</small></div>
        <div class="col-5 text-end text-success"><small>${{ number_format($sale->change_returned, 2) }}</small></div>
    </div>

    <div class="dashed-line"></div>

    <!-- Footer message -->
    <div class="text-center">
        <p class="mb-0 fw-semibold"><small>THANK YOU FOR YOUR VISIT</small></p>
        <p class="text-muted"><small>Please keep receipt for refunds/returns</small></p>
    </div>

    <!-- Print Navigation -->
    <div class="no-print mt-4 pt-3 border-top text-center d-flex gap-2 justify-content-center">
        <button class="btn btn-primary btn-sm" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
        <a href="{{ route('pos.index') }}" class="btn btn-secondary btn-sm">Back to POS</a>
    </div>
</div>

<!-- Auto trigger print preview -->
<script>
    window.onload = function() {
        // Trigger print after page loads completely
        setTimeout(function() {
            window.print();
        }, 300);
    }
</script>
</body>
</html>
