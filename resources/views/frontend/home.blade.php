@extends('layouts.frontend')

@section('title', 'Home')
@section('hero_title', 'All Products')
@section('hero_subtitle', 'Search, filter, sort, and add to cart easily')

@section('hero_action')
  <a class="px-4 btn btn-dark pill" href="{{ route('categories.index') }}">
    <i class="bi bi-grid me-1"></i> Browse Categories
  </a>
@endsection

@section('breadcrumb')
  <nav aria-label="breadcrumb">
    <ol class="mb-0 breadcrumb">
      <li class="breadcrumb-item">
        <a href="{{ url('/') }}" class="text-decoration-none">Home</a>
      </li>
      <li class="breadcrumb-item">
        <a href="{{ url('/categories') }}" class="text-decoration-none">Categories</a>
      </li>
    </ol>
  </nav>
@endsection

@push('styles')
  <style>
    .thumb {
      width: 100%;
      height: 220px;
      object-fit: cover;
      object-position: center;
      display: block;
      border-top-left-radius: 16px;
      border-top-right-radius: 16px;
    }

    .noimg {
      width: 100%;
      height: 220px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #f8f9fa;
      border-top-left-radius: 16px;
      border-top-right-radius: 16px;
    }
  </style>
@endpush

@section('content')

  <div class="row g-4">
    {{-- Left Sidebar: Filters --}}
    <div class="col-lg-3">
      <form method="GET" class="search-filter-card">
        <h5 class="mb-3 fw-bold" style="color: #4f46e5;">
          <i class="bi bi-funnel me-2"></i>Filters
        </h5>

        <div class="mb-3">
          <label class="form-label small fw-medium">Search</label>
          <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search products...">
        </div>

        <div class="mb-3">
          <label class="form-label small fw-medium">Category</label>
          <select name="category" class="form-select">
            <option value="">All Categories</option>
            @foreach($categories as $c)
              <option value="{{ $c->id }}" @selected(request('category') == $c->id)>
                {{ $c->name }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label small fw-medium">Price Range</label>
          <div class="row g-2">
            <div class="col-6">
              <input type="number" name="min_price" value="{{ request('min_price') }}" class="form-control"
                placeholder="Min">
            </div>
            <div class="col-6">
              <input type="number" name="max_price" value="{{ request('max_price') }}" class="form-control"
                placeholder="Max">
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

        <div class="gap-2 d-grid">
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-funnel me-1"></i> Apply Filters
          </button>
          <a href="{{ route('home') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
          </a>
        </div>
      </form>
    </div>

    {{-- Right Side: Products --}}
    <div class="col-lg-9">
      @if($products->count() == 0)
        <div class="p-4 border alert alert-light rounded-4">
          <div class="fw-bold">No products found.</div>
          <div class="text-muted">Try different filters.</div>
        </div>
      @else
        <div class="row g-3">
          @foreach($products as $p)
            <div class="col-6 col-md-4 col-lg-3">
              <div class="card soft-card h-100">

                @if($p->image)
                  <img src="{{ asset($p->image) }}" class="thumb" alt="{{ $p->name }}">
                @else
                  <div class="noimg"><span class="small">No Image</span></div>
                @endif

                <div class="card-body d-flex flex-column">
                  <div class="mb-1 fw-bold line-clamp-2">{{ $p->name }}</div>

                  <div class="mb-1 text-muted small">
                    {{ $p->brand ?? 'No Brand' }}
                  </div>

                  <div class="mb-2 text-muted small">
                    Stock: {{ $p->stock }}
                  </div>

                  <div class="mb-3 d-flex justify-content-between align-items-center">
                    <span class="fw-bold">${{ number_format($p->price, 2) }}</span>
                    <span class="border badge bg-light text-dark pill">
                      {{ $p->category?->name ?? 'Uncategorized' }}
                    </span>
                  </div>

                  <div class="gap-2 mt-auto d-flex align-items-stretch">
                    <a class="py-2 btn btn-dark pill flex-grow-1 btn-sm" href="{{ route('products.show', $p->id) }}">
                      Detail
                    </a>

                    <button type="button" class="flex-shrink-0 px-3 py-2 btn btn-success pill btn-sm js-add-to-cart"
                      data-url="{{ route('cart.add', $p->id) }}" data-name="{{ $p->name }}">
                      + Add
                    </button>
                  </div>

                </div>
              </div>
            </div>
          @endforeach
        </div>

        <div class="mt-4 d-flex justify-content-center">
          {{ $products->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
      @endif
    </div>
  </div>

@endsection

@push('scripts')
  <script>
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
              'X-Requested-With': 'XMLHttpRequest',
              'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({ qty: 1 })
          });

          const data = await res.json().catch(() => ({}));

          if (!res.ok) {
            throw new Error(data.message || 'Request failed');
          }

          showCartToast(data.message || `${name} added to cart!`);
          if (data.cart_count !== undefined) {
            updateCartBadge(data.cart_count);
          }
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