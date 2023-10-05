@extends('layouts.admin-new')
@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
    <li class="breadcrumb-item">Home</li>
        @if($investor->is_active == 1)
            <li class="breadcrumb-item"><a href="{{route('admin.investors.active')}}">Investors</a></li>
        @else
            <li class="breadcrumb-item"><a href="{{route('admin.investors.inactive')}}">Investors</a></li>
        @endif
        <li class="breadcrumb-item active">{{$investor->reference}}</li>
    </ol>

    <div class="container-fluid">
        
        <div class="animated fadeIn">
            @if ($errors->any())
                <div class="row justify-content-center">
                    <div class="col-sm-6">
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        
                        <div class="card-header">                            
                            Investor Summary
                            <span class="pull-right">
                                @if($investor->is_active)
                                
                                <span class="text-success">Active</span>
                                @else
                                <span class="text-danger">Inactive</span>
                                @endif
                            </span>
                        </div>
                        <div class="card-body">
                            
                            @component('components.investor-stats', ['investor' => $investor])
                            @endcomponent

                            @if ($investor->role == 2)
                            <div class="row">
                                    <div class="col-12 card-statistics">
                                        <div class="row">
                                            <div class="col-12 col-sm-6 col-md-3 grid-margin stretch-card card-tile">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between pb-2">
                                                            <h5>₦ {{ number_format($investor->wallet)}}</h5>
                                                            <i class="icon-wallet"></i>
                                                        </div>
                                                        <div class="d-flex justify-content-between">
                                                            <!--<p class="text-muted">Avg. Session</p>-->
                                                            <p class="text-muted">Wallet Balance</p>
                                                        </div>
                                                        <div class="progress progress-md">
                                                            <div class="progress-bar bg-info w-100" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6 col-md-3 grid-margin stretch-card card-tile">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between pb-2">
                                                            <h5>₦ {{ number_format($investor->vault) }}</h5>
                                                            <i class="icon-wallet"></i>
                                                        </div>
                                                        <div class="d-flex justify-content-between">
                                                            <!--<p class="text-muted">Avg. Session</p>-->
                                                            <p class="text-muted">Vault Balance</p>
                                                        </div>
                                                        <div class="progress progress-md">
                                                            <div class="progress-bar bg-info w-100" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-12 col-sm-6 col-md-3 grid-margin stretch-card card-tile">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between pb-2">
                                                            <h5>₦ {{ number_format($investor->promissoryPortfolioSize(), 2) }}</h5>
                                                        
                                                            <i class="icon-briefcase"></i>
                                                        </div>
                                                        <div class="d-flex justify-content-between">
                                                            <p class="text-muted">Portfolio Size</p>
                                                            <!--<p class="text-muted">max: 143</p>-->
                                                        </div>
                                                        <div class="progress progress-md">
                                                            <div class="progress-bar bg-warning w-100" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12 col-sm-6 col-md-3 grid-margin stretch-card card-tile">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between pb-2">
                                                            ₦ {{ number_format($investor->promissoryTax(), 2) }}
                                                            <i class="icon-share"></i>
                                                        </div>
                                                        <div class="d-flex justify-content-between">
                                                            <!--<p class="text-muted">Avg. Session</p>-->
                                                            <p class="text-muted">Tax Paid</p>
                                                        </div>
                                                        <div class="progress progress-md">
                                                            <div class="progress-bar bg-danger w-100" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 card-statistics">
                                        <div class="row">
                                            <div class="col-12 col-sm-6 col-md-3 grid-margin stretch-card card-tile">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between pb-2">
                                                            <h5>₦ {{ number_format($investor->promissoryWithdrawals(), 2) }}</h5>
                                                        
                                                            <i class="icon-briefcase"></i>
                                                        </div>
                                                        <div class="d-flex justify-content-between">
                                                            <p class="text-muted">Withdrawals Made</p>
                                                            <!--<p class="text-muted">max: 143</p>-->
                                                        </div>
                                                        <div class="progress progress-md">
                                                            <div class="progress-bar bg-warning w-100" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            
                                        </div>
                                    </div>
                                </div>

                                
                            @endif
                        </div>
                        
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-sm-10 text-center">
                               
                                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#assignCommission">
                                        <i class="fa fa-link"></i>
                                        Assign Commission
                                    </button>
                                    @if(optional($investor->getReferrer())->name)
                                    
                                        <form method="POST" class="d-inline" action="{{route('admin.investors.unassign-commission', ['investor'=> $investor->reference])}}">

                                        {{csrf_field()}}
                                            <button type="submit" class="btn btn-info btn-sm">
                                                <i class="fa fa-link"></i>
                                                Unassign {{$investor->referrer->name}}
                                            </button>
                                        </form>
                                    @endif
                                    @if(!$investor->staff_id)                                 
                                    
                                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#assignStaff">
                                        <i class="fa fa-link"></i>
                                        Assign Staff
                                    </button>
                                    @else
                                    <a href="{{route('admin.staff.view', ['reference' => App\Models\Staff::find($investor->staff_id)->reference])}}"
                                        class="btn btn-sm btn-primary">
                                        <i class="icon-eye"></i> 
                                        View staff
                                    </a>
                                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#assignStaff">
                                        <i class="fa fa-link"></i>
                                        Reassign
                                    </button>
                                    @endif
                                    @if($investor->is_active)
                                    <a href="{{route('admin.investors.toggle', ['investor' => $investor->reference])}}" 
                                        onclick="return confirm('Are you sure you want to disable this investor?');"
                                        class="btn btn-sm btn-warning"><i class="icon-close"></i> Disable</a>
                                    @else
                                    <a href="{{route('admin.investors.toggle', ['investor' => $investor->reference])}}" 
                                        onclick="return confirm('Are you sure you want to enable this investor?');"
                                        class="btn btn-sm btn-success"><i class="icon-check"></i> Enable</a>
                                    @endif
                                </div>
                                <br><br>
                            </div>
                            <div class="row">
                                @if ($investor->role == 1)
                                    <div class="col-sm-8">
                                        <form method="post"  class="row" action="{{route('admin.investors.update', ['investor' => $investor->reference])}}">
                                            {{ csrf_field() }}
                                            <div class="col-md-6">
                                                <label><strong>Investor Commission Rate</strong></label>
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control" placeholder="Investor's commission rate"
                                                        aria-label="Investor's commission rate" aria-describedby="basic-addon2"
                                                        value="{{$investor->commission_rate}}" name="commission_rate">

                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <label><strong>Investor Tax Rate</strong></label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" placeholder="Investor's tax rate"
                                                        aria-label="Investor's tax rate" aria-describedby="basic-addon2"
                                                        value="{{$investor->tax_rate}}" name="tax_rate">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <label><strong>Pause Auto Invest (Auto sweep vault)</strong></label>
                                                <div class="input-group">
                                                    <select name="auto_invest" class="form-control">
                                                        <option value="1" {{$investor->auto_invest == 1 ? 'selected' : ''}}>No</option>
                                                        <option value="0" {{$investor->auto_invest == 0 ? 'selected' : ''}}>Yes</option>
                                                    </select>
                                                </div>
                                            </div>


                                            <div class="col-md-6">
                                                <label><strong>Management Fee</strong></label>
                                                <div class="input-group">
                                                    <select name="takes_mgt" class="form-control">
                                                        <option value="1" {{$investor->takes_mgt == 1 ? 'selected' : ''}}>Yes</option>
                                                        <option value="0" {{$investor->takes_mgt == 0 ? 'selected' : ''}}>No</option>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                            <br><br>
                                                <div class="input-group">
                                                    <button class="btn btn-outline-primary" type="submit">Update</button>
                                                </div>
                                            </div>

                                            
                                        </form>
                                        
                                    </div>
                                @endif
                                
                            </div>

                            
                        </div>
                    </div>
                </div>
                <!--/.col-->
            </div>
            @if ($investor->role == 1)

                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3>Investor Statistics</h3>
                            </div>

                            <div class="card-body p-3">
                                <investor-transactions :investor="{{$investor}}" :isadmin=true></investor-statistics>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($investor->role == 2)
                <div class="row">
                    @component('components.admin_promissory_note_table_list', ['promissoryNotes'=> $investor->promissoryNotes])
                    @endcomponent
                </div>
                
            @endif

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            Investor Details
                            <span class="pull-right">
                                @if($investor->is_active)
                                <span class="text-success">Active</span>
                                @else
                                <span class="text-danger">Inactive</span>
                                @endif
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-2 text-center">
                                    <img src="{{$investor->avatar}}" width="100" style="border-radius: 50px" class="img-round img-circle img-thumbnail"/>
                                </div>
                                <div class="col-sm-4">
                                    <h4>Personal Details</h4>
                                    <p>Name: <strong>{{$investor->name}}</strong></p>
                                    <p>Email: <strong>{{$investor->email}}</strong></p>
                                    <p>Phone: <strong>{{$investor->phone}}</strong></p>
                                    <p>Address: <strong>{{$investor->address}}</strong></p>
                                    <p>LGA: <strong>{{$investor->lga}}</strong></p>
                                    <p>City, State: <strong>{{$investor->city}} {{$investor->state}}</strong></p>
                                </div>
                                <div class="col-sm-4">
                                    <h4>Bank Details</h4>
                                    @php
                                        $bank = $investor->banks()->latest()->first();
                                    @endphp
                                    @if($bank)
                                    <p>Bank Name: {{$bank->bank_name}}</p>
                                    <p>Account Number: {{ $bank->account_number}}</p>
                                    @else
                                    <p>No bank details available</p>
                                    @endif
                                </div>
                            </div>
                            <br/>
                        </div>
                        <div class="card-footer text-right">
                            
                        </div>
                    </div>
                </div>
                <!--/.col-->
            </div>
            
            <div class="modal fade" id="assignStaff" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-sm" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Assign staff to investor</h4><br>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                  
                        <form method="post" action="{{route('admin.users.assign-staff')}}">
                        {{csrf_field()}}
                        <div class="modal-body">
                            <input type="hidden" name="type" value="investors"/>
                            <input type="hidden" name="investor_id" value="{{$investor->id}}"/>
                            <label>Select a staff</label>
                            <div class="input-group mb-3">
                                <span class="input-group-addon"><i class="icon-question"></i></span>
                                <select name="staff_id" class="form-control" required>
                                    <?php $validStaff = App\Models\Staff::orderBy('lastname')->get(); ?>
                                    @foreach($validStaff as $staff)
                                        <option value="{{$staff->id}}">{{$staff->lastname}}, {{$staff->firstname}} {{$staff->midname}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                  
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-success">Assign</button>
                        </div>
                        </form>
                  
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>

            <div class="modal fade" id="assignCommission" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Assign Funding Commission To</h4><br>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                  
                        <form method="post" action="{{route('admin.investors.assign-commission', ['investor'=> $investor->reference])}}">
                        {{csrf_field()}}
                        <div class="modal-body">

                            @php
                                $staffs = \App\Models\Staff::all();
                                $affiliates = \App\Models\Affiliate::active()->get();
                                $investors = \App\Models\Investor::all();
                                $referrer = $investor->getReferrer();
                            @endphp

                            @if ($referrer && $referrer->name)
                                <h5 class="text-success">Currently aassigned to {{$referrer->name}} - {{$referrer->toHumanReadable()}}</h5>
                            @endif

                           <assign-investor-commission :staffs="{{$staffs}}" :investors="{{$investors}}" :affiliates="{{$affiliates}}"></assign-investor-commission>

                        </div>
                  
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-success">Assign</button>
                        </div>
                        </form>
                  
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
        </div>

    </div>
    <!-- /.conainer-fluid -->
    
</main>
@endsection

@section('page-js')
@endsection