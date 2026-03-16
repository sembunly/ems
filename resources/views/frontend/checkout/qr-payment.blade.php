@extends('layouts.frontend')

@section('title','QR Payment')
@section('hero_title','Scan to Pay')
@section('hero_subtitle','Scan the QR code below to complete your payment')
@section('hero_action')
  <a class="px-4 btn btn-outline-dark pill" href="{{ route('home') }}">
    <i class="bi bi-house me-1"></i> Back to Home
  </a>
@endsection

@section('breadcrumb')
  <nav aria-label="breadcrumb">
    <ol class="mb-0 breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none">Home</a></li>
      <li class="breadcrumb-item"><a href="{{ route('checkout.index') }}" class="text-decoration-none">Checkout</a></li>
      <li class="breadcrumb-item active" aria-current="page">QR Payment</li>
    </ol>
  </nav>
@endsection

@section('content')
<div class="row g-4 justify-content-center">
  {{-- QR Code Card --}}
  <div class="col-lg-5">
    <div class="p-3 text-center card soft-card p-md-4">
      <h5 class="mb-1 fw-bold">Order #{{ $order->id }}</h5>
      <div class="mb-3 text-muted small">Amount: <span class="fw-bold text-dark fs-5">${{ number_format($order->total_amount, 2) }}</span></div>

      <div class="p-3 mx-auto mb-3 border bg-white rounded-4" style="display:inline-block;">
        <div id="qrcode"></div>
      </div>

      <div class="border alert alert-light rounded-4">
        <div class="fw-bold"><i class="bi bi-phone me-1"></i> How to Pay</div>
        <ol class="mt-2 mb-0 text-start text-muted small">
          <li>Open your banking or payment app</li>
          <li>Scan the QR code above</li>
          <li>Payment completes automatically</li>
        </ol>
      </div>

      <div id="statusBox" class="mt-3">
        <div class="d-flex align-items-center justify-content-center gap-2 text-muted">
          <div class="spinner-border spinner-border-sm" role="status"></div>
          <span>Waiting for payment...</span>
        </div>
      </div>
    </div>
  </div>

  {{-- Order Summary --}}
  <div class="col-lg-5">
    <div class="p-3 card soft-card p-md-4">
      <h5 class="mb-3 fw-bold">Order Summary</h5>

      <div class="gap-2 d-grid">
        @foreach($order->orderItems as $item)
          <div class="d-flex justify-content-between">
            <div class="me-2">
              <div class="fw-semibold">{{ $item->product->name ?? 'Product' }}</div>
              <div class="text-muted small">${{ number_format($item->price, 2) }} &times; {{ $item->quantity }}</div>
            </div>
            <div class="fw-bold">${{ number_format($item->price * $item->quantity, 2) }}</div>
          </div>
        @endforeach
      </div>

      <hr>

      <div class="d-flex justify-content-between">
        <span class="fw-bold">Total</span>
        <span class="fw-bold fs-5">${{ number_format($order->total_amount, 2) }}</span>
      </div>

      <hr>

      <div class="mb-2 fw-bold">Customer</div>
      <div class="text-muted small">
        <div>{{ $order->full_name }}</div>
        <div>{{ $order->phone }}</div>
        <div>{{ $order->address }}</div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
{{-- QRCode.js library --}}
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
  // Generate QR code pointing to the confirm URL
  const confirmUrl = @json(route('checkout.qr.confirm', $order->payment_token));

  new QRCode(document.getElementById('qrcode'), {
    text: confirmUrl,
    width: 220,
    height: 220,
    colorDark: '#111827',
    colorLight: '#ffffff',
    correctLevel: QRCode.CorrectLevel.H,
  });

  // Poll for payment completion every 3 seconds
  function checkPaymentStatus() {
    fetch(@json(route('checkout.qr.status', $order->id)), {
      headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
      if (data.paid) {
        document.getElementById('statusBox').innerHTML =
          '<div class="alert alert-success rounded-4 fw-bold">' +
          '<i class="bi bi-check-circle me-1"></i> Payment received! Redirecting...</div>';
        setTimeout(() => {
          window.location.href = @json(route('checkout.success', $order->id));
        }, 1500);
      } else {
        setTimeout(checkPaymentStatus, 3000);
      }
    })
    .catch(() => setTimeout(checkPaymentStatus, 5000));
  }

  checkPaymentStatus();
</script>
@endpush
