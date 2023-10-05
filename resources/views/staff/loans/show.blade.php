@extends('layouts.staff-new')

@section('content')
<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">Loan {{$loan->reference}}</h4>
            <div>
                @if($loan->is_penalized)
                    <span class="badge badge-danger">
                        <i class="fa fa-exclamation-triangle"></i>
                        Penalised
                    </span>
                @endif

                @component('components.loan-status', ['loan' => $loan])
                @endcomponent

                @component('components.loan-statement-button',['loan'=>$loan])
                @endcomponent

                @php($staff = auth('staff')->user())
                @if($staff->manages('loan_transactions'))
                @component('components.add_loan_transaction',['url'=>route('staff.loan.transaction.add',['reference'=>$loan->reference]),'loan'=>$loan])
                @endcomponent
                @endif
                @if($staff->manages('settlements'))
                @if($loan->canSettle())
                <button class="btn btn-primary">
                    <a href="{{route('staff.settlement.preview',['reference'=>$loan->reference])}}" target="_blank"
                        style="color:white"> Settle Loan</a>
                </button>
                @endif
                @endif
                <div id="topUp" class="modal fade" role="dialog">
                    <div class="modal-dialog">

                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Top up loan</h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="{{route('staff.loan-requests.store') }}"
                                    enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <input type="hidden" value="{{$loan->reference}}" name="loan_referenced" />
                                    <input type="hidden" value="true" name="is_top_up">
                                    @php($users = collect([$loan->user->load('employments.employer')]))
                                    <max-request-amount :url="'{{route('staff.loan-requests.checkmax')}}'"
                                        :emi-url="'{{route('staff.loan-requests.checkemi')}}'" :users="{{$users}}"
                                        :user="{{$loan->user}}"></max-request-amount>

                                    <!-- <div class="checkbox mb-2 mt-2">
                                        <label for="checkbox1">
                                            <input type="checkbox" id="checkbox1"
                                                name="will_collect_incomplete">&nbsp;&nbsp;
                                            Will this loan be taken if its incomplete by the expected withdrawal date?
                                        </label>
                                    </div>

                                    <div class="form-group">
                                        <label for="bank_statement" style="display:block;">Bank Statement (3 months from
                                            today, JPG or PDF)
                                            <span class="pull-right">Max Size: 1MB</span>
                                        </label>
                                        
                                        <input type="file" class="form-control" name="bank_statement"
                                            id="bank_statement" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="pay_slip" style="display:block;">Pay Slip (Not more than 3months
                                            old)
                                            <span class="pull-right">Max Size: 1MB</span>
                                        </label>
                                        <input type="file" class="form-control" name="pay_slip" id="pay_slip" required>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-sm-12">
                                            <label for="textarea-input">Purpose of Loan</label>
                                            <textarea id="textarea-input" name="comment" rows="3" class="form-control"
                                                placeholder="This is your opportunity to increase your chances of getting a Loan">{{old('comment')}}</textarea>
                                        </div>
                                    </div>

                                    <br />

                                    <div class="checkbox">
                                        <p>
                                            <strong>NOTE:</strong> Our loans are covered by Life insurance cover and
                                            Loss of job insurance cover.
                                            Total is 2.5% of requested amount. This amount will be removed from the
                                            total amount realized form this request.
                                        </p>
                                        <label for="checkbox2">
                                            <input type="checkbox" id="checkbox2" name="accept_insurance" required>
                                            Accept Insurance Terms
                                        </label>
                                    </div>
                                    <br />

                                    <button type="submit" class="btn btn-sm btn-primary"><i
                                            class="fa fa-dot-circle-o"></i> Submit</button> -->
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.shared.error-display')

    <div class="row">
        <div class="col-md-4 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-0">Loaned Amount</h4>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-inline-block pt-3">
                            <div class="d-flex">
                                <h2 class="mb-0">₦{{number_format($loan->amount, 0)}}</h2>
                                <div class="d-none d-md-flex align-items-center ml-2">
                                    <!--<i class="mdi mdi-clock text-muted"></i>-->
                                    <!--<small class=" ml-1 mb-0">Updated: 9:10am</small>-->
                                </div>
                            </div>
                            <small class="text-gray">
                                INSURANCE: ₦{{number_format($loan->insurance, 2)}}
                            </small>
                        </div>
                        <div class="d-inline-block">
                            <div class="bg-success px-4 py-2 rounded">
                                <i class="icon-note text-white icon-sm"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
       
        <div class="col-md-4 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-0">Disbursal Amount</h4>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-inline-block pt-3">
                            <div class="d-flex">
                                <h2 class="mb-0"> ₦{{$loan->disbursal_amount ? number_format($loan->disbursal_amount, 2) : number_format($loan->disbursalAmount(), 2)}}</h2>
                                <div class="d-none d-md-flex align-items-center ml-2">
                                    <!--<i class="mdi mdi-clock text-muted"></i>-->
                                    <!--<small class=" ml-1 mb-0">Updated: 9:10am</small>-->
                                </div>
                            </div>
                            <small class="text-gray">
                                INSURANCE: ₦{{number_format($loan->insurance, 2)}}
                            </small>
                        </div>
                        <div class="d-inline-block">
                            <div class="bg-success px-4 py-2 rounded">
                                <i class="icon-note text-white icon-sm"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    

        <div class="col-md-4 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-0">Loan Tenure</h4>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-inline-block pt-3">
                            <div class="d-flex">
                                <h2 class="mb-0">{{$loan->due_date->diffInMonths($loan->created_at)}}</h2>
                                <div class="d-none d-md-flex align-items-center ml-2">
                                    <!--<i class="mdi mdi-clock text-muted"></i>-->
                                    <!--<small class=" ml-1 mb-0">Updated: 05:42pm</small>-->
                                </div>
                            </div>
                            <small class="text-gray">
                                Months
                            </small>
                        </div>
                        <div class="d-inline-block">
                            <div class="bg-info px-4 py-2 rounded">
                                <i class="icon-calendar text-white icon-sm"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-0">Date Collected</h4>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-inline-block pt-3">
                            <div class="d-flex">
                                <h2 class="mb-0">{{$loan->created_at->toDateString()}}</h2>
                                <div class="d-none d-md-flex align-items-center ml-2">
                                    <!--<i class="mdi mdi-clock text-muted"></i>-->
                                    <!--<small class=" ml-1 mb-0">Updated: 9:10am</small>-->
                                </div>
                            </div>
                            <small class="text-gray">
                                &nbsp;
                            </small>
                        </div>
                        <div class="d-inline-block">
                            <div class="bg-primary px-4 py-2 rounded">
                                <i class="icon-arrow-right-circle text-white icon-sm"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-0">Due Date</h4>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-inline-block pt-3">
                            <div class="d-flex">
                                <h2 class="mb-0">{{$loan->due_date->toDateString()}}</h2>
                                <div class="d-none d-md-flex align-items-center ml-2">
                                    <!--<i class="mdi mdi-clock text-muted"></i>-->
                                    <!--<small class=" ml-1 mb-0">Updated: 9:10am</small>-->
                                </div>
                            </div>
                            <small class="text-gray">
                                &nbsp;
                            </small>
                        </div>
                        <div class="d-inline-block">
                            <div class="bg-warning px-4 py-2 rounded">
                                <i class="icon-badge text-white icon-sm"></i>
                            </div>
                        </div>
                    </div>
                    {{-- <button class="btn btn-warning btn-xs" data-toggle="modal" data-target="#backDateDueDate">
                        <i class="fa fa-calendar"></i> Change Due Date
                    </button> --}}
                </div>
            </div>
        </div>

        <div class="col-md-4 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-0">Payment hub</h4>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-inline-block pt-3">
                            <div class="d-flex">
                                <h2 class="mb-0"> ₦{{number_format($loan->user->masked_loan_wallet, 2)}}</h2>
                                <div class="d-none d-md-flex align-items-center ml-2">
                                    <!-- <i class="mdi mdi-clock text-muted"></i>
                                    <small class=" ml-1 mb-0">Updated: 9:10am</small> -->
                                </div>
                            </div>
                            <small class="text-gray">
                                Loan Wallet: ₦{{number_format($loan->user->loan_wallet, 2)}}
                            </small>
                        </div>
                        <div class="d-inline-block">
                            <div class="bg-warning px-4 py-2 rounded">
                                <i class="icon-badge text-white icon-sm"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title bold">CUSTOMER DATA</h4>
                    <p>Information about the borrower</p>
                    @php($user = $loan->user)
                    <div class="">
                        <p>Name: <strong>{{ $user->name }}</strong></p>
                        <p>ID: <strong>{{ $user->reference }}</strong></p>

                        <p>Bank Name: <strong> {{ optional($user->bank)->bank_name ?? 'N/A'}}</strong></p>
                        <p>Account Number: <strong>{{ optional($user->bank)->account_number ?? 'N/A'}}</strong></p>

                        <p>Pay Roll ID: <strong>{{optional($user->employments->first())->payroll_id ?? 'N/A'}}</strong>
                        </p>


                        <p>
                            <a class="badge badge-primary"
                                href="{{route('staff.accounts.view', ['user' => $user->reference])}}">More Details...</a>
                        </p>
                    </div>
                    <br />
                    <h4 class="card-title bold">LOAN DOCUMENTS</h4>
                    <p>User uploaded documents will show up here</p>
                    <div>
                        @if ($loan->collection_documents)
                            @php($documents = json_decode($loan->collection_documents))


                            @foreach($documents as $name => $document)
                            <a href="{{ asset(Storage::url($document))}}" target="_blank" class="btn btn-primary btn-xs">
                                {{ ucwords(str_replace("_", " ", $name)) }}
                            </a>
                            @endforeach
                        @else
                            <p class="badge badge-dark">No documents available</p>
                        @endif

                        @if($loan->status == 2)
                            <a class="btn btn-primary btn-sm" href="{{route('view.loan.fulfillment-doc',['reference'=>$loan->reference])}}" target="_blank">Loan Fulfillment Doc</a>   
                        @endif

                        
                    </div>
                    <br />
                    @if($loan->is_top_up)
                    <h4 class="card-title bols"><a
                            href="{{route('admin.loans.view', ['reference' => $loan->top_up_loan_reference])}}"
                            class="btn btn-outline-primary">View Previous loan</a></h4>
                    @endif
                    <h4 class="card-title bold">ADMIN ACTIONS</h4>
                    <p>Actions you can perform on the loan at each stage shows up here</p>
                    <br />
                    <div>
                        @component('components.staff-loan-actions', ['loan' => $loan])
                        @endcomponent
                    </div>

                </div>
                <br />
            </div>
        </div>
    </div>
    <br />

    @if($loan->is_penalized)
        @component('components.penalty_schedule_others', ['loan'=> $loan])
        @endcomponent
    @endif
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Loan Repayments</h4>
            <div class="row">
                @if($loan->repaymentPlans->isNotEmpty() && $loan->repaymentPlans->first()->is_new )
                <div class="col-12 table-responsive">
                    <caption>EMI: {{number_format($loan->repaymentPlans->last()->emi,2)}}</caption>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                @if ($staff->manages('repayments'))
                                <th>Buffer Status</th>
                                @endif

                                <th>Total Amount</th>
                                <th>
                                    Collected Date
                                </th>
                                <th>Amount Paid</th>
                                <th>Due Date</th>
                                <th>Payment Proof</th>
                                <th>Payment Method</th>
                                <th>Status</th>
                                @if ($staff->manages('approve_repayment'))
                                    <th>Action</th>
                                @endif

                            </tr>
                        </thead>
                        <tbody>
                            @php($i = 1)
                            @foreach ($loan->repaymentPlans as $plan)
                            <tr>
                                <td>{{$i}}</td>
                                @if ($staff->manages('repayments'))
                                <td><buffer-status :buffers="{{$plan->buffers}}"></buffer-status></td>
                                @endif

                                <td>{{number_format($plan->totalAmount,2)}}</td>



                                <td>{{$plan->date_paid}}</td>
                                <td>@if($plan->status == 1)
                                    {{number_format($plan->paid_amount,2)}}
                                    @endif
                                </td>
                                <td>{{$plan->payday}}</td>
                                @if($plan->payment_proof)
                                <td><a target="_blank" href="{{Storage::url($plan->payment_proof)}}">View</a></td>
                                @else
                                <td><a target="_blank" href="{{$plan->payment_proof}}">No proof</a></td>
                                @endif
                                <td>
                                    @if($staff->manages('approve_repayment') && $plan->status == 0)
                                
                                        @if(!$plan->collection_mode)
                                            <select id="select-confirm-method" class="form-control" style="width:auto;" onchange="attachCollectionMethod(event, {{$plan->id}})">
                                                <option value="Cash">Cash</option>
                                                <option value="Cheque">Cheque</option>
                                                <option value="Deposit">Deposit</option>
                                                <option value="DDAS">DDAS</option>
                                                <option value="Transfer">Transfer</option>
                                                <option value="Remita">Remita</option>
                                                <option value="Paystack">Paystack</option>
                                                <option value="Set-off">Set-off</option>
                                                <option value="RavePay">RavePay</option>
                                            </select>
                                        @else
                                            {{$plan->collection_mode}}
                                        @endif
                                    @else
                                        {{$plan->collection_mode}}
                                    @endif
                                    
                                </td>
                                <td>
                                    @if($plan->status)
                                    <a class="btn btn-xs btn-block btn-success" href="#">Paid</a>
                                    @else
                                    <a class="btn btn-xs btn-block btn-warning" href="#">Not Paid</a>
                                    @endif
                                </td>
                                @if ($staff->manages('approve_repayment'))
                                <td>
                                    @if($plan->status == true)
                                    <button class="btn btn-warning" onclick="var status = confirm('Are you sure?'); if(status == true){document.getElementById('unconfirm-repayment{{$plan->id}}').submit()}">unconfirm</button>
                                    @else
                                    <button class="btn btn-primary" onclick="var status = confirm('Are you sure?'); if(status == true){document.getElementById('confirm-repayment{{$plan->id}}').submit()}">Confirm</button>
                                    @endif
                                </td>
                                @endif
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

                                <th>Total Amount</th>
                                <th>Due Date <br>
                                    <a  class=" mt-2 badge badge-sm badge-danger" href="{{ route('admin.loans.back_date_paydays', [$loan->id, 'backdate'] ) }}"><i class="fa fa-edit  " data-toggle="modal"
                                   data-target="#backDateSingleDueDate"></i> <small>Back Date</small> </a>
                                       <br>
                                   <a  class=" mt-2 badge badge-sm badge-warning" href="{{ route('admin.loans.back_date_paydays', [$loan->id, 'postdate'] ) }}"><i class="fa fa-edit  " data-toggle="modal"
                                       data-target="#backDateSingleDueDate"></i> <small>Post Date</small> </a> </th>
                               <th>Balance</th>
                                <th>
                                    Collected Date
                                </th>
                                <th>Payment Proof</th>
                                <th>Payment Method</th>
                                <th>Status</th>


                            </tr>
                        </thead>
                        <tbody>
                            @forelse($loan->repaymentPlans as $plan)
                            <tr>
                                <td>{{$loop->iteration}}</td>

                                <td>₦{{ number_format($plan->totalAmount, 2)}}</td>
                                <td>{{$plan->payday}}
                                    {{-- <i class="fa fa-edit text-primary  " data-toggle="modal"
                                    data-target="#backDateSingleDueDate{{ $plan->id }}"></i> --}}
                                </td>

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
                                <td>
                                    @if($staff->manages('approve_repayment') && $plan->status == 0)
                                
                                        @if(!$plan->collection_mode)
                                            <select id="select-confirm-method" class="form-control" style="width:auto;" onchange="attachCollectionMethod(event, {{$plan->id}})">
                                                <option value="Cash">Cash</option>
                                                <option value="Cheque">Cheque</option>
                                                <option value="Deposit">Deposit</option>
                                                <option value="DDAS">DDAS</option>
                                                <option value="Transfer">Transfer</option>
                                                <option value="Remita">Remita</option>
                                                <option value="Paystack">Paystack</option>
                                                <option value="Set-off">Set-off</option>
                                                <option value="RavePay">RavePay</option>
                                            </select>
                                        @else
                                            {{$plan->collection_mode}}
                                        @endif
                                    @else
                                        {{$plan->collection_mode}}
                                    @endif
                                    
                                </td>
                                <td>
                                    @if($plan->status)
                                    <a class="btn btn-xs btn-block btn-success" href="#">Paid</a>
                                    @else
                                    <a class="btn btn-xs btn-block btn-warning" href="#">Not Paid</a>
                                    @endif
                                </td>


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
            </div>

            @foreach($loan->repaymentPlans as $plan)
                <form method="post" id="confirm-repayment{{$plan->id}}"
                    action="{{route('staff.repayment.confirm',$plan)}}">
                    {{ csrf_field() }}
                    <input type="hidden" name="collection_method" value="Cash"
                        id="confirm-collection-method-{{$plan->id}}">
                </form>
            @endforeach
            @foreach($loan->repaymentPlans as $plan)
                <form method="post" id="unconfirm-repayment{{$plan->id}}"
                    action="{{route('staff.repayment.unconfirm',$plan)}}">
                    {{ csrf_field() }}
                </form>
            @endforeach
        </div>
    </div>
   

    <br><br>
    @component('components.loan_note', ['notes'=> $loan->notes, 'loan'=>$loan, 'user'=> Auth::guard('staff')->user()])
    @endcomponent





