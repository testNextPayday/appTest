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

    <!-- Icons -->
    <link href="{{asset('coreui/vendors/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet">
    <link href="{{asset('coreui/vendors/simple-line-icons/css/simple-line-icons.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css">
    <!-- Main styles for this application -->
    <link href="{{asset('coreui/css/style.css')}}" rel="stylesheet">
    <!-- Styles required by this views -->
    
    <link href="{{asset('css/unicredit.css')}}" rel="stylesheet">
    <style>
        .pagination li.active {
            padding: 7px;
        }
    </style>
    @yield('page-css')
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
    @include('layouts.admin.header')

    <div class="app-body">
        @include('layouts.admin.sidebar')

        <!-- Main content -->
        @yield('content')
        
        
        <div class="modal fade" id="fundWallet" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Fund Account Wallet</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
              
                    <div class="modal-body">
                        <form method="POST" id="fundAccountWallet" action="{{ route('admin.users.fund-wallet') }}" accept-charset="UTF-8">
                            <div class="form-group">
                                <label>Account Reference</label>
                                <input type="text" name="reference" class="form-control" placeholder="Enter account reference" required>    
                            </div>
                            
                            <div class="form-group">
                                <label>Payment code</label>
                                <input type="text" name="code" class="form-control" placeholder="Enter Payment Code" required>    
                            </div>
                            
                            <div class="form-group">
                                <label>Payment Amount</label>
                                <input type="number" name="amount" class="form-control" placeholder="Enter Amount" required>    
                            </div>
                            {{ csrf_field() }}
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" form="fundAccountWallet" class="btn btn-primary">Fund Wallet</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
    
    
    </div>
    @include('layouts.admin.footer')
    </div>
    <!-- Bootstrap and necessary plugins -->
    <script src="{{asset('js/app.js')}}"></script>
    <script src="{{asset('coreui/vendors/jquery/dist/jquery.min.js')}}"></script>
    <script src="{{asset('coreui/vendors/popper.js/dist/umd/popper.min.js')}}"></script>
    <script src="{{asset('coreui/vendors/bootstrap/dist/js/bootstrap.min.js')}}"></script>
    <script src="{{asset('coreui/vendors/pace-progress/pace.min.js')}}"></script>

    <!-- Plugins and scripts required by all views -->
    <script src="{{asset('coreui/vendors/chart.js/dist/Chart.min.js')}}"></script>
    
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" type="text/css" />
    <!-- CoreUI main scripts -->
    <script src="{{asset('coreui/js/app.js')}}"></script>

    <!-- Plugins and scripts required by this views -->

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
</body>
</html>