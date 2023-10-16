@extends('layouts.admin-new')

@section('content')
    <div class="content-wrapper">
        <div class="row m-4">
            <div class="col-12 d-flex align-items-center justify-content-between">
                <h4 class="page-title">Loan {{ $loan->reference }}</h4>
                <div>
                    @if ($loan->is_penalized)
                        <span class="badge badge-danger">
                            <i class="fa fa-exclamation-triangle"></i>
                            Penalised
                        </span>
                    @endif


                    @component('components.admin-loan-status', ['loan' => $loan])
                    @endcomponent

                    @if ($loan->canBeSweptCard() || isset($loan->mandateId))
                        <div class="dropdown" style="display:inline-block">
                            <a href="#" style="color:white;text-decoration:none" class="dropdown-toggle btn bg-success"
                                data-toggle="dropdown" aria-expanded="false" aria-haspopup="true"
                                id="paystack_sweeps">Collection Sweeps</a>

                            <div class="dropdown-menu" aria-labelledby="paystack_sweeps">

                                @if ($loan->canBeSweptCard())
                                    <a class="dropdown-item"
                                        href="{{ route('admin.loans.auto-sweep-toggle', ['loan' => $loan->reference]) }}">{{ $loan->auto_sweeping == 1 ? 'Disable Card Auto Sweep' : 'Enable Card Auto Sweep' }}</a>
                                    <a href="{{ route('admin.loans.pause-sweep-toggle', ['loan' => $loan->reference]) }}"
                                        class="dropdown-item">{{ $loan->sweep_enabled ? 'Disable Manual Card Sweep' : 'Enable Manual Card Sweep' }}</a>
                                @endif

                                @if (isset($loan->mandateId))
                                    <a href="{{ route('admin.loans.remita-sweep-toggle', ['loan' => $loan->reference]) }}"
                                        class="dropdown-item">{{ $loan->remita_active ? 'Disable Remita Sweep' : 'Enable Remita Sweep' }}</a>
                                @endif
                            </div>
                        </div>
                    @endif

                    @component('components.loan-statement-button', ['loan' => $loan])
                    @endcomponent



                    @if ($loan->canSettle())
                        <button class="btn btn-primary">
                            <a href="{{ route('admin.settlement.preview', ['reference' => $loan->reference]) }}"
                                target="_blank" style="color:white"> Settle Loan</a>
                        </button>
                    @endif

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
                                    <h2 class="mb-0">₦{{ number_format($loan->amount, 0) }}</h2>
                                    <div class="d-none d-md-flex align-items-center ml-2">
                                        <!--<i class="mdi mdi-clock text-muted"></i>-->
                                        <!--<small class=" ml-1 mb-0">Updated: 9:10am</small>-->
                                    </div>
                                </div>
                                <small class="text-gray">
                                    INSURANCE: ₦{{ number_format($loan->insurance, 2) }}
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
                        <h4 class="card-title mb-0">Disbursal Amount </h4>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-inline-block pt-3">
                                <div class="d-flex">



                                    <h2 class="mb-0">
                                        ₦{{ $loan->disbursal_amount ? number_format($loan->disbursal_amount, 2) : number_format($loan->disbursalAmount(), 2) }}
                                    </h2>
                                    <div class="d-none d-md-flex align-items-center ml-2">
                                        <!--<i class="mdi mdi-clock text-muted"></i>-->
                                        <!--<small class=" ml-1 mb-0">Updated: 9:10am</small>-->
                                    </div>
                                </div>
                                <small class="text-gray">
                                    INSURANCE: ₦{{ number_format($loan->insurance, 2) }}
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
                                    <h2 class="mb-0">{{ $loan->due_date->diffInMonths($loan->created_at) }}</h2>
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
                                    <h2 class="mb-0">{{ $loan->created_at }}</h2>
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
                        {{-- <button class="btn btn-primary btn-xs" data-toggle="modal" data-target="#backDateCollectionDate">
                            <i class="fa fa-calendar"></i> Change Collected Date
                        </button> --}}
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
                                    <h2 class="mb-0">{{ $loan->due_date }}</h2>
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
                                    <h2 class="mb-0"> ₦{{ number_format($maturity_penalty, 2) }}</h2>
                                    {{-- <h2 class="mb-0"> ₦{{ number_format($loan->user->masked_loan_wallet - array_last($excesspenalties), 2) }}</h2> --}}
                                    <div class="d-none d-md-flex align-items-center ml-2">
                                        <!-- <i class="mdi mdi-clock text-muted"></i>
                                                <small class=" ml-1 mb-0">Updated: 9:10am</small> -->
                                    </div>
                                </div>
                                <small class="text-gray">
                                    Loan Wallet: ₦{{ number_format($loan->user->loan_wallet, 2) }}
                                </small>
                            </div>
                            <div class="d-inline-block">
                                <div class="bg-warning px-4 py-2 rounded">
                                    <i class="icon-badge text-white icon-sm"></i>
                                </div>
                            </div>
                        </div>

                        <div>
                            @component('components.top_wallet', ['loan' => $loan])
                            @endcomponent
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
                        @if (isset($user))
                            <div class="">
                                <p>Name: <strong>{{ $user->name }}</strong></p>
                                <p>ID: <strong>{{ $user->reference }}</strong></p>
                                <p>Bank Name: <strong> {{ optional($user->bank)->bank_name ?? 'N/A' }}</strong></p>
                                <p>Account Number: <strong>{{ optional($user->bank)->account_number ?? 'N/A' }}</strong>
                                </p>
                                <p>Pay Roll ID:
                                    <strong>{{ optional($user->employments->first())->payroll_id ?? 'N/A' }}</strong>
                                </p>

                                <p>
                                    <a class="badge badge-primary"
                                        href="{{ route('admin.users.view', ['user' => $user->reference]) }}">More
                                        Details...</a>
                                </p>
                            </div>
                            <br />
                            <h4 class="card-title bold">LOAN DOCUMENTS</h4>
                            <p>User uploaded documents will show up here</p>
                        @endif
                        <div>
                            @if ($loan->collection_documents)
                                @php($documents = json_decode($loan->collection_documents))


                                @foreach ($documents as $name => $document)
                                    <a href="{{ asset(Storage::url($document)) }}" target="_blank"
                                        class="btn btn-primary btn-xs">
                                        {{ ucwords(str_replace('_', ' ', $name)) }}
                                    </a>
                                @endforeach
                            @else
                                <p class="badge badge-dark">No documents available</p>
                            @endif

                            @if ($loan->status == 2)
                                <a class="btn btn-primary btn-sm"
                                    href="{{ route('view.loan.fulfillment-doc', ['reference' => $loan->reference]) }}"
                                    target="_blank">Loan Fulfillment Doc</a>
                            @endif

                        </div>
                        <br />
                        @if ($loan->is_top_up)
                            <h4 class="card-title bols"><a
                                    href="{{ route('admin.loans.view', ['reference' => $loan->top_up_loan_reference]) }}"
                                    class="btn btn-outline-primary">View Previous loan</a></h4>
                        @endif
                        <h4 class="card-title bold">ADMIN ACTIONS</h4>
                        <p>Actions you can perform on the loan at each stage shows up here</p>
                        <br />
                        <div>
                            @component('components.admin-loan-actions', ['loan' => $loan])
                            @endcomponent
                        </div>

                    </div>
                    <br />
                </div>
            </div>
        </div>
        <br />



        @if ($loan->is_penalized)
    </div>
    @component('components.penalty_schedule', ['loan' => $loan])
    @endcomponent
    <div class="card">
        {{-- <div class="card-body">
            <h4 class="text-center">Total: ₦{{ number_format($excessBalance, 2) }}</h4>
        </div> --}}
        @endif


        @if ($loan->is_penalized == 1 && count($excesspenalties) > 0)
            <div class="card-body">
                <h4 class="text-danger">Maturity Penalties </h4>
                <table class="table table-responsive">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Description</th>
                            <th>Date</th>
                            <th>Dr.</th>
                            <th>Cr.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($excesspenalties as $penalty)
                           
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>Total penalty due as at {{ Carbon\Carbon::parse( Carbon\Carbon::parse($loan->created_at)->addMonths($loan->duration))->addMonths($loop->iteration)  }}</td>
                                    <td>{{ Carbon\Carbon::parse( Carbon\Carbon::parse($loan->created_at)->addMonths($loan->duration))->addMonths($loop->iteration)  }}</td>
                                    <td>₦{{ number_format($penalty, 2) }}</td>
                                    <td>₦0.00</td>
                                </tr>
                        @endforeach
                        
                    </tbody>
                </table>
            </div>
        @endif




        <br /><br /><br />
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Loan Repayments </h4>
                <div class="row">

                    @if ($loan->repaymentPlans->isNotEmpty() && $loan->repaymentPlans->first()->is_new)
                        @component('components.repayment_plans.armotised', ['loan' => $loan])
                        @endcomponent
                    @else
                        @component('components.repayment_plans.old', ['loan' => $loan])
                        @endcomponent
                    @endif
                    @foreach ($loan->repaymentPlans as $plan)
                        <form method="post" id="confirm-repayment{{ $plan->id }}"
                            action="{{ route('admin.repayment.confirm', $plan) }}">
                            {{ csrf_field() }}
                            <input type="hidden" name="collection_method" value="Cash"
                                id="confirm-collection-method-{{ $plan->id }}">
                        </form>
                    @endforeach
                    @foreach ($loan->repaymentPlans as $plan)
                        <form method="post" id="unconfirm-repayment{{ $plan->id }}"
                            action="{{ route('admin.repayment.unconfirm', $plan) }}">
                            {{ csrf_field() }}
                        </form>
                    @endforeach
                    @foreach ($loan->repaymentPlans as $plan)
                        <form method="post" id="delete-repayment{{ $plan->id }}"
                            action="{{ route('admin.repayment.delete', $plan) }}">
                            {{ csrf_field() }}
                            {{ method_field('DELETE') }}
                        </form>
                    @endforeach


                </div>
            </div>
        </div>




        <br><br>
        @component('components.loan_note', [
            'notes' => $loan->notes,
            'loan' => $loan,
            'user' => Auth::guard('admin')->user(),
        ])
        @endcomponent



        <br><br>
        @component('components.loan-transactions', ['loan' => $loan])
        @endcomponent

        <div class="card">

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h4>Funds for this Request ({{ $loan->loanRequest->funds()->count() }})</h4>
                            <table class="table table-responsive-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Investor Reference</th>
                                        <th>Investor Name</th>
                                        <th class="text-center">Offer</th>
                                        <th>Fund Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($loan->loanRequest->funds()->latest()->get() as $fund)
                                        <tr>
                                            <td>
                                                <div>
                                                    {{ $fund->investor->reference }}
                                                </div>
                                            </td>
                                            <td>
                                                <div>{{ $fund->investor->name }}</div>
                                            </td>
                                            <td class="text-center">
                                                <div class="text-muted">
                                                    ₦ {{ $fund->amount }} <span>({{ $fund->percentage }}%)</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="small text-muted">
                                                    {{ $fund->created_at }}
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">
                                                There are no funds for this Request
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!--/.col-->
            </div>
        </div>




        <div class="modal fade" id="backDateCollectionDate" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Change Collected Date</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form">
                            <form action="{{ route('admin.loans.back_date') }}" method="post">@csrf
                                <div class="">
                                    <div class="form-group col-md-7">
                                        <label for="date">Change Date</label>
                                        <input type="hidden" name="loan_id" value="{{ $loan->id }}">
                                        <input type="date" name="new_date"
                                            value="{{ $loan->created_at }}" class="form-control">
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
                                            value="{{ $loan->due_date}}" class="form-control">
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



    </div>
@endsection

@section('page-js')
    <script>
        var attachCollectionMethod = function(evt, id) {

            var value = evt.target.value;
            document.getElementById('confirm-collection-method-' + id).value = value;
        }
    </script>
    <script src="{{ asset('assets/js/data-table.js') }}"></script>
    <script src="{{ asset('assets/js/data-table.js') }}"></script>
    <script type="text/javascript" src="//cdn.datatables.net/plug-ins/1.10.19/api/sum().js"></script>
@endsection
