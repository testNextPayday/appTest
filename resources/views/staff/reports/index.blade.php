@extends('layouts.staff-new')

@section('content')
<div class="content-wrapper">
    <div class="card">
        <div class="card-body">
            <h2 class="card-title"> Reports Analysis </h2>
            <report></report>


        </div>
    </div>
</div>
@endsection

@section('page-js')


<script src="{{asset('assets/js/data-table.js')}}">
</script>
<script type="text/javascript" src="//cdn.datatables.net/plug-ins/1.10.19/api/sum().js"></script>
<!-- <script>
   window.modalOpened = false;

    var waitForEl = function(selector, callback) {
        if (jQuery(selector).length) {
            callback();
            
        } else {
            setTimeout(function() {
                waitForEl(selector, callback);
            }, 100);
        }
    };

    var successHandler = function(){

        $("#order-listing").DataTable({
          aLengthMenu: [[5, 10, 15, -1], [5, 10, 15, "All"]],
          dom: "Bfrtip",
          buttons: ["copy", "csv", "excel", "pdf"],
          iDisplayLength: 5,
          language: {
            search: ""
          }
        });
    }


   
    $(document).ready(function() {

        waitForEl("#order-listing",function(){successHandler();});
    })
</script> -->
@endsection