@php($insurance = 2.5/100 * $loanRequest->amount)
<div class="row">
    <div class="col-md-4 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-0">Request Amount</h4>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-inline-block pt-3">
                        <div class="d-flex">
                            <h2 class="mb-0">₦{{number_format($loanRequest->amount, 0)}}</h2>
                            <div class="d-none d-md-flex align-items-center ml-2">
                              <!--<i class="mdi mdi-clock text-muted"></i>-->
                              <!--<small class=" ml-1 mb-0">Updated: 9:10am</small>-->
                            </div>
                        </div>
                        <small class="text-gray">
                            Insurance: ₦ {{ number_format($insurance, 2) }}
                        </small>
                    </div>
                    <div class="d-inline-block">
                        <div class="bg-info px-4 py-2 rounded">
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
                <h4 class="card-title mb-0">Request Duration</h4>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-inline-block pt-3">
                        <div class="d-flex">
                            <h2 class="mb-0">{{ $loanRequest->duration }}</h2>
                            <div class="d-none d-md-flex align-items-center ml-2">
                                <!--<i class="mdi mdi-clock text-muted"></i>-->
                                <!--<small class=" ml-1 mb-0">Updated: 05:42pm</small>-->
                            </div>
                        </div>
                        <small class="text-gray">
                            {{ $loanRequest->loan_period == 'weekly' ? 'Weeks' : 'Months' }}
                        </small>
                    </div>
                    <div class="d-inline-block">
                        <div class="bg-warning px-4 py-2 rounded">
                            <i class="icon-speedometer text-white icon-sm"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-0">Interest</h4>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-inline-block pt-3">
                        <div class="d-flex">
                            <h2 class="mb-0">{{ $loanRequest->interest_percentage }}%</h2>
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
                            <i class="icon-pie-chart text-white icon-sm"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-0">{{ $loanRequest->loan_period }} Repayment</h4>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-inline-block pt-3">
                        <div class="d-flex">
                            <h2 class="mb-0">₦{{number_format($loanRequest->emi(), 2)}}</h2>
                            <!-- <h2 class="mb-0">₦{{number_format($loanRequest->emi() + $loanRequest->fee(), 2)}}</h2> -->
                            <div class="d-none d-md-flex align-items-center ml-2">
                              <!--<i class="mdi mdi-clock text-muted"></i>-->
                              <!--<small class=" ml-1 mb-0">Updated: 9:10am</small>-->
                            </div>
                        </div>
                        <small class="text-gray">
                            EMI (Potential)
                        </small>
                    </div>
                    <div class="d-inline-block">
                        <div class="bg-success px-4 py-2 rounded">
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
                <h4 class="card-title mb-0">Amount Realized</h4>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-inline-block pt-3">
                        <div class="d-flex">
                            <h2 class="mb-0">₦{{number_format($loanRequest->amountRealized, 0)}}</h2>
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
                            <i class="icon-arrow-left-circle text-white icon-sm"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-0">Percentage Remaining</h4>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-inline-block pt-3">
                        <div class="d-flex">
                            <h2 class="mb-0">{{ $loanRequest->percentage_left }}%</h2>
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
                        <div class="bg-info px-4 py-2 rounded">
                            <i class="icon-compass text-white icon-sm"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-0">Expected Date</h4>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-inline-block pt-3">
                        <div class="d-flex">
                            <h2 class="mb-0">{{$loanRequest->expected_withdrawal_date}}</h2>
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
                        <div class="bg-success px-4 py-2 rounded">
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
                <h4 class="card-title mb-0">Disbursal Amount</h4>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-inline-block pt-3">
                        <div class="d-flex">
                            <h2 class="mb-0">₦{{ number_format($loanRequest->disbursalAmount(),2) }}</h2>
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
            </div>
        </div>
    </div>

    @if(!$loanRequest->upfront_interest)
        <div class="col-md-4 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-0">Loan Fee Charged</h4>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-inline-block pt-3">
                            <div class="d-flex">
                                <h2 class="mb-0">₦{{number_format($loanRequest->success_fee,2)}}</h2>
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
                </div>
            </div>
        </div>
    @else
    <div class="col-md-4 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-0">Interest Charged</h4>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-inline-block pt-3">
                            <div class="d-flex">
                                <h2 class="mb-0">₦{{number_format(optional($loanRequest->investorUpfrontInterest)->total_payment,2)}}</h2>
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
                </div>
            </div>
        </div>
    @endif
</div>