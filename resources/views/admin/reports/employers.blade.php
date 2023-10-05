    






@extends('layouts.admin-new')

@section('content')
<div class="content-wrapper">
    <div class="card">
        
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-row align-items-top">
                    <div class="ml-3 table-responsive">
                        <h4 class="mb-2">Download Active Loan</h4>
                        {{-- <form action="{{ Route('admin.download.activeLoan') }}" method="POST" class=mt-3>@csrf
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
                                    <button class="btn btn-sm btn-primary">Download </button>
                                </div>
                            </div>
                        </form> --}}

                        <table class="table table-stripped">
                            <thead>
                                <tr>
                                    <th>BVN	</th>
                                    <th>Phone Number</th>
                                    <th>Loan Amount</th>
                                    <th>Total Repayment Expected</th>
                                    <th>Loan Reference</th>
                                    <th>Repayment Account</th>
                                    {{-- <th>Customer ID	</th> --}}
                                    <th>Customer Name</th>
                                    {{-- <th>Sweep Type</th> --}}
                                    <th>Loan Request Date 	</th>
                                    {{-- <th>Consent Receipt Channel</th> --}}
                                    <th>Loan Expiration Date </th>
                                    <th>Loan Tenure</th>
                                </tr>
                            </thead>
                    
                    
                            @foreach ($loans as $loan)
                            <tr>
                                <td>{{ $loan->user->bvn }}	</td>
                                <td>{{ $loan->user->phone }}</td>
                                <td>N{{ number_format($loan->amount, 2) }}</td>
                                <td>N{{ number_format($loan->emi *  + $loan->duration, 2) }}</td>
                                <td>{{ $loan->reference }}</td>
                                <td>N{{ number_format($loan->emi, 2) }}</td>
                                {{-- <td>Customer ID	</td> --}}
                                <td>{{ $loan->user->name }}</td>
                                {{-- <td>Sweep Type</td> --}}
                                <td>{{ $loan->created_at->format('Y-m-d') }}</td>
                                {{-- <td>Consent Receipt Channel</td> --}}
                                <td>{{ $loan->due_date->format('Y-m-d') }}</td>
                                <td>{{ $loan->duration }}</td>
                            </tr>
                            @endforeach
                        </table>
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