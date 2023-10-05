@extends('layouts.admin-new')
@section('content')
<div class="content-wrapper">
    <div class="card">
    @include('flash-message')
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h2 class="card-title"> Select A User To Generate Okra Balance ID </h2>
                    <form method="post" action="{{route('admin.update.okraID')}}">
                        @csrf                            
                            <div class="form-group col-xl-12">
                                
                                <select class="form-group col-xl-12 custom-select" name="reference">
                                    @foreach($users as $user)    
                                        <option value="{{$user->reference}}">{{$user->name}}</option>
                                    @endforeach
                                </select>
                            </div> 
                            <div class="form-group col-xl-3 mb-0">
                                <button type="submit" class="btn btn-primary ">Submit</button>
                            </div>
                    </form>
                </div>

                <div class="col-md-6">
                    <h2 class="card-title">Select A User To View Account Balance Before Initiating Payment</h2>
                    <form method="post" action="{{route('admin.retrieve.balance')}}">
                        @csrf
                            <div class="form-group col-xl-12">
                                <select class="form-group col-xl-12 custom-select" name="okra_balance_id">
                                    @foreach($userBalance as $user)    
                                        <option value="{{$user->banks->last()->okra_balance_id}}">{{$user->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-xl-3 mb-0">
                                <button type="submit" class="btn btn-primary ">Submit</button>
                            </div>
                    </form>
                </div>
            </div>
        </div>        

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h2 class="card-title">Select Loan To Initiate Payment </h2>
                    <form method="post" action="{{route('admin.okra.repayment')}}">
                        @csrf
                            <div class="form-group col-xl-12">
                                <select class="form-group col-xl-12 custom-select" name="plan_id">
                                    @foreach($plans as $plan)    
                                        <option value="{{$plan->id}}">{{$plan->loan->reference}}, {{$plan->loan->user->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-xl-3 ">
                                <button type="submit" class="btn btn-primary ">Submit</button>
                            </div>
                    </form>
                </div>
                <div class="col-md-6">
                    <h2 class="card-title"> Select User To Verify Payment</h2>
                    <form method="post" action="{{route('admin.verify.payment')}}">
                        @csrf
                            <div class="row">
                                <div class="form-group col-xl-12">
                                    <label>Select User To Verify Payment</label>
                                    <select class="form-group col-xl-12 custom-select" name="payment_id">
                                        @foreach($userPayment as $user)    
                                            <option value="{{$user->okraSetup->last()->payment_id}}">{{$user->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-xl-3 mb-0">
                                    <button type="submit" class="btn btn-primary ">Submit</button>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="card-body">
                <h2 class="card-title"> Select User To Save Record</h2>
                <form method="post" action="{{route('admin.okra.settlement')}}">
                    @csrf
                        <div class="row">
                            <div class="form-group col-xl-6">
                                
                                <select class="form-group col-xl-12 custom-select" name="plan_id">
                                    @foreach($userPayment as $user)    
                                        <option value="{{$user->okraSetup->last()->plan_id}}">{{$user->name}}</option>
                                    @endforeach
                                </select>
                                
                            </div>
                            <div class="form-group col-xl-6">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-xl-3 mb-0">
                                <button type="submit" class="btn btn-primary ">Submit</button>
                            </div>
                            <div class="form-group col-xl-9">
                            </div>
                        </div>    
                    
                </form>
        </div>
    </div>
</div>
@endsection

@section('page-js')


<script src="{{asset('assets/js/data-table.js')}}">
</script>
<script type="text/javascript" src="//cdn.datatables.net/plug-ins/1.10.19/api/sum().js"></script>

@endsection