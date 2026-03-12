@extends('layouts.frontend')

@section('title', 'Your Cart')
@section('hero_title', 'Your Shopping Cart')
@section('hero_subtitle', 'Update quantities, remove items, and checkout')

@section('hero_action')
  <a class="px-4 btn btn-outline-dark pill" href="{{ route('categories.index') }}">
    <i class="bi bi-grid me-1"></i> Continue Shopping
  </a>
@endsection

@section('breadcrumb')
  <nav aria-label="breadcrumb">
    <ol class="mb-0 breadcrumb">
      <li class="breadcrumb-item">
        <a href="{{ url('/') }}" class="text-decoration-none">Home</a>
      </li>
      <li class="breadcrumb-item active" aria-current="page">Cart</li>
    </ol>
  </nav>
@endsection

@section('content')
@php
  $cartItems = $cart ?? [];
  $subtotal = 0;

  foreach ($cartItems as $it) {
      $subtotal += ((float)($it['price'] ?? 0)) * ((int)($it['qty'] ?? 1));
  }
@endphp

@if(count($cartItems) === 0)
  <div class="p-4 border alert alert-light rounded-4">
    <div class="fw-bold">Your cart is empty.</div>
    <div class="text-muted">Add some products to cart then come back here.</div>

    <a href="{{ route('categories.index') }}" class="mt-3 btn btn-dark pill">
      <i class="bi bi-bag-plus me-1"></i> Browse Products
    </a>
  </div>
@else
  <div class="row g-3 g-lg-4">
    <div class="col-lg-8">
      <div class="p-3 card soft-card p-md-4">
        <div class="mb-3 d-flex justify-content-between align-items-center">
          <h5 class="m-0 fw-bold">Items</h5>

          <form method="POST" action="{{ route('cart.clear') }}">
            @csrf
            <button class="btn btn-outline-dark pill btn-sm" type="submit">
              <i class="bi bi-trash3 me-1"></i> Clear All Cart
            </button>
          </form>
        </div>

        <div class="table-responsive">
          <table class="table mb-0 align-middle">
            <thead class="text-muted small">
              <tr>
                <th style="min-width:280px;">Product</th>
                <th class="text-center">Price</th>
                <th class="text-center" style="width:170px;">Qty</th>
                <th class="text-end">Total</th>
                <th class="text-end">Action</th>
              </tr>
            </thead>

            <tbody id="cart-body">
              @foreach($cartItems as $pid => $it)
                @php
                  $img = !empty($it['image']) ? asset($it['image']) : null;
                  $price = (float)($it['price'] ?? 0);
                  $qty = (int)($it['qty'] ?? 1);
                  $lineTotal = $price * $qty;
                @endphp

                <tr id="row-{{ $pid }}">
                  <td>
                    <div class="gap-3 d-flex align-items-center">
                      <div class="overflow-hidden rounded-4 bg-light" style="width:72px;height:60px;box-shadow:0 8px 18px rgba(0,0,0,.08);">
                        @if($img)
                          <img
                            src="{{ $img }}"
                            alt="{{ $it['name'] ?? 'Product' }}"
                            style="width:100%;height:100%;object-fit:cover;"
                          >
                        @else
                          <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted small">
                            No Image
                          </div>
                        @endif
                      </div>

                      <div>
                        <div class="fw-bold">{{ $it['name'] ?? 'Product' }}</div>
                        <div class="text-muted small">Product ID: #{{ $pid }}</div>
                      </div>
                    </div>
                  </td>

                  <td class="text-center">
                    <span class="fw-semibold">${{ number_format($price, 2) }}</span>
                  </td>

                  <td class="text-center">
                    <div class="input-group justify-content-center" style="max-width:160px;margin:auto;">
                      <button
                        class="btn btn-outline-dark js-qty-minus"
                        type="button"
                        data-id="{{ $pid }}"
                      >-</button>

                      <input
                        type="number"
                        min="1"
                        class="text-center form-control js-qty"
                        value="{{ $qty }}"
                        data-id="{{ $pid }}"
                      >

                      <button
                        class="btn btn-outline-dark js-qty-plus"
                        type="button"
                        data-id="{{ $pid }}"
                      >+</button>
                    </div>
                  </td>

                  <td class="text-end">
                    <span class="fw-bold">$<span class="js-line-total" data-id="{{ $pid }}">{{ number_format($lineTotal, 2) }}</span></span>
                  </td>

                  <td class="text-end">
                    <button
                      class="btn btn-outline-danger pill btn-sm js-remove"
                      type="button"
                      data-id="{{ $pid }}"
                      data-url="{{ url('/cart/remove/' . $pid) }}"
                    >
                      <i class="bi bi-x-lg me-1"></i> Remove
                    </button>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="p-3 card soft-card p-md-4">
        <h5 class="mb-3 fw-bold">Order Summary</h5>

        <div class="d-flex justify-content-between text-muted">
          <span>Subtotal</span>
          <span>$<span id="subtotal">{{ number_format($subtotal, 2) }}</span></span>
        </div>

        <div class="mt-2 d-flex justify-content-between text-muted">
          <span>Shipping</span>
          <span>Free</span>
        </div>

        <hr>

        <div class="d-flex justify-content-between">
          <span class="fw-bold">Total</span>
          <span class="fw-bold fs-5">$<span id="total">{{ number_format($subtotal, 2) }}</span></span>
        </div>

        <a href="{{ route('checkout.index') }}" class="py-2 mt-3 btn btn-dark pill w-100">
          <i class="bi bi-credit-card me-1"></i> Checkout
        </a>

        <div class="mt-3 text-muted small">
          <i class="bi bi-shield-check me-1"></i> Secure payment • Easy returns
        </div>
      </div>

      <div class="p-3 mt-3 card soft-card p-md-4">
        <div class="mb-2 fw-bold">Have a coupon?</div>
        <div class="input-group">
          <input type="text" class="form-control pill" placeholder="Enter code">
          <button type="button" class="btn btn-outline-dark pill">Apply</button>
        </div>
        <div class="mt-2 text-muted small">* Coupon module optional</div>
      </div>
    </div>
  </div>
