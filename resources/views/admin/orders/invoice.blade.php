<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Invoice {{ $order->name }}</title>
<style>
  body { font-family: sans-serif; font-size: 13px; color: #333; }
  h1 { font-size: 20px; }
  .label { color: #666; }
  table { width: 100%; border-collapse: collapse; }
  th, td { padding: 6px 8px; border-bottom: 1px solid #eee; }
  th { background: #f5f5f5; }
  .total-row td { font-weight: bold; border-top: 2px solid #333; }
</style>
</head>
<body>
<h1>Invoice — {{ $order->name }}</h1>
<p class="label">Date: {{ $order->created_at->format('F j, Y') }}</p>
<p class="label">Status: {{ ucfirst($order->financial_status) }}</p>

@if($order->customer)
<p><strong>Customer:</strong> {{ $order->customer->full_name }} ({{ $order->customer->email }})</p>
@endif

@if($order->shipping_address)
<p><strong>Ship to:</strong>
  {{ $order->shipping_address['address1'] ?? '' }},
  {{ $order->shipping_address['city'] ?? '' }},
  {{ $order->shipping_address['country'] ?? '' }}
</p>
@endif

<table>
  <thead>
    <tr><th>Item</th><th>SKU</th><th>Qty</th><th>Price</th><th>Total</th></tr>
  </thead>
  <tbody>
    @foreach($order->lineItems as $item)
    <tr>
      <td>{{ $item->title }} — {{ $item->variant_title }}</td>
      <td>{{ $item->sku }}</td>
      <td>{{ $item->quantity }}</td>
      <td>${{ number_format($item->price, 2) }}</td>
      <td>${{ number_format($item->price * $item->quantity, 2) }}</td>
    </tr>
    @endforeach
    <tr><td colspan="4" class="label text-right">Subtotal</td><td>${{ number_format($order->subtotal_price, 2) }}</td></tr>
    <tr><td colspan="4" class="label text-right">Shipping</td><td>${{ number_format($order->total_shipping, 2) }}</td></tr>
    <tr><td colspan="4" class="label text-right">Tax</td><td>${{ number_format($order->total_tax, 2) }}</td></tr>
    <tr class="total-row"><td colspan="4">Total</td><td>${{ number_format($order->total_price, 2) }}</td></tr>
  </tbody>
</table>
</body>
</html>
