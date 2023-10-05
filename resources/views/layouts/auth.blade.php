<!DOCTYPE html>
<html lang="en">
<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="Loan management application">
  <meta name="author" content="Olotusquare">
  <meta name="keyword" content="Loan management, Loan applications, Loan trades">
  <!-- <link rel="shortcut icon" href="assets/ico/favicon.png"> -->
  
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>@yield('title')</title>

  <!-- Icons -->
  <link href="{{asset('coreui/vendors/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet">
  <link href="{{asset('coreui/vendors/simple-line-icons/css/simple-line-icons.css')}}" rel="stylesheet">

  <!-- Main styles for this application -->
  <link href="{{ asset('coreui/css/style.css') }}" rel="stylesheet">

  <!-- Styles required by this views -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    @yield('page-css')
</head>

<body class="app flex-row align-items-center">
  <div class="container" id="app">
    <div class="row justify-content-center">
      
          @yield('content')
        
          
        </div>
          <!-- <p class="text-center"><small>Powered by <a href="http://olotusquare.co" target="_blank">Olotusquare</a></small></p> -->
      </div>
      
    </div>
      
  </div>

  <!-- Bootstrap and necessary plugins -->
  <script src="{{asset('js/app.js')}}"></script>
  <script src="{{asset('coreui/vendors/jquery/dist/jquery.min.js')}}"></script>
  <script src="{{asset('coreui/vendors/popper.js/dist/umd/popper.min.js')}}"></script>
  <script src="{{asset('coreui/vendors/bootstrap/dist/js/bootstrap.min.js')}}"></script>
  
  <script src="https://js.paystack.co/v1/inline.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

  @if(Session::has('success'))
        <script>
            setTimeout(function()
            {
                var opts = {
                    "closeButton": true,
                    "debug": false,
                    "positionClass": "toast-top-right",
                    "toastClass": "black",
                    "onclick": null,
                    "showDuration": "300",
                    "hideDuration": "1000",
                    "timeOut": "5000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                };

                toastr.success("{{Session::get('success')}}", "Success", opts);
            }, 1000);
        </script>
    @endif

    @if(Session::has('failure'))
        <script>
            setTimeout(function()
            {
                var opts = {
                    "closeButton": true,
                    "debug": false,
                    "positionClass": "toast-top-right",
                    "toastClass": "black",
                    "onclick": null,
                    "showDuration": "300",
                    "hideDuration": "1000",
                    "timeOut": "5000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                };

                toastr.error("{{Session::get('failure')}}", "Error", opts);
            }, 3000);
        </script>
    @endif
    
     @if(Session::has('info'))
        <script>
            setTimeout(function()
            {
                var opts = {
                    "closeButton": true,
                    "debug": false,
                    "positionClass": "toast-top-right",
                    "toastClass": "black",
                    "onclick": null,
                    "showDuration": "300",
                    "hideDuration": "1000",
                    "timeOut": "5000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                };

                toastr.info("{{Session::get('info')}}", "Info", opts);
            }, 1000);
        </script>
    @endif
</body>
</html>