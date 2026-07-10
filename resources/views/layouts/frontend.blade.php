<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title','Store Electronics')</title>

  {{-- Bootstrap 5 --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  {{-- Bootstrap Icons --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  {{-- Google Fonts --}}
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  {{-- Custom styles --}}
  <link rel="stylesheet" href="{{ asset('css/frontend.css') }}">

  @stack('styles')
</head>
<body class="bg-light">

  {{-- NAVBAR --}}
  <nav class="navbar navbar-expand-lg sticky-top bg-white shadow-sm" style="border-bottom: 1px solid #e5e7eb;">
    <div class="container-fluid px-4">
      <a class="navbar-brand fw-bold fs-4" href="{{ url('/') }}" style="color: #4f46e5;">
        <i class="bi bi-laptop me-1"></i>Store
      </a>

      <button class="border-0 shadow-none navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="topNav">
        <ul class="mb-2 navbar-nav me-auto mb-lg-0">
          <li class="nav-item">
            <a class="nav-link px-3 rounded-pill me-1" href="{{ route('categories.index') }}" style="color: #374151;">
              <i class="bi bi-grid me-1"></i>Categories
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link px-3 rounded-pill me-1" href="{{ route('about') }}" style="color: #374151;">
              <i class="bi bi-info-circle me-1"></i>About
            </a>
          </li>
        </ul>

        {{-- Right actions --}}
        <div class="gap-2 d-flex align-items-center">
          @php
            $cart = session()->get('cart', []);
            $cartCount = collect($cart)->sum('qty');
          @endphp
          <a href="{{ route('cart.index') }}" class="btn btn-primary rounded-pill px-3 position-relative" style="background: #4f46e5; border-color: #4f46e5;">
            <i class="bi bi-cart3 me-1"></i>Cart
            @if($cartCount > 0)
              <span id="cartBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                {{ $cartCount }}
              </span>
            @else
              <span id="cartBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem; display: none;">
                0
              </span>
            @endif
          </a>

          @auth
            <div class="dropdown">
              <button class="btn btn-light rounded-pill dropdown-toggle px-3 d-flex align-items-center" data-bs-toggle="dropdown" style="border: 1px solid #e5e7eb; gap: 0.5rem;">
                @if(auth()->user()->avatar)
                  <img src="{{ auth()->user()->avatar }}" alt="Avatar" class="rounded-circle" style="width: 24px; height: 24px; object-fit: cover;">
                @else
                  <i class="bi bi-person-circle" style="color: #4f46e5; font-size: 1.2rem;"></i>
                @endif
                <span class="fw-medium">{{ auth()->user()->name }}</span>
              </button>
              <ul class="dropdown-menu dropdown-menu-end shadow-lg" style="border: none; border-radius: 12px;">
                <li><a class="dropdown-item py-2" href="{{ route('profile') }}"><i class="bi bi-person me-2"></i>Profile</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                  <form action="{{ url('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button class="dropdown-item py-2">
                      <i class="bi bi-box-arrow-right me-2"></i>Logout
                    </button>
                  </form>
                </li>
              </ul>
            </div>
          @else
            <a href="{{ url('login') }}" class="btn btn-light rounded-pill px-3" style="border: 1px solid #e5e7eb; color: #374151;">
              <i class="bi bi-box-arrow-in-right me-1"></i>Login
            </a>
          @endauth
        </div>
      </div>
    </div>
  </nav>

  {{-- HEADER / HERO --}}
  <header class="container-fluid px-4 mt-3">
    <div class="card border-0 shadow-sm" style="border-radius: 20px; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);">
      <div class="card-body py-4 px-4">
        <div class="row align-items-center g-3">
          <div class="col-md-7">
            <h1 class="mb-2 fw-bold text-white">@yield('hero_title','Welcome to Store Electronics')</h1>
            <p class="mb-0 text-white-50">@yield('hero_subtitle','Discover the latest laptops and accessories at great prices')</p>
          </div>
          <div class="col-md-5 text-md-end">
            @yield('hero_action')
          </div>
        </div>
      </div>
    </div>
  </header>

  {{-- BREADCRUMB --}}
  <section class="container-fluid px-4 mt-2">
    @yield('breadcrumb')
  </section>

  {{-- CONTENT --}}
  <main class="container-fluid px-4 mt-3 min-vh-70">
    @if(session('success'))
      <div class="alert alert-success d-flex align-items-center gap-2 rounded-3 shadow-sm border-0" style="background: #d1fae5; color: #065f46;">
        <i class="bi bi-check-circle-fill"></i>
        <span>{{ session('success') }}</span>
      </div>
    @endif

    @yield('content')
  </main>

  {{-- FOOTER --}}
  <footer class="mt-5 py-4" style="background: #1f2937;">
    <div class="container-fluid px-4">
      <div class="row g-4">
        <div class="col-md-4">
          <div class="mb-2 fw-bold fs-5" style="color: #4f46e5;">
            <i class="bi bi-laptop me-2"></i>Store Electronics
          </div>
          <div class="small" style="color: #9ca3af;">
            Your trusted source for laptops and electronics in Cambodia.
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="mb-2 fw-semibold" style="color: #f3f4f6;">Quick Links</div>
          <div class="d-grid gap-2 small">
            <a href="{{ route('categories.index') }}" class="text-decoration-none" style="color: #9ca3af;">Categories</a>
            <a href="#" class="text-decoration-none" style="color: #9ca3af;">New Arrivals</a>
            <a href="#" class="text-decoration-none" style="color: #9ca3af;">Contact</a>
          </div>
        </div>
        <div class="col-6 col-md-4">
          <div class="mb-2 fw-semibold" style="color: #f3f4f6;">Contact Us</div>
          <div class="small" style="color: #9ca3af;">
            <div class="mb-1"><i class="bi bi-geo-alt me-1"></i>Phnom Penh, Cambodia</div>
            <div class="mb-1"><i class="bi bi-telephone me-1"></i>+855 10 800 921</div>
            <div><i class="bi bi-envelope me-1"></i>sembunly2005@gmail.com</div>
          </div>
        </div>
      </div>
      <hr class="my-4" style="border-color: #374151;">
      <div class="d-flex justify-content-between small" style="color: #6b7280;">
        <span>© {{ date('Y') }} Store Electronics</span>
        <span>Store Laptop</span>
      </div>
    </div>
  </footer>

  {{-- TOAST --}}
  <div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="cartToast" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true" style="background: #4f46e5; border-radius: 12px;">
      <div class="d-flex">
        <div class="toast-body d-flex align-items-center gap-2">
          <i class="bi bi-check-circle-fill"></i>
          <span id="toastMessage">Added to cart!</span>
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>
  </div>

  {{-- Bootstrap JS --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  {{-- Toast helper --}}
  <script>
    function showCartToast(message = "Added to cart!") {
      document.getElementById('toastMessage').textContent = message;
      const el = document.getElementById('cartToast');
      const toast = new bootstrap.Toast(el, { delay: 2000 });
      toast.show();
    }

    // Update cart badge in navbar
    function updateCartBadge(count) {
      const badge = document.getElementById('cartBadge');
      if (badge) {
        if (count > 0) {
          badge.textContent = count;
          badge.style.display = 'inline';
        } else {
          badge.style.display = 'none';
        }
      }
    }

    @if(session('cart_toast'))
      document.addEventListener('DOMContentLoaded', () => {
        showCartToast(@json(session('cart_toast')));
      });
    @endif
  </script>

  @stack('scripts')
</body>
</html>
