@extends('layouts.frontend')

@section('title', 'Products')
@section('hero_title', 'All Products')
@section('hero_subtitle', 'Browse all available products')

@section('hero_action')
  <a class="px-4 btn btn-outline-dark pill" href="{{ route('categories.index') }}">
    <i class="bi bi-grid me-1"></i> All Categories
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
      <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
    </ol>
  </nav>
@endsection

@section('content')

<div class="row g-4">
  {{-- Left Sidebar: Filters --}}
  <div class="col-lg-3">
    <form method="GET" action="{{ route('categories.show', $category->id) }}" class="search-filter-card">
      <h5 class="fw-bold mb-3" style="color: #4f46e5;">
        <i class="bi bi-funnel me-2"></i>Filters
      </h5>

      <div class="mb-3">
        <label class="form-label small fw-medium">Price Range</label>
        <div class="row g-2">
          <div class="col-6">
            <input type="number" name="min_price" value="{{ request('min_price') }}" class="form-control" placeholder="Min">
          </div>
          <div class="col-6">
            <input type="number" name="max_price" value="{{ request('max_price') }}" class="form-control" placeholder="Max">
          </div>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label small fw-medium">Sort By</label>
        <select name="sort" class="form-select">
          <option value="">Default</option>
          <option value="latest" @selected(request('sort') == 'latest')>Latest</option>
          <option value="price_asc" @selected(request('sort') == 'price_asc')>Price: Low to High</option>
          <option value="price_desc" @selected(request('sort') == 'price_desc')>Price: High to Low</option>
          <option value="name_asc" @selected(request('sort') == 'name_asc')>Name: A to Z</option>
          <option value="name_desc" @selected(request('sort') == 'name_desc')>Name: Z to A</option>
        </select>
      </div>

      <div class="d-grid gap-2">
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-funnel me-1"></i> Apply Filters
        </button>
        <a href="{{ route('categories.show', $category->id) }}" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
        </a>
      </div>
    </form>
  </div>

  {{-- Right Side: Products --}}
  <div class="col-lg-9">
  @if($products->count() == 0)
    <div class="p-4 border alert alert-light rounded-4">
      <div class="fw-bold">No products in this category.</div>
      <div class="text-muted">Try another filter or category.</div>
    </div>
  @else
    <div class="row g-3">
      @foreach($products as $p)
        <div class="col-6 col-md-4 col-lg-3">
          <div class="card soft-card h-100">

            @if($p->image)
              <img src="{{ asset($p->image) }}" class="thumb" alt="{{ $p->name }}">
            @else
              <div class="noimg d-flex align-items-center justify-content-center" style="height:220px;">
                <span class="small text-muted">No Image</span>
              </div>
            @endif

            <div class="card-body d-flex flex-column">
              <h6 class="mb-1 fw-bold line-clamp-2">{{ $p->name }}</h6>

              <div class="mb-2 text-muted small">
                @if($p->stock > 0)
                  In stock • Fast delivery
                @else
                  Out of stock
                @endif
              </div>

              <div class="mb-2 small text-muted">
                <div><strong>Brand:</strong> {{ $p->brand }}</div>
                <div><strong>Model:</strong> {{ $p->model }}</div>
                @if($p->ram)
                  <div><strong>RAM:</strong> {{ $p->ram }}</div>
                @endif
                @if($p->storage)
                  <div><strong>Storage:</strong> {{ $p->storage }}</div>
                @endif
                @if($p->processor)
                  <div><strong>Processor:</strong> {{ $p->processor }}</div>
                @endif
                @if($p->screen_size)
                  <div><strong>Screen:</strong> {{ $p->screen_size }}</div>
                @endif
              </div>

              <div class="mb-3 d-flex align-items-center justify-content-between">
                <span class="fw-bold">${{ number_format($p->price, 2) }}</span>
                <span class="border badge bg-light text-dark pill">
                  {{ $p->stock > 0 ? 'Popular' : 'Unavailable' }}
                </span>
              </div>

              <div class="gap-2 mt-auto d-flex">
                <a class="btn btn-dark pill w-100" href="{{ route('products.show', $p->id) }}">
                  Detail
                </a>

                <button type="button"
                        class="btn btn-success pill w-100 js-add-to-cart"
                        data-url="{{ route('cart.add', $p->id) }}"
                        data-name="{{ $p->name }}"
                        @if($p->stock < 1) disabled @endif>
                  + Add
                </button>
              </div>
            </div>

          </div>
        </div>
      @endforeach
    </div>

    <div class="mt-4 d-flex justify-content-center">
      {{ $products->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
  @endif
  </div>
</div>

@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.js-add-to-cart').forEach(btn => {
      btn.addEventListener('click', async () => {
        const url = btn.dataset.url;
        const name = btn.dataset.name || 'Item';

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
            body: new URLSearchParams({ qty: 1 })
          });

          const data = await res.json().catch(() => ({}));

          if (!res.ok) {
            throw new Error(data.message || 'Request failed');
          }

          if (typeof showCartToast === 'function') {
            showCartToast(data.message || `${name} added to cart!`);
          } else {
            alert(data.message || `${name} added to cart!`);
          }
          if (typeof updateCartBadge === 'function' && data.cart_count !== undefined) {
            updateCartBadge(data.cart_count);
          }
        } catch (e) {
          if (typeof showCartToast === 'function') {
            showCartToast(e.message || 'Cannot add to cart. Please try again.');
          } else {
            alert(e.message || 'Cannot add to cart. Please try again.');
          }
        } finally {
          btn.disabled = false;
          btn.innerHTML = oldHtml;
        }
      });
    });
  });
</script>
@endpush