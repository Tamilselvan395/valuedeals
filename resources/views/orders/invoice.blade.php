<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $order->order_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #1a1a1a; background: #fff; }
        .container { max-width: 750px; margin: 0 auto; padding: 40px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #feee00; padding-bottom: 20px; margin-bottom: 30px; }
        .store-name { font-size: 24px; font-weight: 900; color: #111; letter-spacing: -0.5px; }
        .store-tagline { font-size: 11px; color: #666; margin-top: 3px; }
        .invoice-label { font-size: 28px; font-weight: 900; color: #111; text-align: right; }
        .invoice-meta { text-align: right; color: #555; font-size: 11px; margin-top: 5px; }
        .section { margin-bottom: 25px; }
        .section-grid { display: flex; gap: 30px; }
        .section-col { flex: 1; }
        .section-title { font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #666; margin-bottom: 8px; }
        .section-value { font-size: 12px; color: #1a1a1a; line-height: 1.8; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        thead tr { background: #feee00; }
        thead th { padding: 10px 12px; text-align: left; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: #111; }
        thead th:last-child { text-align: right; }
        tbody tr { border-bottom: 1px solid #f0f0f0; }
        tbody td { padding: 10px 12px; font-size: 12px; color: #333; }
        tbody td:last-child { text-align: right; font-weight: 600; }
        .totals { width: 260px; margin-left: auto; }
        .totals-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #f0f0f0; font-size: 12px; color: #555; }
        .totals-row.grand { border-top: 2px solid #feee00; border-bottom: none; padding-top: 10px; font-size: 15px; font-weight: 900; color: #111; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 50px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }
        .badge-cod { background: #feee00; color: #111; }
        .badge-stripe { background: #ede9fe; color: #5b21b6; }
        .footer { margin-top: 40px; border-top: 1px solid #e5e5e5; padding-top: 20px; text-align: center; color: #aaa; font-size: 11px; }
        .thank-you { text-align: center; background: #fafafa; border-radius: 8px; border: 1px solid #eee; border-top: 3px solid #feee00; padding: 15px; margin-bottom: 20px; }
        .thank-you p { font-size: 14px; font-weight: 800; color: #111; }
        .thank-you span { font-size: 11px; color: #555; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div>
            @php
                $storeSettings = \App\Models\StoreSetting::first();
                $base64Logo = null;
                if($storeSettings && $storeSettings->logo_path) {
                    $path = public_path('storage/' . $storeSettings->logo_path);
                    if (file_exists($path)) {
                        $type = pathinfo($path, PATHINFO_EXTENSION);
                        $data = file_get_contents($path);
                        $base64Logo = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    }
                }
            @endphp
            @if($base64Logo)
                <img src="{{ $base64Logo }}" alt="Logo" style="height: 38px; object-fit: contain; margin-bottom: 8px;">
            @else
                <div class="store-name">value<span style="color:#9ca3af;">deals</span></div>
            @endif
            <div class="store-tagline">{{ config('bookstore.store_address') }}</div>
            <div class="store-tagline">{{ config('bookstore.store_email') }} | {{ config('bookstore.store_phone') }}</div>
        </div>
        <div>
            <div class="invoice-label">INVOICE</div>
            <div class="invoice-meta">
                <div>#{{ $order->order_number }}</div>
                <div>Date: {{ $order->created_at->format('d M Y') }}</div>
            </div>
        </div>
    </div>
    <div class="section section-grid">
        <div class="section-col">
            <div class="section-title">Bill To</div>
            <div class="section-value">
                <strong>{{ $order->shipping_name }}</strong><br>
                {{ $order->shipping_email }}<br>{{ $order->shipping_phone }}
            </div>
        </div>
        <div class="section-col">
            <div class="section-title">Ship To</div>
            <div class="section-value">
                {{ $order->shipping_address }}<br>
                {{ $order->shipping_city }}@if($order->shipping_state), {{ $order->shipping_state }}@endif<br>
                {{ $order->shipping_pincode }}, {{ $order->shipping_country }}
            </div>
        </div>
        <div class="section-col" style="text-align:right;">
            <div class="section-title">Order Details</div>
            <div class="section-value">
                <span class="badge {{ $order->payment_method === 'stripe' ? 'badge-stripe' : 'badge-cod' }}">
                    {{ $order->payment_method === 'stripe' ? 'STRIPE' : 'COD' }}
                </span><br><br>
                Status: <strong>{{ ucfirst($order->status) }}</strong>
            </div>
        </div>
    </div>
    <table>
        <thead>
            <tr>
                <th style="width:40%">Book Title</th>
                <th style="text-align:center">Qty</th>
                <th style="text-align:right">Unit Price</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product_title }}</td>
                <td style="text-align:center">{{ $item->quantity }}</td>
                <td style="text-align:right">{{ config('bookstore.currency_symbol') }}{{ number_format($item->unit_price, 2) }}</td>
                <td>{{ config('bookstore.currency_symbol') }}{{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="totals">
        <div class="totals-row"><span>Subtotal</span><span>{{ config('bookstore.currency_symbol') }}{{ number_format($order->subtotal, 2) }}</span></div>
        @if((float) $order->discount_amount > 0)
        <div class="totals-row"><span>Discount @if($order->coupon_code)({{ $order->coupon_code }})@endif</span><span>−{{ config('bookstore.currency_symbol') }}{{ number_format($order->discount_amount, 2) }}</span></div>
        @endif
        <div class="totals-row"><span>Shipping</span><span>{{ $order->shipping_cost == 0 ? 'FREE' : config('bookstore.currency_symbol') . number_format($order->shipping_cost, 2) }}</span></div>
        <div class="totals-row grand"><span>Total Amount</span><span>{{ config('bookstore.currency_symbol') }}{{ number_format($order->total, 2) }}</span></div>
    </div>
    @if($order->notes)
    <div class="section" style="margin-top:20px;">
        <div class="section-title">Notes</div>
        <div class="section-value">{{ $order->notes }}</div>
    </div>
    @endif
    <div class="thank-you">
        <p>Thank you for shopping with us! 📚</p>
        <span>Questions? Contact {{ config('bookstore.store_email') }}</span>
    </div>
    <div class="footer">
        <p>{{ config('bookstore.store_name') }} · {{ config('bookstore.store_address') }}</p>
        <p style="margin-top:5px;">This is a computer-generated invoice and does not require a signature.</p>
    </div>
</div>
</body>
</html>
