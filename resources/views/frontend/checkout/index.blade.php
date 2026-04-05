@extends('layouts.frontend')


@section('title', 'Checkout')
@section('hero_title', 'Checkout')
@section('hero_subtitle', 'Enter address, choose payment, confirm order')
@section('hero_action')
  <a class="px-4 btn btn-outline-dark pill" href="{{ route('cart.index') }}">
    <i class="bi bi-arrow-left me-1"></i> Back to Cart
  </a>
@endsection

@section('breadcrumb')
  <nav aria-label="breadcrumb">
    <ol class="mb-0 breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none">Home</a></li>
      <li class="breadcrumb-item"><a href="{{ route('cart.index') }}" class="text-decoration-none">Cart</a></li>
      <li class="breadcrumb-item active" aria-current="page">Checkout</li>
    </ol>
  </nav>
@endsection

@section('content')

  @php
    $cartItems = $cart ?? [];
    $subtotal = 0;
    foreach ($cartItems as $it) {
      $subtotal += ((float) $it['price']) * ((int) $it['qty']);
    }
  @endphp

  @if(count($cartItems) === 0)
    <div class="p-4 border alert alert-light rounded-4">
      <div class="fw-bold">Cart is empty.</div>
      <div class="text-muted">Please add products before checkout.</div>
      <a href="{{ route('home') }}" class="mt-3 btn btn-dark pill">Go Home</a>
    </div>
  @else

    <form method="POST" action="{{ route('checkout.store') }}" enctype="multipart/form-data">
      @csrf

      <div class="row g-3 g-lg-4">
        {{-- Left: Address + Payment --}}
        <div class="col-lg-7">
          <div class="p-3 mb-3 card soft-card p-md-4">
            <h5 class="mb-3 fw-bold">Delivery Address</h5>

            <div class="row g-2">
              <div class="col-md-6">
                <label class="mb-1 form-label small text-muted">Full Name</label>
                <input name="full_name" value="{{ old('full_name') }}" class="form-control pill" required>
                @error('full_name')<div class="mt-1 text-danger small">{{ $message }}</div>@enderror
              </div>

              <div class="col-md-6">
                <label class="mb-1 form-label small text-muted">Phone</label>
                <input name="phone" value="{{ old('phone') }}" class="form-control pill" required>
                @error('phone')<div class="mt-1 text-danger small">{{ $message }}</div>@enderror
              </div>

              <div class="col-12">
                <label class="mb-1 form-label small text-muted">Address</label>
                <textarea name="address" class="form-control" placeholder="House No, Street, Commune, City..."
                  required>{{ old('address') }}</textarea>
                @error('address')<div class="mt-1 text-danger small">{{ $message }}</div>@enderror
              </div>

              <div class="col-12">
                <label class="mb-1 form-label small text-muted">Note (Optional)</label>
                <input name="note" value="{{ old('note') }}" class="form-control pill" placeholder="Leave at door...">
                @error('note')<div class="mt-1 text-danger small">{{ $message }}</div>@enderror
              </div>
            </div>
          </div>

          <div class="p-3 card soft-card p-md-4">
            <h5 class="mb-3 fw-bold">Payment</h5>

            <div class="flex-wrap gap-2 d-flex">
              <label class="btn btn-outline-dark pill">
                <input type="radio" class="form-check-input me-2" name="payment_method" value="cod"
                  @checked(old('payment_method', 'cod') == 'cod')>
                Cash on Delivery (COD)
              </label>

              <label class="btn btn-outline-dark pill">
                <input type="radio" class="form-check-input me-2" name="payment_method" value="qr"
                  @checked(old('payment_method') == 'qr')>
                Pay by QR
              </label>
            </div>
            @error('payment_method')<div class="mt-2 text-danger small">{{ $message }}</div>@enderror

            {{-- QR extra fields --}}
            <div id="qrBox" class="mt-3" style="display:none;">
              <div class="border alert alert-light rounded-4">
                <div class="mb-2 fw-bold"><i class="bi bi-qr-code me-1"></i> QR Payment</div>
                <div class="text-muted small">After placing the order, you will be redirected to a page with a QR code. Scan
                  it to complete your payment instantly.</div>
              </div>
            </div>

          </div>
        </div>

        {{-- Right: Order summary --}}
        <div class="col-lg-5">
          <div class="p-3 card soft-card p-md-4">
            <h5 class="mb-3 fw-bold">Confirm Order</h5>

            <div class="mb-2 small text-muted">Items</div>

            <div class="gap-2 d-grid">
              @foreach($cartItems as $pid => $it)
                <div class="d-flex justify-content-between">
                  <div class="me-2">
                    <div class="fw-semibold">{{ $it['name'] }}</div>
                    <div class="text-muted small">${{ number_format($it['price'], 2) }} × {{ (int) $it['qty'] }}</div>
                  </div>
                  <div class="fw-bold">
                    ${{ number_format(((float) $it['price']) * ((int) $it['qty']), 2) }}
                  </div>
                </div>
              @endforeach
            </div>

            <hr>

            @php $shipping = 0;
            $total = $subtotal + $shipping; @endphp

            <div class="d-flex justify-content-between text-muted">
              <span>Subtotal</span>
              <span>${{ number_format($subtotal, 2) }}</span>
            </div>
            <div class="mt-2 d-flex justify-content-between text-muted">
              <span>Shipping</span>
              <span>${{ number_format($shipping, 2) }}</span>
            </div>

            <hr>

            <div class="d-flex justify-content-between">
              <span class="fw-bold">Total</span>
              <span class="fw-bold fs-5">${{ number_format($total, 2) }}</span>
            </div>

            <button class="py-2 mt-3 btn btn-dark pill w-100">
              <i class="bi bi-check2-circle me-1"></i> Place Order
            </button>

            <div class="mt-3 text-muted small">
              <i class="bi bi-shield-check me-1"></i> Your info is protected.
            </div>
          </div>
        </div>
      </div>
    </form>

  @endif
@endsection

@push('scripts')
  <script>
    function toggleQR() {
      const method = document.querySelector('input[name="payment_method"]:checked')?.value;
      const box = document.getElementById('qrBox');
      box.style.display = (method === 'qr') ? 'block' : 'none';
    }
    document.querySelectorAll('input[name="payment_method"]').forEach(r => r.addEventListener('change', toggleQR));
    toggleQR();
  </script>
@endpush