@endif
@endsection

@push('scripts')
<script>
  const csrfToken = '{{ csrf_token() }}';

  function showCartToast(message) {
    if (typeof window.showToast === 'function') {
      window.showToast(message);
    } else {
      alert(message);
    }
  }

  async function updateQty(productId, qty) {
    const url = `{{ url('/cart/update') }}/${productId}`;

    const res = await fetch(url, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: new URLSearchParams({ qty })
    });

    const data = await res.json().catch(() => ({}));

    if (!res.ok || !data.ok) {
      throw new Error(data.message || 'Update failed');
    }

    return data;
  }

  async function removeItem(url) {
    const res = await fetch(url, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    });

    const data = await res.json().catch(() => ({}));

    if (!res.ok || !data.ok) {
      throw new Error(data.message || 'Remove failed');
    }

    return data;
  }

  document.querySelectorAll('.js-qty-minus').forEach(btn => {
    btn.addEventListener('click', async () => {
      const id = btn.dataset.id;
      const input = document.querySelector(`.js-qty[data-id="${id}"]`);
      const currentQty = parseInt(input.value || 1);
      const qty = Math.max(1, currentQty - 1);
      input.value = qty;

      try {
        const data = await updateQty(id, qty);
        document.querySelector(`.js-line-total[data-id="${id}"]`).innerText = data.line_total;
        document.getElementById('subtotal').innerText = data.subtotal;
        document.getElementById('total').innerText = data.subtotal;
        showCartToast(data.message || 'Quantity updated');
      } catch (e) {
        showCartToast('⚠️ ' + e.message);
      }
    });
  });

  document.querySelectorAll('.js-qty-plus').forEach(btn => {
    btn.addEventListener('click', async () => {
      const id = btn.dataset.id;
      const input = document.querySelector(`.js-qty[data-id="${id}"]`);
      const currentQty = parseInt(input.value || 1);
      const qty = Math.max(1, currentQty + 1);
      input.value = qty;

      try {
        const data = await updateQty(id, qty);
        document.querySelector(`.js-line-total[data-id="${id}"]`).innerText = data.line_total;
        document.getElementById('subtotal').innerText = data.subtotal;
        document.getElementById('total').innerText = data.subtotal;
        showCartToast(data.message || 'Quantity updated');
      } catch (e) {
        showCartToast('⚠️ ' + e.message);
      }
    });
  });

  document.querySelectorAll('.js-qty').forEach(input => {
    input.addEventListener('change', async () => {
      const id = input.dataset.id;
      const qty = Math.max(1, parseInt(input.value || 1));
      input.value = qty;

      try {
        const data = await updateQty(id, qty);
        document.querySelector(`.js-line-total[data-id="${id}"]`).innerText = data.line_total;
        document.getElementById('subtotal').innerText = data.subtotal;
        document.getElementById('total').innerText = data.subtotal;
        showCartToast(data.message || 'Quantity updated');
      } catch (e) {
        showCartToast('⚠️ ' + e.message);
      }
    });
  });

  document.querySelectorAll('.js-remove').forEach(btn => {
    btn.addEventListener('click', async () => {
      const id = btn.dataset.id;
      const url = btn.dataset.url;

      if (!confirm('Remove this item?')) return;

      try {
        const data = await removeItem(url);

        const row = document.getElementById(`row-${id}`);
        if (row) row.remove();

        document.getElementById('subtotal').innerText = data.subtotal;
        document.getElementById('total').innerText = data.subtotal;

        if (document.querySelectorAll('#cart-body tr').length === 0) {
          location.reload();
        }

        showCartToast(data.message || 'Item removed');
      } catch (e) {
        showCartToast('⚠️ ' + e.message);
      }
    });
  });
</script>
@endpush