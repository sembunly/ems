@extends('layouts.frontend')

@section('title', $product->name)

@section('hero_title', $product->name)
@section('hero_subtitle', 'Product details and quick add to cart')

@section('hero_action')
  <a class="px-4 btn btn-outline-dark pill" href="{{ url()->previous() }}">
    <i class="bi bi-arrow-left me-1"></i> Back
  </a>
@endsection

@section('breadcrumb')
  <nav aria-label="breadcrumb">
    <ol class="mb-0 breadcrumb">
      <li class="breadcrumb-item">
        <a href="{{ route('home') }}" class="text-decoration-none">Home</a>
      </li>
      <li class="breadcrumb-item">
        <a href="{{ route('categories.index') }}" class="text-decoration-none">Categories</a>
      </li>

      @if($product->category)
        <li class="breadcrumb-item">
          <a href="{{ route('categories.show', $product->category->id) }}" class="text-decoration-none">
            {{ $product->category->name }}
          </a>
        </li>
      @endif

      <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
    </ol>
  </nav>
@endsection

@section('content')

<div class="row g-3 g-lg-4">
  <div class="col-lg-6">
    <div class="p-3 card soft-card">
      <div class="overflow-hidden rounded-4" style="box-shadow: var(--soft-shadow);">
        @php
          $mainImg = $product->image ? asset($product->image) : null;
        @endphp
      </div>

      @php
        $thumbs = collect([]);

        if ($product->image) {
            $thumbs->push(asset($product->image));
        }
  
        if (isset($images) && $images) {
            foreach ($images as $img) {
                $path = $img->image ?? $img->filename ?? $img->path ?? null;

                if ($path) {
                    $thumbs->push(asset('images/' . $path));
                }
            }
        }

        $thumbs = $thumbs->unique()->values();
      @endphp

      @if($thumbs->count() > 0)
        <div class="flex-wrap gap-2 mt-3 d-flex">
          @foreach($thumbs as $t)
            <button type="button"
                    class="p-0 overflow-hidden border-0 btn rounded-4 thumb-btn"
                    data-img="{{ $t }}"
                    style="width:100%;height:500px;box-shadow: 0 8px 18px rgba(0,0,0,.08);">
              <img src="{{ $t }}" style="width:100%;height:100%;object-fit:cover;" alt="thumb">
            </button>
          @endforeach
        </div>
      @endif
    </div>
  </div>

  <div class="col-lg-6">
    <div class="p-3 card soft-card p-md-4">
      <div class="gap-2 d-flex justify-content-between align-items-start">
        <div>
          <h3 class="mb-1 fw-bold">{{ $product->name }}</h3>
          <div class="text-muted small">
            <i class="bi bi-check-circle me-1"></i>
            @if($product->stock > 0)
              In stock • Fast delivery
            @else
              Out of stock
            @endif
          </div>
        </div>
        <span class="border badge bg-light text-dark pill">Best Seller</span>
      </div>

      <hr class="my-3">

      <div class="d-flex align-items-center justify-content-between">
        <div>
          <div class="mb-1 text-muted small">Price</div>
          <div class="fs-3 fw-bold">${{ number_format($product->price, 2) }}</div>
        </div>
        <div class="text-end">
          <div class="mb-1 text-muted small">Stock</div>
          <div class="fw-semibold">{{ $product->stock }}</div>
        </div>
      </div>

      <div class="mt-3 text-muted">
        {{ $product->description ?? 'High quality product with modern design. Perfect for daily use and easy to order.' }}
      </div>

      <hr class="my-3">

      <div class="row g-2">
        <div class="col-6">
          <div class="p-2 border rounded-4 bg-light">
            <div class="text-muted small">Brand</div>
            <div class="fw-semibold">{{ $product->brand ?? 'N/A' }}</div>
          </div>
        </div>

        <div class="col-6">
          <div class="p-2 border rounded-4 bg-light">
            <div class="text-muted small">Model</div>
            <div class="fw-semibold">{{ $product->model ?? 'N/A' }}</div>
          </div>
        </div>

        <div class="col-6">
          <div class="p-2 border rounded-4 bg-light">
            <div class="text-muted small">RAM</div>
            <div class="fw-semibold">{{ $product->ram ?? 'N/A' }}</div>
          </div>
        </div>

        <div class="col-6">
          <div class="p-2 border rounded-4 bg-light">
            <div class="text-muted small">Storage</div>
            <div class="fw-semibold">{{ $product->storage ?? 'N/A' }}</div>
          </div>
        </div>

        <div class="col-6">
          <div class="p-2 border rounded-4 bg-light">
            <div class="text-muted small">Processor</div>
            <div class="fw-semibold">{{ $product->processor ?? 'N/A' }}</div>
          </div>
        </div>

        <div class="col-6">
          <div class="p-2 border rounded-4 bg-light">
            <div class="text-muted small">Screen Size</div>
            <div class="fw-semibold">{{ $product->screen_size ?? 'N/A' }}</div>
          </div>
        </div>
      </div>

      <hr class="my-3">

      <div class="row g-2 align-items-end">
        <div class="col-5 col-md-4">
          <label class="mb-1 form-label small text-muted">Quantity</label>
          <div class="input-group">
            <button type="button" class="btn btn-outline-dark" id="qtyMinus">-</button>
            <input type="number" id="qty" class="text-center form-control" value="1" min="1">
            <button type="button" class="btn btn-outline-dark" id="qtyPlus">+</button>
          </div>
        </div>

        <div class="gap-2 col-7 col-md-8 d-flex">
          <button type="button"
                  class="py-2 btn btn-success pill w-100 js-add-to-cart"
                  data-url="{{ route('cart.add', $product->id) }}"
                  data-name="{{ $product->name }}"
                  @if($product->stock < 1) disabled @endif>
            <i class="bi bi-cart-plus me-1"></i> Add to Cart
          </button>

          <a href="{{ route('cart.index') }}" class="py-2 btn btn-dark pill w-100">
            <i class="bi bi-bag-check me-1"></i> View Cart
          </a>
        </div>
      </div>

      <div class="flex-wrap gap-2 mt-3 d-flex">
        <span class="border badge bg-light text-dark pill"><i class="bi bi-truck me-1"></i> Free Delivery</span>
        <span class="border badge bg-light text-dark pill"><i class="bi bi-shield-check me-1"></i> Secure Payment</span>
        <span class="border badge bg-light text-dark pill"><i class="bi bi-arrow-repeat me-1"></i> 7 Days Return</span>
      </div>
    </div>
  </div>
