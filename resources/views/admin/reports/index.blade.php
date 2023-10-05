@extends('layouts.admin-new')

@section('content')
<div class="content-wrapper">
    <div class="card">
        <div class="card-body">
            <h2 class="card-title"> Reports Analysis </h2>
            <report></report>
            
            

        </div>
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-row align-items-top">
                    <div class="ml-3">
                        <h4 class="mb-2">Download Active Loan</h4>
                        <form action="{{ Route('admin.download.activeLoan') }}" method="POST" class=mt-3>@csrf
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="table">Category</label>
                                    <select name="employer_id" id="" class="form-control">
                                        @foreach ($employers as $employer)
                                        <option value="{{ $employer->id }}">{{ $employer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="table">From</label>
                                    <input type="date" class="form-control" name="fromDate">
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="table"></label>
                                    <button class="btn btn-sm btn-primary">Submit </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
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