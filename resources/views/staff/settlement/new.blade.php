@extends('layouts.staff-new')

@section('content')
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title"> Add Settlements</h4>
                <div class="row">
                    <div class="col-12 table-responsive">
                        <form method="POST" class="row" action="{{route('staff.upload.settlement')}}" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group col-md-3">
                                <label class="form-label-control">Loan Reference</label>
                                <input type="text" name="reference" class="form-control">
                            </div>

                            <div class="form-group col-md-3">
                                <label class="form-label-control">Settlement Amount</label>
                                <input type="text" name="amount" class="form-control">
                            </div>

                            <div class="form-group col-md-3 ">
                                <label class="form-label-control">Collection Method</label>
                                <select name="method" id="" class="form-control">
                                    <option value="Bank">Bank</option>
                                    <option value="Cash">Cash</option>
                                </select>
                            </div>

                            <div class="form-group col-md-3">
                                <label class="form-control-label">Payment Proof</label>
                                <input type="file" name="payment_proof">
                            </div>

                            <div class="form-group col-md-3">
                                <label class="form-label-control">Date Paid</label>
                                <input type="date" name="paid_at" class="form-control">
                            </div>
                            
                            <div class="form-group col-md-3" >
                                <input type="submit" name="submit" class="btn btn-primary" value="Save">
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-js')
    <script src="{{asset('assets/js/data-table.js')}}"></script>
@endsection