<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Nextpayday">
    <meta name="author" content="">
    <meta name="keyword" content="Nextpayday,credit,loans">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="shortcut icon" href="{{asset('unicredit-image-suite/icons/32/icons-10.png')}}">
    <title>Nextpayday</title>

    <!-- Add this to <head> -->
    <!--<link type="text/css" rel="stylesheet" href="//unpkg.com/bootstrap/dist/css/bootstrap.min.css"/>-->
    <link type="text/css" rel="stylesheet" href="//unpkg.com/bootstrap-vue@latest/dist/bootstrap-vue.css" />


    <!-- Icons -->
    <link href="{{asset('coreui/vendors/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet">
    <link href="{{asset('coreui/vendors/simple-line-icons/css/simple-line-icons.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <!-- Main styles for this application -->
    <link href="{{asset('coreui/css/style.css')}}" rel="stylesheet">
    <!-- Styles required by this views -->

    <link href="{{asset('css/unicredit.css')}}" rel="stylesheet">

    @yield('page-css')

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/css/bootstrap-select.min.css">

</head>


<body class="app header-fixed sidebar-fixed aside-menu-fixed aside-menu-hidden footer-fixed">

    <div id="app">

        <header class="app-header navbar">
            <button class="navbar-toggler mobile-sidebar-toggler d-lg-none mr-auto" type="button">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand" href="#"></a>
          
        </header>

        <div class="app-body">


            <!-- Main content -->
            @yield('content')



        </div>
      

    </div>
    <!-- Bootstrap and necessary plugins -->
    <script src="{{asset('js/app.js')}}"></script>


    <!-- Add this after vue.js -->
    <script src="//unpkg.com/babel-polyfill@latest/dist/polyfill.min.js"></script>
    <script src="//unpkg.com/bootstrap-vue@latest/dist/bootstrap-vue.js"></script>

    <script src="{{asset('coreui/vendors/jquery/dist/jquery.min.js')}}"></script>
    <script src="{{asset('coreui/vendors/popper.js/dist/umd/popper.min.js')}}"></script>
    <script src="{{asset('coreui/vendors/bootstrap/dist/js/bootstrap.min.js')}}"></script>
    <script src="{{asset('coreui/vendors/pace-progress/pace.min.js')}}"></script>

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js">
    </script>

    <!-- Plugins and scripts required by all views -->
    <script src="{{asset('coreui/vendors/chart.js/dist/Chart.min.js')}}"></script>



    <!-- CoreUI main scripts -->
    <script src="{{asset('coreui/js/app.js')}}"></script>

    <!-- Plugins and scripts required by this views -->

    <!-- Custom scripts required by this view -->
    @yield('page-js')

    @if(Session::has('success'))
    <script>
    setTimeout(function() {
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
    setTimeout(function() {
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
    setTimeout(function() {
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/js/bootstrap-select.min.js"></script>
    <script src="https://js.paystack.co/v1/inline.js"></script>
</body>

</html>