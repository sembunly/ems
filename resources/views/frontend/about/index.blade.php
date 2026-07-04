@extends('layouts.frontend')

@section('title', 'About Us - GenZ Electronics')

@section('hero_title', 'About GenZ Electronics')
@section('hero_subtitle', 'Your trusted source for laptops and electronics in Cambodia')

@section('hero_action')
  <a href="{{ route('categories.index') }}" class="px-4 btn btn-light rounded-pill">
    <i class="bi bi-grid me-1"></i>Browse Products
  </a>
@endsection

@section('breadcrumb')
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item">
        <a href="{{ url('/') }}" class="text-decoration-none">Home</a>
      </li>
      <li class="breadcrumb-item active" aria-current="page">About</li>
    </ol>
  </nav>
@endsection

@section('content')
<div class="row g-4">

  {{-- Company Info --}}
  <div class="col-12">
    <div class="border-0 shadow-sm card" style="border-radius: 16px;">
      <div class="p-4 card-body">
        <h2 class="mb-4 fw-bold" style="color: #4f46e5;">Welcome to GenZ Electronics</h2>
        <p class="mb-4 text-secondary">
          GenZ Electronics is your trusted destination for the latest laptops and electronics in Cambodia. 
          We specialize in providing high-quality laptops from top brands including Apple, Windows, and gaming laptops.
        </p>
        <p class="mb-4 text-secondary">
          Our mission is to make technology accessible to everyone with competitive prices, excellent customer service, 
          and fast delivery across Cambodia.
        </p>
      </div>
    </div>
  </div>

  {{-- Our Team --}}
  <div class="col-12">
    <div class="border-0 shadow-sm card" style="border-radius: 16px;">
      <div class="p-4 card-body">
        <h2 class="mb-4 text-center fw-bold" style="color: #4f46e5;">Our Team</h2>

        <div class="text-center row justify-content-center g-4">

          {{-- Member 1 --}}
          <div class="col-md-2 col-6">
            <img src="/images/team_members/by.jpg" class="mb-3 img-fluid rounded-circle"
                style="height:160px; width:160px; object-fit:cover;">
            <h6 class="mb-1 fw-bold">Name: Sem Bunly</h6>
            <p class="mb-0 text-secondary small">+855 XXX XXX XXX</p>
          </div>

          {{-- Member 2 --}}
          <div class="col-md-2 col-6">
            <img src="/images/team_members/khit.jpg" class="mb-3 img-fluid rounded-circle"
                style="height:160px; width:160px; object-fit:cover;">
            <h6 class="mb-1 fw-bold">Name: Khom Khit</h6>
            <p class="mb-0 text-secondary small">+855 XXX XXX XXX</p>
          </div>

          {{-- Member 3 --}}
          <div class="col-md-2 col-6">
            <img src="/images/team_members/lean.jpg" class="mb-3 img-fluid rounded-circle"
                style="height:160px; width:160px; object-fit:cover;">
            <h6 class="mb-1 fw-bold">Name: Chheng Lean</h6>
            <p class="mb-0 text-secondary small">+855 XXX XXX XXX</p>
          </div>

          {{-- Member 4 --}}
          <div class="col-md-2 col-6">
            <img src="/images/team_members/daro.jpg" class="mb-3 img-fluid rounded-circle"
                style="height:160px; width:160px; object-fit:cover;">
            <h6 class="mb-1 fw-bold">Name: Daro</h6>
            <p class="mb-0 text-secondary small">+855 XXX XXX XXX</p>
          </div>

        </div>
      </div>
    </div>
  </div>

  {{-- Contact Info --}}
  <div class="col-12">
    <div class="border-0 shadow-sm card"
        style="border-radius: 16px; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);">
      <div class="p-4 text-center card-body">
        <h4 class="mb-3 text-white fw-bold">Get In Touch</h4>
        <div class="flex-wrap gap-4 d-flex justify-content-center">
          <div class="text-white">
            <i class="bi bi-geo-alt me-2"></i>Phnom Penh, Cambodia
          </div>
          <div class="text-white">
            <i class="bi bi-telephone me-2"></i>+855 10 800 921
          </div>
          <div class="text-white">
            <i class="bi bi-telephone me-2"></i>+855 69 800 921
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