@extends('layouts.investor')

@section('content')

<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title"></h4>
        </div>
    </div>
        <form class="form-horizontal px-4 d-flex align-items-center">
                    <div class="form-group">
                        <select class="form-control" name="month" width="200">
                            @foreach(config('unicredit.months') as $key => $mth)
                                <option value="{{ $key + 1 }}" {{ ($key + 1) == $month ? 'selected': ''}}>{{ $mth }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mx-2">
                        <input type="text" name="year" value="{{ $year }}" class="form-control"/>
                    </div>

                 
                    <div class="form-group">
                        <button class="btn btn-sm btn-success">Get Recoveries</button>
                    </div>
                </form>
    <div class="row">
        <div class="col-md-12">
            <div class="card support-pane-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">Inflow</h4>
                    </div>
                    <div class="table-responsive support-pane no-wrap">
                        @if(count($transactions))
                        <table id="order-listing" class="table">
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Amount</th>
                            </tr>
                            @foreach($transactions as $transaction)
                                <tr>
                                    <td>{{$transaction->date_paid}}</td>
                                    <td>Repayments</td>
                                    <td>N{{$transaction->balance}}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>Total = N{{$transactions->sum('balance')}}</td>
                            </tr>
                        </table>
                        
                        @else
                        <div class="t-row text-center">
                            <h4>You've made no transactions yet</h4>
                        </div>
                        @endif
                    </div>
                    <!--<div class="d-flex justify-content-between align-items-center mt-4">-->
                    <!--    <p class="mb-0 d-none d-md-block"></p>-->
                    <!--    <nav>-->
                    <!--     
                    <!--    </nav>-->
                    <!--</div>-->
                </div>
            </div>
        </div>
    </div>
</div>
<!-- content-wrapper ends -->
@endsection
