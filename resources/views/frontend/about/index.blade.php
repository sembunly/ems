@extends('layouts.frontend')

@section('title', 'About Us - GenZ Electronics')

@section('hero_title', 'About GenZ Electronics')
@section('hero_subtitle', 'Your trusted source for laptops and electronics in Cambodia')

@section('hero_action')
  <a href="{{ route('categories.index') }}" class="btn btn-light rounded-pill px-4">
    <i class="bi bi-grid me-1"></i>Browse Products
  </a>
@endsection

@section('breadcrumb')
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none">Home</a></li>
      <li class="breadcrumb-item active" aria-current="page">About</li>
    </ol>
  </nav>
@endsection

@section('content')
<div class="row g-4">
  {{-- Company Info --}}
  <div class="col-12">
    <div class="card border-0 shadow-sm" style="border-radius: 16px;">
      <div class="card-body p-4">
        <h2 class="fw-bold mb-4" style="color: #4f46e5;">Welcome to GenZ Electronics</h2>
        <p class="text-secondary mb-4">
          GenZ Electronics is your trusted destination for the latest laptops and electronics in Cambodia. 
          We specialize in providing high-quality laptops from top brands including Apple, Windows, and gaming laptops.
        </p>
        <p class="text-secondary mb-4">
          Our mission is to make technology accessible to everyone with competitive prices, excellent customer service, 
          and fast delivery across Cambodia.
        </p>
      </div>
    </div>
  </div>

  {{-- Features --}}
  <div class="col-md-4">
    <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
      <div class="card-body text-center p-4">
        <div class="mb-3">
          <i class="bi bi-laptop" style="font-size: 3rem; color: #4f46e5;"></i>
        </div>
        <h5 class="fw-bold mb-2">Quality Products</h5>
        <p class="text-secondary small mb-0">
          We offer only authentic products from trusted brands with full warranty support.
        </p>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
      <div class="card-body text-center p-4">
        <div class="mb-3">
          <i class="bi bi-truck" style="font-size: 3rem; color: #4f46e5;"></i>
        </div>
        <h5 class="fw-bold mb-2">Fast Delivery</h5>
        <p class="text-secondary small mb-0">
          Quick and reliable delivery across Phnom Penh and nationwide in Cambodia.
        </p>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
      <div class="card-body text-center p-4">
        <div class="mb-3">
          <i class="bi bi-shield-check" style="font-size: 3rem; color: #4f46e5;"></i>
        </div>
        <h5 class="fw-bold mb-2">Secure Payment</h5>
        <p class="text-secondary small mb-0">
          Safe and secure payment methods including QR code and cash on delivery.
        </p>
      </div>
    </div>
  </div>

  {{-- Contact Info --}}
  <div class="col-12">
    <div class="card border-0 shadow-sm" style="border-radius: 16px; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);">
      <div class="card-body p-4 text-center">
        <h4 class="fw-bold text-white mb-3">Get In Touch</h4>
        <div class="d-flex justify-content-center gap-4 flex-wrap">
          <div class="text-white">
            <i class="bi bi-geo-alt me-2"></i>Phnom Penh, Cambodia
          </div>
          <div class="text-white">
            <i class="bi bi-telephone me-2"></i>+855 10 800 921
          </div>
          <div class="text-white">
            <i class="bi bi-envelope me-2"></i>sembunly2005@gmail.com
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
