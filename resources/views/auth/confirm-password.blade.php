<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Confirm Password - Store Electronics</title>

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

                <h4>Confirm your password</h4>
                <h6 class="mb-4 fw-light">Please confirm your password before continuing.</h6>

                <!-- Validation Errors -->
                @if ($errors->any())
                  <div class="alert alert-danger">
                    <ul class="mb-0" style="list-style: none; padding: 0;">
                      @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                      @endforeach
                    </ul>
                  </div>
                @endif

                <form class="pt-3" method="POST" action="{{ route('password.confirm') }}">
                  @csrf

                  <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input 
                      type="password" 
                      class="form-control form-control-lg @error('password') is-invalid @enderror" 
                      id="password" 
                      name="password"
                      placeholder="Enter your password"
                      required 
                      autocomplete="current-password"
                    >
                    @error('password')
                      <div class="invalid-feedback d-block">
                        {{ $message }}
                      </div>
                    @enderror
                  </div>

                  <div class="mt-3">
                    <button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">
                      Confirm
                    </button>
                  </div>

                  <div class="text-center mt-4 fw-light">
                    <a href="{{ route('login') }}" class="text-primary">Back to login</a>
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
    <script src="{{ asset('staradmin/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('staradmin/js/template.js') }}"></script>
    <script src="{{ asset('staradmin/js/file-upload.js') }}"></script>
  </body>
</html>
