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
  <form method="GET" action="{{ route('categories.show', $category->id) }}" class="p-3 mb-3 card soft-card p-md-4">
    <div class="row g-2 align-items-end">
      <div class="col-md-4">
        <label class="mb-1 form-label small text-muted">Filter by price</label>
        <div class="gap-2 d-flex">
          <input type="number" name="min_price" value="{{ request('min_price') }}" class="form-control pill" placeholder="Min">
          <input type="number" name="max_price" value="{{ request('max_price') }}" class="form-control pill" placeholder="Max">
        </div>
      </div>

      <div class="col-md-4">
        <label class="mb-1 form-label small text-muted">Sort</label>
        <select name="sort" class="form-select pill">
          <option value="">-- Default --</option>
          <option value="latest" @selected(request('sort') == 'latest')>Latest</option>
          <option value="price_asc" @selected(request('sort') == 'price_asc')>Price: Low → High</option>
          <option value="price_desc" @selected(request('sort') == 'price_desc')>Price: High → Low</option>
          <option value="name_asc" @selected(request('sort') == 'name_asc')>Name: A → Z</option>
          <option value="name_desc" @selected(request('sort') == 'name_desc')>Name: Z → A</option>
        </select>
      </div>

      <div class="gap-2 col-md-4 d-flex">
        <button type="submit" class="btn btn-dark pill w-100">
          <i class="bi bi-funnel me-1"></i> Apply
        </button>

        <a class="btn btn-outline-dark pill w-100" href="{{ route('categories.show', $category->id) }}">
          Reset
        </a>
      </div>
    </div>
  </form>

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
      {{ $products->appends(request()->query())->links() }}
    </div>
  @endif
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