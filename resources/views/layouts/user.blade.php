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
    <link type="text/css" rel="stylesheet" href="//unpkg.com/bootstrap-vue@latest/dist/bootstrap-vue.css"/>


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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
</head>
<!-- BODY options, add following classes to body to change options

// Header options
1. '.header-fixed'					- Fixed Header

// Brand options
1. '.brand-minimized'       - Minimized brand (Only symbol)

// Sidebar options
1. '.sidebar-fixed'					- Fixed Sidebar
2. '.sidebar-hidden'				- Hidden Sidebar
3. '.sidebar-off-canvas'		- Off Canvas Sidebar
4. '.sidebar-minimized'			- Minimized Sidebar (Only icons)
5. '.sidebar-compact'			  - Compact Sidebar

// Aside options
1. '.aside-menu-fixed'			- Fixed Aside Menu
2. '.aside-menu-hidden'			- Hidden Aside Menu
3. '.aside-menu-off-canvas'	- Off Canvas Aside Menu

// Breadcrumb options
1. '.breadcrumb-fixed'			- Fixed Breadcrumb

// Footer options
1. '.footer-fixed'					- Fixed footer

-->
    

<body class="app header-fixed sidebar-fixed aside-menu-fixed aside-menu-hidden footer-fixed">
    <div id="app">
    @include('layouts.users.header')

    <div class="app-body">
        @include('layouts.users.sidebar')

        <!-- Main content -->
        @yield('content')

        {{-- @include('layouts.users.aside-menu') --}}

        <!-- <div class="fixed-bottom mb-4 mr-4"> 
          <a class="pull-right" id="whatsapp" onclick="getDeviceType(this)">
            <img src="/images/whatsapp.svg" width="50" height="50">
          </a>
        </div> -->

    </div>
    @include('layouts.users.footer')
        <div class="modal fade" id="fundWallet" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm" role="document">
                <wallet-fund
                    :email="'{{Auth::guard('web')->user()->email}}'"
                    :pay-key="'{{config('paystack.publicKey')}}'">
                    
                </wallet-fund>
                <!-- /.modal-content -->
            </div>
              <!-- /.modal-dialog -->
        </div>
    </div>
    <!-- Bootstrap and necessary plugins -->
    <script src="{{mix('js/app.js')}}"></script>
    
    
    <!-- Add this after vue.js -->
    <script src="//unpkg.com/babel-polyfill@latest/dist/polyfill.min.js"></script>
    <script src="//unpkg.com/bootstrap-vue@latest/dist/bootstrap-vue.js"></script>

    <script src="{{asset('coreui/vendors/jquery/dist/jquery.min.js')}}"></script>
    <script src="{{asset('coreui/vendors/popper.js/dist/umd/popper.min.js')}}"></script>
    <script src="{{asset('coreui/vendors/bootstrap/dist/js/bootstrap.min.js')}}"></script>
    <script src="{{asset('coreui/vendors/pace-progress/pace.min.js')}}"></script>
    
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

    <!-- Plugins and scripts required by all views -->
    <script src="{{asset('coreui/vendors/chart.js/dist/Chart.min.js')}}"></script>
    


    <!-- CoreUI main scripts -->
    <script src="{{asset('coreui/js/app.js')}}"></script>

    <!-- Plugins and scripts required by this views -->
    
    
    
    <!-- <script type="text/javascript">
        //Whatsappp
        //function getDeviceType(attr){
  
          //if (navigator.userAgent.match(/Android/i) 
                //|| navigator.userAgent.match(/webOS/i) 
                //|| navigator.userAgent.match(/iPhone/i)  
                //|| navigator.userAgent.match(/iPad/i)  
                //|| navigator.userAgent.match(/iPod/i) 
                //|| navigator.userAgent.match(/BlackBerry/i) 
                //|| navigator.userAgent.match(/Windows Phone/i)) { 
                    //document.getElementById("whatsapp").setAttribute("href", `https://api.whatsapp.com/send?phone=2348095000667`);
           // } else { 
                //const setAttributes = function(el, attrs) {
                    //for(var key in attrs) {
                      //  el.setAttribute(key, attrs[key]);
                    //}
                //}
                //var elem = document.getElementById("whatsapp");
                //setAttributes(elem, {"href": "https://web.whatsapp.com/send?phone=2348095000667", "target": "_blank"});
           // } 

          
       // }
    </script> -->
    <!-- Custom scripts required by this view -->
    @yield('page-js')
    
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/js/bootstrap-select.min.js"></script>
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <!-- Start of HubSpot Embed Code -->
  <script type="text/javascript" id="hs-script-loader" async defer src="//js-na1.hs-scripts.com/20587769.js"></script>
<!-- End of HubSpot Embed Code -->
</body>
</html>