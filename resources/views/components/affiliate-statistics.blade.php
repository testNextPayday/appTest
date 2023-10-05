<div class="row">
    <div class="col-12 card-statistics">
        <div class="row">
            <div class="col-12 col-sm-4 col-md-3 grid-margin stretch-card card-tile">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between pb-2">
                            <h5>₦{{ number_format($affiliate->wallet, 2) }}</h5>
                            <i class="icon-wallet"></i>
                        </div>
                        <div class="d-flex justify-content-between">
                            <p class="text-muted">Wallet Balance</p>
                        </div>
                        <div class="progress progress-md">
                            <div class="progress-bar bg-dark w-100" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-4 col-md-3 grid-margin stretch-card card-tile">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between pb-2">
                            <h5>{{strtoupper($affiliate->reference)}}</h5>
                            <i class="icon-user"></i>
                        </div>
                        <div class="d-flex justify-content-between">
                            <p class="text-muted">REF Code</p>
                        </div>
                        <div class="progress progress-md">
                            <div class="progress-bar bg-info w-100" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-4 col-md-3 grid-margin stretch-card card-tile">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between pb-2">
                            <h5>{{ $affiliate->borrowers()->count() }}</h5>
                            <i class="icon-badge"></i>
                        </div>
                        <div class="d-flex justify-content-between">
                            <p class="text-muted">Referred Borrowers</p>
                            <p><a href="#" data-toggle="modal" data-target="#affiliate-borrowers">View</a></p>
                            <!--<p class="text-muted">max: 120</p>-->
                        </div>
                        <div class="progress progress-md">
                            <div class="progress-bar bg-warning w-100" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-4 col-md-3 grid-margin stretch-card card-tile">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between pb-2">
                            <h5>{{ $affiliate->investors()->count() }}</h5>
                            <i class="icon-people"></i>
                        </div>
                        <div class="d-flex justify-content-between">
                            <p class="text-muted">Referred Investors</p>
                            <!--<p class="text-muted">max: 54</p>-->
                        </div>
                        <div class="progress progress-md">
                            <div class="progress-bar bg-primary w-100" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-4 col-md-3 grid-margin stretch-card card-tile">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between pb-2">
                            <h5>{{ $affiliate->commission_rate ? "$affiliate->commission_rate %" : 'N/A' }}</h5>
                            <i class="icon-graph"></i>
                        </div>
                        <div class="d-flex justify-content-between">
                            <p class="text-muted">Commission Rate (Borrowers)</p>
                            <!--<p class="text-muted">max: 54</p>-->
                        </div>
                        <div class="progress progress-md">
                            <div class="progress-bar bg-success w-100" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-4 col-md-3 grid-margin stretch-card card-tile">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between pb-2">
                            <h5>{{ $affiliate->commission_rate_investor ? "$affiliate->commission_rate_investor %" : 'N/A' }}</h5>
                            <i class="icon-diamond"></i>
                        </div>
                        <div class="d-flex justify-content-between">
                            <p class="text-muted">Commission Rate (Investors)</p>
                            <!--<p class="text-muted">max: 54</p>-->
                        </div>
                        <div class="progress progress-md">
                            <div class="progress-bar bg-danger w-100" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Reference Link</h4>
                        <p><small>Click to copy your ref link</small></p>
                        <br/>
                        
                        <click-to-copy 
                            :text="'{{ route('register') }}/?rc={{ strtolower($affiliate->reference) }}'">
                        </click-to-copy>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>


<div class="modal fade" id="affiliate-borrowers" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">This Agent Borrowers</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <tbody>
                    @forelse($affiliate->borrowers as $borrower)
                    <tr>
                        <td> <a class="btn btn-primary-outline" href="{{route('admin.users.view',['user'=>$borrower->reference])}}">{{$borrower->name}}</a><br></td>
                    </tr>
                   
                @empty
                   <tr>
                       <td>No borrowers</td>
                   </tr>
               @endforelse
                    </tbody>
                </table>
              
            </div>
           
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>