</div>

<div class="modal fade" id="backDateDueDate" tabindex="-1" role="dialog"
aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Change Due Date</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="form">
                <form action="{{ route('admin.loans.back_date_due_date') }}" method="post">@csrf
                    <div class="">
                        <div class="form-group col-md-7">
                            <label for="date">Change Date</label>
                            <input type="hidden" name="loan_id" value="{{ $loan->id }}">
                            <input type="date" name="new_date"
                                value="{{ $loan->due_date->toDateString() }}" class="form-control">
                        </div>
                        <div class="form-group col-md-5">
                            <button class="btn btn-sm btn-success">Update</button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
        </div>
    </div>
</div>
</div>











@forelse($loan->repaymentPlans as $plan)
    <div class="modal fade" id="backDateSingleDueDate{{ $plan->id }}" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Change Single Due Date</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form">
                        <form action="{{ route('admin.loans.change_single_due_date') }}" method="post">@csrf
                            <div class="">
                                <div class="form-group col-md-7">
                                    <label for="date">Change Date</label>
                                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                    <input type="date" name="new_date" value="{{ $plan->payday}}"
                                        class="form-control">
                                </div>
                                <div class="form-group col-md-5">
                                    <button class="btn btn-sm btn-success">Update</button>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
                </div>
            </div>
        </div>
    </div>

@empty
    <tr>
        <td colspan="8" class="text-center">Repayments not found</td>
    </tr>
@endforelse
@endsection

@section('page-js')
<script>
var attachCollectionMethod = function(evt, id) {

    var value = evt.target.value;
    document.getElementById('confirm-collection-method-' + id).value = value;
}
</script>
<script src="{{asset('assets/js/data-table.js')}}"></script>
@endsection