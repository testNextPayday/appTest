@extends('layouts.admin-new')

@section('content')
    <div class="content-wrapper">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h4>Select Period:</h4>
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
                    <div class="form-group mx-2">
                        <select class="form-control" name="collection_mode">
                            <option value="{{$method}}">{{$method ?? 'Select Method'}}</option>
                            <option value="Remita">Remita</option>
                            <option value="check">Check</option>
                            <option value="deposit">Bank Deposit</option>
                            <option value="ddas">DAS</option>
                            <option value="ippis">IPPIS</option>
                            <option value="paystack">Paystack</option>
                        </select>
                    </div>
                 
                    <div class="form-group">
                        <button class="btn btn-sm btn-success">Get Collections</button>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <h4 class="">Collections as at {{ @config('unicredit.months')[$month - 1] }}, {{ $year}}</h4>
                <br/>
                <h4> Total Amount : ₦ {{number_format($plans->sum('due_amount'),2)}}</h4>
                <h4>Total Count   : {{number_format($plans->count(),2)}}</h4>
                <div class="row">
                    <div class="col-12 table-responsive">
                        <table id="order-listing" class="table">
                            <thead>
                                <tr>
                                   
                                    <!-- <th>Mandate ID</th> -->
                                    <th>Email</th>
                                    <th>Due Amount</th>
                                    <th>Borrower</th>
                                    <th>Staff</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Employer</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($plans as $plan)
                                    @php

                                        $loan = $plan->loan; 
                                        $borrower =  $loan->user;
                                        $employment = $loan->loanRequest->employment;
                                        $staff = $loan->collector_type == 'AppModelsUser' ? 'no staff' : $loan->collector
                                       
                                    @endphp
                                    <tr>
                                       
                                        <!-- <td>
                                            <i class="fa fa-file"></i>&nbsp;
                                            <a href="{{route('admin.loans.view', ['reference' => $loan->reference])}}">
                                                {{ $loan->mandateId }}
                                            </a>
                                        </td> -->
                                        <td>
                                           
                                            <!-- @if(isset($loan->mandateId))
                                                <i class="fa fa-2x fa-play"></i> Push
                                            @endif -->
                                            <a href="{{route('admin.loans.view', ['reference' => $loan->reference])}}">
                                                {{$borrower->email}}
                                            </a>
                                            
                                        </td>
                                        <td>₦ {{ number_format($plan->interest + $plan->principal, 2) }}</td>
                                        <td>
                                            <i class="fa fa-user"></i>&nbsp;
                                            <a href="{{route('admin.users.view', ['user' =>$borrower->reference])}}">
                                                {{$borrower->name }}
                                            </a>
                                        </td>
                                        <td>
                                            {{optional($staff)->name ?? 'No staff'}}
                                        </td>
                                        <td>{{$plan->payday-}}</td>
                                        <td>
                                            @if($plan->status)
                                                <a class="badge badge-success" href="#">Paid - {{ $plan->collection_mode }}</a>
                                            @else
                                                <a class="badge badge-warning" href="#">{{ $plan->status_message ?? 'No Message'}}</a>
                                            @endif
                                        </td>

                                        <td>
                                            @if ($employment)

                                                {{$employment->employer->name}}
                                            @else 
                                            
                                                {{'No employment Data'}}
                                            @endif
                                        </td>
                                        
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">Data Unavailable</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-js')
    <script src="{{asset('assets/js/data-table.js')}}"></script>
@endsection