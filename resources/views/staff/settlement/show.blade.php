@extends('layouts.staff-new')

@section('content')
@php($loan = $settlement->loan)
<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">Loan {{$loan->reference}}</h4>
            <div>

                @component('components.stm-status', ['settlement' => $settlement])
                @endcomponent

            </div>
        </div>
    </div>

    @include('layouts.shared.error-display')

    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title"> Settlement Details</h4>
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td>Amount Paid</td>
                                <td>{{number_format($settlement->amount,2)}}</td>
                            </tr>
                            <tr>
                                <td>Collection Method</td>
                                <td>{{$settlement->collection_method}}</td>
                            </tr>
                            <tr>
                                <td>Date Paid</td>
                                <td>{{$settlement->paid_at}}</td>
                            </tr>
                        </tbody>
                    </table>
                   
                </div>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title bold">OTHER DATA</h4>
                    <br />
                    <h4 class="card-title bold"> SETTLEMENT DOCUMENTS</h4>
                    @if($settlement->payment_proof)
                    <a href="{{asset(Storage::url($settlement->payment_proof))}}" target="_blank">Payment Proof here</a>
                    @endif
                    @if($settlement->invoice)
                    <a href="{{route('settlement.invoice.view',['reference'=>$settlement->reference])}}" class="btn btn-primary"  target="_blank">Invoice</a>
                    @endif
                    <br />
                    @php($staff = auth('staff')->user())
                    @if ($staff->manages('confirm_settlement'))
                    @if($settlement->status == 1)
                    <div>
                        <a class="btn btn-xs btn-warning" onclick=" return confirm('Going ahead means this customer has successfully settled his/her loan and payment confirmed.This action is not reversible. Do you wish to proceed?')" href="{{ route('staff.settlement.confirm', ['reference' => $settlement->reference]) }}">
                            Confirm Settlement
                        </a>
                        <a class="btn btn-xs btn-danger" data-toggle="modal" data-target="#decline-modal" href="#">
                            Decline Settlement
                        </a>
        
                        <div class="modal fade" id="decline-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Decline Settlement {{$settlement->reference}}</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{route('staff.settlement.decline',['reference'=>$settlement->reference])}}" method="POST">
                                            @csrf
                                            <div class="form-group">
                                                <label class="form-control-label">Reason for decline</label>
                                                <textarea class="form-control" name="status_message"></textarea>
                                            </div>
                                            <div>
                                                <input type="submit" name="submit" value="Decline" class="btn btn-danger">
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">

                                    </div>
                                </div>
                                <!-- /.modal-content -->
                            </div>
                            <!-- /.modal-dialog -->
                        </div>
                    </div>
                    @elseif($settlement->status == 2)
                    <button disabled class="btn btn-primary">Confirmed</button>
                    @else
                    <a class="btn btn-xs btn-warning" onclick=" return confirm('Going ahead means this customer has successfully settled his/her loan and payment confirmed.This action is not reversible. Do you wish to proceed?');" href="{{ route('staff.settlement.confirm', ['reference' => $settlement->reference]) }}">
                        Confirm Settlement
                    </a>
                    <button class="btn btn-xs btn-danger" disabled >
                        Decline Settlement
                    </button>
                    @endif
                    @else
                    @endif
                    

                </div>
                <br />
            </div>
        </div>
    </div>
    <br />

    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Loan Repayments </h4>
            <div class="row">
                @if($loan->repaymentPlans->isNotEmpty() && $loan->repaymentPlans->first()->is_new )
                <div class="col-12 table-responsive">
                    <caption>EMI: {{number_format($loan->repaymentPlans->first()->emi,2)}}</caption>
                    <table class="table table-bordered">

                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Begining Balance</th>
                                <th>Payment</th>
                                <th>Principal</th>
                                <th>Interest</th>
                                <th>Ending Balance</th>

                                <th>Wallet Balance</th>
                                <th>Management Fee</th>
                                <th>
                                    Collected Date
                                </th>
                                <th>Amount Paid</th>
                                <th>Due Date</th>
                                <th>Payment Proof</th>
                                <th>Payment Method</th>
                                <th>Status</th>
                                <th>Action</th>
                                <th>
                                    Delete
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @php($i = 1)
                            @foreach ($loan->repaymentPlans as $plan)
                            <tr>
                                <td>{{$i}}</td>
                                <td>{{number_format($plan->begin_balance,2)}}</td>
                                <td>{{number_format($plan->payments,2)}}</td>
                                <td>{{number_format($plan->principal,2)}}</td>
                                <td>{{number_format($plan->interest,2)}}</td>
                                <td>{{number_format($plan->end_balance,2)}}</td>

                                <td>
                                    @if(! is_null($plan->wallet_balance))
                                    {{number_format($plan->wallet_balance,2)}}
                                    @else

                                    @endif
                                </td>
                                <td>{{number_format($plan->management_fee,2)}}</td>
                                <td>{{$plan->date_paid}}</td>
                                <td>{{number_format($plan->paid_amount,2)}}</td>
                                <td>{{$plan->payday}}</td>
                                @if($plan->payment_proof)
                                <td><a target="_blank" href="{{Storage::url($plan->payment_proof)}}">View</a></td>
                                @else
                                <td><a target="_blank" href="{{$plan->payment_proof}}">No proof</a></td>
                                @endif
                                <td>{{$plan->collection_mode}}</td>
                                <td>
                                    @if($plan->status)
                                    <a class="btn btn-xs btn-block btn-success" href="#">Paid</a>
                                    @else
                                    <a class="btn btn-xs btn-block btn-warning" href="#">Not Paid</a>
                                    @endif
                                </td>
                                <td>
                                    @if($plan->status == true)
                                    <button class="btn btn-warning" onclick="var status = confirm('Are you sure?'); if(status == true){document.getElementById('unconfirm-repayment{{$plan->id}}').submit()}">unconfirm</button>
                                    @else
                                    <button class="btn btn-primary" onclick="var status = confirm('Are you sure?'); if(status == true){document.getElementById('confirm-repayment{{$plan->id}}').submit()}">Confirm</button>
                                    @endif
                                </td>
                                <td><button onclick="var status = confirm('Are you sure?'); if(status == true){document.getElementById('delete-repayment{{$plan->id}}').submit()}">Delete</button></td>


                            </tr>
                            @php(++$i)
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else

                <div class="col-12 table-responsive">
                    <table id="" class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Interest</th>
                                <th>Principal</th>
                                <th>Mgt Fee</th>
                                <th>Total Amount</th>
                                <th>Due Date</th>
                                <th>Balance</th>
                                <th>
                                    Collected Date
                                </th>
                                <th>Payment Proof</th>
                                <th>Payment Method</th>
                                <th>Status</th>
                                <th>Action</th>
                                <th>
                                    Delete
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($loan->repaymentPlans as $plan)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>₦{{ number_format($plan->interest, 2) }}</td>
                                <td>₦{{ number_format($plan->principal, 2) }}</td>
                                <td>₦{{ number_format($plan->management_fee, 2) }}</td>
                                <td>₦{{ number_format($plan->interest + $plan->principal + $plan->management_fee, 2)}}</td>
                                <td>{{$plan->payday}}</td>

                                <td>
                                    @if($loop->iteration === $loan->loanRequest->duration)
                                    ₦0.00
                                    @else
                                    ₦{{ number_format($plan->balance, 2) }}
                                    @endif
                                </td>
                                <td>{{$plan->date_paid}}</td>
                                @if($plan->payment_proof)
                                <td><a target="_blank" href="{{Storage::url($plan->payment_proof)}}">View</a></td>
                                @else
                                <td><a target="_blank" href="{{$plan->payment_proof}}">No proof</a></td>
                                @endif
                                <td>{{$plan->collection_mode}}</td>
                                <td>
                                    @if($plan->status)
                                    <a class="btn btn-xs btn-block btn-success" href="#">Paid</a>
                                    @else
                                    <a class="btn btn-xs btn-block btn-warning" href="#">Not Paid</a>
                                    @endif
                                </td>
                                <td>
                                    @if($plan->status == true)
                                    <button class="btn btn-warning" onclick="var status = confirm('Are you sure?'); if(status == true){document.getElementById('unconfirm-repayment{{$plan->id}}').submit()}">unconfirm</button>
                                    @else
                                    <button class="btn btn-primary" onclick="var status = confirm('Are you sure?'); if(status == true){document.getElementById('confirm-repayment{{$plan->id}}').submit()}">Confirm</button>
                                    @endif
                                </td>
                                <td><button onclick="var status = confirm('Are you sure?'); if(status == true){document.getElementById('delete-repayment{{$plan->id}}').submit()}">Delete</button></td>

                            </tr>

                            @empty
                            <tr>
                                <td colspan="8" class="text-center">Repayments not found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>


                </div>
                @endif
                @foreach($loan->repaymentPlans as $plan)
                <form method="post" id="confirm-repayment{{$plan->id}}" action="{{route('staff.repayment.confirm',$plan)}}">
                    {{ csrf_field() }}
                </form>
                @endforeach
                @foreach($loan->repaymentPlans as $plan)
                <form method="post" id="unconfirm-repayment{{$plan->id}}" action="{{route('staff.repayment.unconfirm',$plan)}}">
                    {{ csrf_field() }}
                </form>
                @endforeach
                @foreach($loan->repaymentPlans as $plan)
                <form method="post" id="delete-repayment{{$plan->id}}" action="{{route('staff.repayment.delete',$plan)}}">
                    {{ csrf_field() }}
                    {{method_field('DELETE')}}
                </form>
                @endforeach
            </div>
        </div>
    </div>



</div>
@endsection

@section('page-js')
<script src="{{asset('assets/js/data-table.js')}}"></script>
@endsection