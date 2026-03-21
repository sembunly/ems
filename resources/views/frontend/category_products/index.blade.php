<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="m-0">Category: {{ $category->CategoryName }}</h3>
  <a class="btn btn-outline-dark btn-sm" href="{{ route('front.categories.index') }}">All Categories</a>
</div>

@if($products->count() == 0)
  <div class="alert alert-light border">No products in this category.</div>
@else
  <div class="row g-3">
    @foreach($products as $p)
      <div class="col-md-3">
        <div class="card h-100">
          @if($p->ProductImage)
            <img src="{{ asset('img/product/'.$p->ProductImage) }}"
                 style="height:160px;object-fit:cover;"
                 class="card-img-top" alt="Product">
          @endif

          <div class="card-body">
            <h6 class="card-title">{{ $p->ProductName }}</h6>
            <div class="text-muted">${{ number_format($p->Price,2) }}</div>
            
            <div class="d-flex gap-2 mt-2">
              <a class="btn btn-sm btn-primary w-100" href="#">Detail</a>
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  <div class="mt-3">
    {{ $products->links('pagination::bootstrap-5') }}
  </div>
@endif