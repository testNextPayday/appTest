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