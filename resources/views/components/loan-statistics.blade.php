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
                <h4 class="card-title mb-0">Loan Tenure</h4>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-inline-block pt-3">
                        <div class="d-flex">
                            <h2 class="mb-0">{{$loan?->due_date?->diffInMonths($loan->created_at)}}</h2>
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
                            <h2 class="mb-0">{{$loan->created_at}}</h2>
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
                            <h2 class="mb-0">{{$loan->due_date}}</h2>
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
</div>