</div>

  <div class="p-3 mt-3 card soft-card mt-lg-4 p-md-4">
    <ul class="gap-2 nav nav-pills" id="prodTab" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active pill" data-bs-toggle="tab" data-bs-target="#tabDesc" type="button">Description</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link pill" data-bs-toggle="tab" data-bs-target="#tabShip" type="button">Shipping</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link pill" data-bs-toggle="tab" data-bs-target="#tabReview" type="button">Reviews</button>
      </li>
    </ul>

    <div class="mt-3 tab-content">
      <div class="tab-pane fade show active" id="tabDesc">
        <div class="text-muted">
          {!! nl2br(e($product->description ?? 'No description yet.')) !!}
        </div>
      </div>

      <div class="tab-pane fade" id="tabShip">
        <div class="text-muted">
          <ul class="mb-0">
            <li>Phnom Penh: 1–2 days</li>
            <li>Province: 2–4 days</li>
            <li>Cash on delivery / QR payment supported</li>
          </ul>
        </div>
      </div>

      <div class="tab-pane fade" id="tabReview">
        <div class="text-muted">
          Reviews module can be added later.
          <div class="p-3 mt-3 border rounded-4 bg-light">
            <div class="fw-semibold">Sokha</div>
            <div class="small text-muted">Great product, fast delivery.</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  @if(isset($related) && $related->count())
    <div class="mt-4 mb-2 d-flex justify-content-between align-items-center">
      <h5 class="m-0 fw-bold">Related Products</h5>
      <a class="btn btn-outline-dark pill btn-sm" href="{{ route('categories.index') }}">More</a>
    </div>

    <div class="row g-3">
      @foreach($related as $rp)
        <div class="col-6 col-md-4 col-lg-3">
          <div class="card soft-card h-100">
            @if($rp->image)
              <img src="{{ asset('storage/' . $rp->image) }}" class="thumb" alt="{{ $rp->name }}">
            @else
              <div class="noimg"><span class="small">No Image</span></div>
            @endif

            <div class="card-body d-flex flex-column">
              <div class="fw-bold line-clamp-2">{{ $rp->name }}</div>
              <div class="mb-2 text-muted small">${{ number_format($rp->price, 2) }}</div>

              <div class="gap-2 mt-auto d-flex">
                <a class="btn btn-dark pill w-100" href="{{ route('products.show', $rp->id) }}">Detail</a>
                <button type="button"
                        class="btn btn-success pill w-100 js-add-to-cart"
                        data-url="{{ route('cart.add', $rp->id) }}"
                        data-name="{{ $rp->name }}">
                  + Add
                </button>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @endif

@endsection

@push('scripts')
<script>
  document.querySelectorAll('.thumb-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const main = document.getElementById('mainImg');
      if (!main) return;
      main.src = btn.dataset.img;
    });
  });

  const qtyInput = document.getElementById('qty');
  const minus = document.getElementById('qtyMinus');
  const plus = document.getElementById('qtyPlus');

  if (minus && plus && qtyInput) {
    minus.addEventListener('click', () => {
      const v = Math.max(1, parseInt(qtyInput.value || 1) - 1);
      qtyInput.value = v;
    });

    plus.addEventListener('click', () => {
      const v = parseInt(qtyInput.value || 1) + 1;
      qtyInput.value = v;
    });
  }

  document.querySelectorAll('.js-add-to-cart').forEach(btn => {
    btn.addEventListener('click', async () => {
      const url = btn.dataset.url;
      const name = btn.dataset.name || 'Item';
      const qty = qtyInput ? parseInt(qtyInput.value || 1) : 1;

      btn.disabled = true;
      const oldHtml = btn.innerHTML;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Adding';

      try {
        const res = await fetch(url, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: new URLSearchParams({ qty })
        });

        const data = await res.json().catch(() => ({}));

        if (!res.ok) {
          throw new Error(data.message || 'Request failed');
        }

        showCartToast(data.message || `${name} (x${qty}) added to cart!`);
      } catch (e) {
        showCartToast(e.message || 'Cannot add to cart. Please try again.');
      } finally {
        btn.disabled = false;
        btn.innerHTML = oldHtml;
      }
    });
  });
</script>
@endpush