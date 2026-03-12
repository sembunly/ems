@extends('layouts.frontend')

@section('title', $category->name)

@push('styles')
<style>
.thumb{
    width:100%;
    height:220px;
    object-fit:cover;
    object-position:center;
}

.card{
    border-radius:16px;
    overflow:hidden;
    border:1px solid #eee;
}

.card:hover{
    transform:translateY(-4px);
    transition:0.2s;
    box-shadow:0 8px 20px rgba(0,0,0,0.08);
}

.line-clamp-2{
    display:-webkit-box;
    -webkit-line-clamp:2;
    -webkit-box-orient:vertical;
    overflow:hidden;
}

.noimg{
    width:100%;
    height:220px;
    display:flex;
    align-items:center;
    justify-content:center;
    background:#f8f9fa;
}

.card-footer{
    border-top:none;
}
</style>
@endpush

@section('content')

<div class="container py-4">

    <div class="mb-4 d-flex justify-content-between align-items-center">
        <h3 class="m-0 fw-bold">
            Products in {{ $category->name }}
        </h3>

        <a href="{{ route('categories.index') }}" class="btn btn-outline-dark btn-sm">
            ← Back to Categories
        </a>
    </div>

    <div class="row g-3">

        @forelse($products as $p)

        <div class="col-6 col-md-4 col-lg-3">

            <div class="card h-100">

                {{-- image --}}
                @if($p->image)
                    <img src="{{ asset($p->image) }}"
                         class="thumb"
                         alt="{{ $p->name }}">
                @else
                    <div class="noimg">No Image</div>
                @endif


                <div class="card-body d-flex flex-column">

                    <div class="mb-1 fw-bold line-clamp-2">
                        {{ $p->name }}
                    </div>

                    <div class="mb-1 text-muted small">
                        {{ $p->brand ?? 'No Brand' }}
                    </div>

                    <div class="mb-2 text-muted small">
                        Stock: {{ $p->stock }}
                    </div>

                    <div class="mt-auto mb-3 fw-bold">
                        ${{ number_format($p->price,2) }}
                    </div>

                </div>


                <div class="bg-white card-footer">

                    <a href="{{ route('products.show',$p->id) }}"
                       class="btn btn-dark btn-sm w-100">
                        Detail
                    </a>

                </div>

            </div>

        </div>

        @empty

        <div class="col-12">
            <div class="border alert alert-light">
                No products found in this category.
            </div>
        </div>

        @endforelse

    </div>


    <div class="mt-4 d-flex justify-content-center">
        {{ $products->links() }}
    </div>

</div>

@endsection