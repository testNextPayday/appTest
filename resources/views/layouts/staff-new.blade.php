<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Nextpayday">
    <meta name="author" content="">
    <meta name="keyword" content="Nextpayday,credit,loans"> 
      
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{asset('unicredit-image-suite/icons/32/icons-10.png')}}">
    
    <title>Nextpayday</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{asset('assets/vendors/iconfonts/font-awesome/css/font-awesome.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/iconfonts/mdi/css/materialdesignicons.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/iconfonts/flag-icon-css/css/flag-icon.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/iconfonts/simple-line-icon/css/simple-line-icons.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/css/vendor.bundle.base.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/css/vendor.bundle.addons.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="{{asset('assets/css/style.css')}}">
    <link href="{{asset('css/unicredit.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" type="text/css" />
    <link type="text/css" href="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.11/css/dataTables.checkboxes.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{asset('css/custom.css')}}">
    
    @yield('page-css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/css/bootstrap-select.min.css">
</head>

<body>
    <div class="container-scroller" id="app">
        @include('layouts.staff-new.topnav')
    
        <div class="container-fluid page-body-wrapper">
            @include('layouts.staff-new.sidebar')
      
            <div class="main-panel">
                @yield('content')
        
                @include('layouts.staff-new.footer')
        
            </div>
            <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->
        
        @include('components.modals.emi')
    </div>
    <!-- container-scroller -->

    @include('layouts.partials.scripts')
    <!-- Custom js for this page-->
    <script src="{{asset('assets/js/dashboard.js')}}"></script>
    @yield('page-js')
    <!-- End custom js for this page-->
  
    @include('layouts.shared.alerts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/js/bootstrap-select.min.js"></script>
    <!-- <script> $(function() {$('select').selectpicker(); }); </script> -->
</body>
</html>