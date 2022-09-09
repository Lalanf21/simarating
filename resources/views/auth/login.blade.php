<!doctype html>
<html lang="en" class="light-theme">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSS Files -->
  <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/css/bootstrap-extended.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

  <title>Login</title>
</head>

<body class="bg-white">
  <div class="d-md-none d-lg-none d-lg-none login-bg-overlay"></div>
  <!--start wrapper-->
  <div class="container">
    <div class="row">
      <div class="col-xl-6 col-lg-12 mt-4 p-2">
        <div class="login-cover-wrapper">
          <div class="card shadow-none">
            <div class="card-body p-4">
              <div class="text-center">
                <h3>Login to continue</h3>
              </div>
              <form class="form-body row g-3" action="{{ route('proses_login') }}" method="post">
                @method('POST')
                @csrf
                <div class="col-12">
                  <label for="nomor_hp" class="form-label">Mobile number</label>
                  <input type="text" name="no_hp" class="form-control  @error('no_hp') is-invalid @enderror" id="nomor_hp">
                  @error('no_hp')
                  <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                  </span>
                  @enderror
                </div>
  
                <div class="col-12">
                  <label for="inputPassword" class="form-label">Password</label>
                  <input type="password" name="password" class="form-control  @error('password') is-invalid @enderror" id="inputPassword">
                  @error('password')
                  <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                  </span>
                  @enderror
                </div>
               
                <div class="col-12 col-lg-12">
                  <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Login</button>
                  </div>
                </div>
                <div class="col-12 col-lg-12 text-center">
                  <p class="mb-0">Don't have an account? <a href="{{ route('register') }}">Register here</a></p>
                </div>
              </form>

              <div class="card">
                <div class="card-footer">
                 <div class="row">
                    <div class="col">
                      <p>Account Admin </p>
                      <p> mobile phone : 08123123</p>
                      <p> Pass : 123456</p>
                    </div>
                    <div class="col">
                      <p>Account user </p>
                      <p> mobile phone : 08123123123</p>
                      <p> Pass : 123456</p>
                    </div>
                 </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-6 col-lg-12">
        <div class="position-fixed top-0 h-100 d-xl-block d-none login-cover-img">
        </div>
      </div>
    </div>
  </div>
  <!--end wrapper-->


  <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.8/dist/sweetalert2.all.min.js"></script>
  <script>
     @if(Session::has('alert_message'))
        Swal.fire({
            icon: "{{ Session::get('alert_type') }}",
            text: "{{ Session::get('alert_message') }}"
        });
    @endif
  </script>
</body>


</html>