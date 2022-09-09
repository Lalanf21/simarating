<!doctype html>
<html lang="en" class="light-theme">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  @stack('before-style')
  @include('include.style')
  @stack('after-style')

  <title>
      @yield('title')
  </title>
</head>

<body>


  <!--start wrapper-->
  <div class="wrapper " id="wrapper">

    @include('include.sidebar')

    @include('include.navbar')

    {{-- konten --}}
    <!-- start page content wrapper-->
    <div class="page-content-wrapper">
      <!-- start page content-->
        <div class="page-content">
            @yield('content')
        </div>
    </div>
      <!-- end page content-->
    
    </div>
    <!--end page content wrapper-->
    {{-- /konten --}}

    <!--Start Back To Top Button-->
    <a href="javaScript:;" class="back-to-top">
        <i class="fas fa-arrow-up mt-2"></i>
    </a>
    <!--End Back To Top Button-->

  </div>
  <!--end wrapper-->


    @stack('before-script')
    @include('include.script')
    @stack('after-script')


</body>

</html>