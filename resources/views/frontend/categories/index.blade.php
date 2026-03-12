@extends('layouts.frontend')

@section('title','Categories')
@section('hero_title','Browse Categories')
@section('hero_subtitle','Choose a category to explore products')
@section('hero_action')
  <a class="px-4 btn btn-dark pill" href="{{ route('products.index') }}">
    <i class="bi bi-lightning-charge me-1"></i> Shop Now
  </a>
@endsection

@section('breadcrumb')
  <nav aria-label="breadcrumb">
    <ol class="mb-0 breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none">Home</a></li>
      <li class="breadcrumb-item active" aria-current="page">Categories</li>
    </ol>
  </nav>
@endsection

@section('content')
  <div class="gap-2 mb-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center">
    <h3 class="m-0 fw-bold">Categories</h3>
  </div>

  <div class="row g-3">
    @forelse($categories as $c)
      <div class="col-6 col-md-4 col-lg-3">
        <a href="{{ url('/category/'.$c->id.'/products') }}" class="text-decoration-none text-dark">
          <div class="card soft-card h-100">

            @if($c->image)
              <img src="{{ asset($c->image) }}" class="thumb" alt="{{ $c->name }}">
            @else
              <div class="noimg">No Image</div>
            @endif

            <div class="card-body">
              <div class="gap-2 d-flex justify-content-between align-items-start">
                <h6 class="m-0 fw-bold line-clamp-2">{{ $c->name }}</h6>
                <span class="border badge bg-light text-dark pill">View</span>
              </div>
              <div class="mt-2 text-muted small">
                {{ $c->description ?? 'Tap to see products' }}
              </div>
            </div>

          </div>
        </a>
      </div>
    @empty
      <div class="col-12">
        <div class="p-4 border alert alert-light rounded-4">
          <div class="fw-bold">No categories found.</div>
          <div class="text-muted">Please add categories from admin panel.</div>
        </div>
      </div>
    @endforelse
  </div>
@endsection