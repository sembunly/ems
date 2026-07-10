<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Register</title>

    <link rel="stylesheet" href="{{ asset('staradmin/vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('staradmin/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('staradmin/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('staradmin/vendors/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('staradmin/vendors/typicons/typicons.css') }}">
    <link rel="stylesheet" href="{{ asset('staradmin/vendors/simple-line-icons/css/simple-line-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('staradmin/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('staradmin/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('staradmin/css/style.css') }}">
    <link rel="shortcut icon" href="{{ asset('staradmin/images/favicon.png') }}" />
  </head>

  <body>
    <div class="container-scroller">
      <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="px-0 content-wrapper d-flex align-items-center auth">
          <div class="mx-0 row w-100">
            <div class="mx-auto col-lg-4">
              <div class="px-4 py-5 text-left auth-form-light px-sm-5">

                <div class="text-center">
                  <h1>Store Electronics</h1><br>
                </div>

                <h4>Create your account</h4>
                <h6 class="mb-4 fw-light">Sign up to get started.</h6>

                @if (session('status'))
                  <div class="alert alert-success">
                    {{ session('status') }}
                  </div>
                @endif

                <form class="pt-3" method="POST" action="{{ route('register') }}">
                  @csrf

                  <div class="form-group">
                    <input
                      type="text"
                      class="form-control form-control-lg @error('name') is-invalid @enderror"
                      id="name"
                      name="name"
                      value="{{ old('name') }}"
                      placeholder="Name"
                      required
                      autofocus
                    >
                    @error('name')
                      <div class="invalid-feedback d-block">
                        {{ $message }}
                      </div>
                    @enderror
                  </div>

                  <div class="form-group">
                    <input
                      type="email"
                      class="form-control form-control-lg @error('email') is-invalid @enderror"
                      id="email"
                      name="email"
                      value="{{ old('email') }}"
                      placeholder="Email"
                      required
                    >
                    @error('email')
                      <div class="invalid-feedback d-block">
                        {{ $message }}
                      </div>
                    @enderror
                  </div>

                  <div class="form-group">
                    <input
                      type="password"
                      class="form-control form-control-lg @error('password') is-invalid @enderror"
                      id="password"
                      name="password"
                      placeholder="Password"
                      required
                    >
                    @error('password')
                      <div class="invalid-feedback d-block">
                        {{ $message }}
                      </div>
                    @enderror
                  </div>

                  <div class="form-group">
                    <input
                      type="password"
                      class="form-control form-control-lg @error('password_confirmation') is-invalid @enderror"
                      id="password_confirmation"
                      name="password_confirmation"
                      placeholder="Confirm Password"
                      required
                    >
                    @error('password_confirmation')
                      <div class="invalid-feedback d-block">
                        {{ $message }}
                      </div>
                    @enderror
                  </div>

                  <div class="gap-2 mt-3 d-grid">
                    <button type="submit" class="btn btn-block btn-primary btn-lg fw-medium auth-form-btn">
                      REGISTER
                    </button>
                  </div>

                  <div class="gap-2 mt-3 d-grid">
                    <a href="{{ route('google.redirect') }}" class="btn btn-block btn-outline-danger btn-lg fw-medium auth-form-btn">
                      <i class="mdi mdi-google me-2"></i> Sign up with Google
                    </a>
                  </div>

                  <div class="mt-4 text-center fw-light">
                    Already have an account?
                    <a href="{{ route('login') }}" class="text-primary">Login.</a>
                  </div>
                </form>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script src="{{ asset('staradmin/vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('staradmin/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('staradmin/js/off-canvas.js') }}"></script>
    <script src="{{ asset('staradmin/js/template.js') }}"></script>
    <script src="{{ asset('staradmin/js/settings.js') }}"></script>
    <script src="{{ asset('staradmin/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('staradmin/js/todolist.js') }}"></script>
  </body>
</html>