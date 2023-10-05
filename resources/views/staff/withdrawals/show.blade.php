@extends('layouts.staff-new')

@section('content')
<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">
               
            </h4>
           
        </div>
    </div>

    @include('layouts.shared.error-display')

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Withdrawal Details</h4>
                </div>

                <div class="card-body">
                    <h3>{{$request->requester->name}}</h3>
                    <h5></h5>
                    <h5>₦ {{number_format($request->amount, 2)}}</h5>
                    <h5>{{date('D d, M Y H:i:s a', strtotime($request->created_at))}}</h5>
                    <br>
                    <h5> 
                        @if($request->status == 1)
                            <a class="btn btn-xs btn-success" 
                                href="{{route('admin.withdrawal-requests.approve', ['request_id' => $request->id ])}}"
                                onclick="return confirm('Are you sure you want to approve this request?');">
                                <i class="icon-action-redo"></i> Approve</a>

                            <a class="btn btn-xs btn-warning" 
                                href="{{route('admin.withdrawal-requests.approve-backend', ['request_id' => $request->id ])}}"
                                onclick="return confirm('Are you sure you want to approve this request backend?');">
                                <i class="icon-action-redo"></i> Approve (Backend)</a>

                            <a class="btn btn-xs btn-danger"
                                href="{{route('admin.withdrawal-requests.decline', ['request_id' => $request->id ])}}"
                                onclick="return confirm('Are you sure you want to decline this request?');">
                                <i class="icon-close"></i> Decline</a>
                        @elseif($request->status == 2)
                            <button class="btn btn-xs btn-success"><i class="icon-check"></i> Paid</button>
                        @elseif($request->status == 3)
                            <button class="btn btn-xs btn-default"><i class="icon-arrow-down-circle"></i> Cancelled</button>
                        @else
                            <button class="btn btn-xs btn-danger"><i class="icon-ban"></i> Declined</button>
                        @endif
                    </h5>
                </div>
            </div>
        </div>        
    </div>

    <br />

    <div class="row">
       <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Wallet Transactions After Previous Withdrawal</h4>
                </div>

                <div class="card-body">
                <div class="row">
                    <div class="col-12 table-responsive">
                        <table id="order-listing" class="table">
                            <thead>
                                <tr>
                                
                                    <th>Entity</th>
                                 
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Direction</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions->where('purse', 1) as $transaction)
                                    
                                    <tr>
                                        
                                        <td>
                                            @if(!is_null($transaction->entity))
                                                {{optional($transaction->entity->user)->name}}
                                            @else
                                                {{'#######'}}
                                            @endif
                                        </td>
                                       
                                        <td>{{$transaction->created_at}}</td>
                                        <td>₦{{ number_format($transaction->amount, 2)}}</td>
                                        <td>
                                            @if($transaction->direction == 1) 
                                                {{'INFLOW'}}
                                            @else
                                                {{'OUTFLOW'}}
                                            @endif
                                            
                                            
                                        </td>
                                        <td>{{$transaction->description}}</td>
                                       
                                        
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No collected commisisons</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                </div>
            </div>
       </div>
    </div>

    <br />

    


    <br />

   

</div>
@endsection

@section('page-js')
<script src="{{asset('assets/js/data-table.js')}}"></script>
@endsection