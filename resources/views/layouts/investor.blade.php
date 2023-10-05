<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="shortcut icon" href="{{asset('unicredit-image-suite/icons/32/icons-10.png')}}">
    
  <title>Nextpayday Investors</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="{{asset('assets/vendors/iconfonts/font-awesome/css/font-awesome.min.css')}}">
  <link rel="stylesheet" href="{{asset('assets/vendors/iconfonts/mdi/css/materialdesignicons.min.css')}}">
  <link rel="stylesheet" href="{{asset('assets/vendors/iconfonts/flag-icon-css/css/flag-icon.min.css')}}">
  <link rel="stylesheet" href="{{asset('assets/vendors/iconfonts/simple-line-icon/css/simple-line-icons.css')}}">
  <link rel="stylesheet" href="{{asset('assets/vendors/css/vendor.bundle.base.css')}}">
  <link rel="stylesheet" href="{{asset('assets/vendors/css/vendor.bundle.addons.css')}}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <link rel="stylesheet" href="{{asset('assets/css/style.css')}}">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
  @yield('page-css')
</head>

<body>
  <div class="container-scroller" id="app">
    @include('layouts.investors.topnav')
    
    <div class="container-fluid page-body-wrapper">
      @include('layouts.investors.sidebar')
      
      <div class="main-panel">
        @yield('content')
        
        @include('layouts.investors.footer')
        
        <!-- Request Modal Starts -->
        <div class="modal fade" id="fundWallet" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel-3" aria-hidden="true">
            <div class="modal-dialog modal-sm" role="document">
                <wallet-fund
                    :email="'{{Auth::guard('investor')->user()->email}}'"
                    :pay-key="'{{config('paystack.publicKey')}}'">
                </wallet-fund>
            </div>
        </div>
        <!-- Request Modal Ends -->
      </div>
      <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->

  @include('layouts.partials.scripts')
  <script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
    <script  src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script  src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

  <!-- Custom js for this page-->
  <script src="{{asset('assets/js/dashboard.js')}}"></script>
   @yield('page-js')
  
  <!-- End custom js for this page-->
  @include('layouts.shared.alerts')
</body>

</html>