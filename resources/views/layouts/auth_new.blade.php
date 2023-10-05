<!DOCTYPE html>
<html lang="en">


<!-- Mirrored from www.urbanui.com/chroma/pages/samples/login-2.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 24 Jul 2018 22:23:09 GMT -->
<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Nextpayday | Authentication</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="{{asset('assets/vendors/iconfonts/font-awesome/css/font-awesome.min.css')}}">
  <link rel="stylesheet" href="{{asset('assets/vendors/iconfonts/mdi/css/materialdesignicons.min.css')}}">
  <link rel="stylesheet" href="{{asset('assets/vendors/iconfonts/flag-icon-css/css/flag-icon.min.css')}}">
  <link rel="stylesheet" href="{{asset('assets/vendors/iconfonts/simple-line-icon/css/simple-line-icons.css')}}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <link rel="stylesheet" href="{{asset('assets/vendors/css/vendor.bundle.base.css')}}">
  <link rel="stylesheet" href="{{asset('assets/vendors/css/vendor.bundle.addons.css')}}">
  <link rel="stylesheet" href="{{asset('assets/css/style.css')}}">
</head>

<body>
  <div class="container-scroller" id="app">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper auth p-0 theme-two">
        <div class="row d-flex align-items-stretch">
          <div class="col-md-4 banner-section d-none d-md-flex align-items-stretch justify-content-center">
            <div class="slide-content bg-1">
            </div>
          </div>
          <div class="col-12 col-md-8 h-100 bg-white">
            @yield('content')
          </div>
        </div>
      </div>
      <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->
  
  @include('layouts.partials.scripts')
  @include('layouts.shared.alerts')
  
  <!-- Custom js for this page-->
  <!-- End custom js for this page-->
</body>

</html>