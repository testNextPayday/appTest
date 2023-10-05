@extends('layouts.staff-new')

@section('content')
<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">
                Borrower Accounts
            </h4>
            <div>
                <div style="display:inline-block">
                    <form action="" method="get">
                        <input class="form-control" name="q" placeholder="Enter search term" value="{{$searchTerm}}" required/>
                    </form>
                </div>
                <a href="{{route('staff.accounts.new')}}" class="btn btn-info btn-sm">
                    <i class="fa fa-plus"></i>
                    Add new
                </a>
            </div>
        </div>
    </div>
    
    
    @include('layouts.shared.error-display')
    
    <div style="min-height: 70vh;">
    <div class="row">
    @if(count($users))
        @foreach($users as $user)
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card text-center">
                    <div class="card-body">
                        <img src="{{ $user->avatar }}" class="img-lg rounded-circle mb-2" alt="profile image" />
                        <h4>{{ $user->name }}</h4>
                        <p class="text-muted">{{ strtoupper($user->reference) }}</p>
                        <p class="text-muted">Payroll ID{{ strtoupper(optional($user->employments->first())->payroll_id) }}</p>
                        @if($user->is_active)
                            <button class="btn btn-sm btn-success mt-3 mb-4">Active</button>
                        @else
                            <button class="btn btn-sm btn-danger mt-3 mb-4">Inactive</button>
                        @endif
                        <a class="btn btn-info btn-sm mt-3 mb-4" href="{{route('staff.accounts.view', ['user' => $user->reference])}}">View</a>
                        
                        <div class="border-top pt-3">
                            <div class="row">
                              <div class="col-6">
                                <h6>₦ {{ number_format($user->wallet, 2) }}</h6>
                                <p>Wallet Balance</p>
                              </div>
                              <div class="col-6">
                                <h6>₦ {{ number_format($user->escrow, 2) }}</h6>
                                <p>Escrow Balance</p>
                              </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        <div class="col-12 grid-margin">
            {{$users->links('layouts.pagination.default')}} 
        </div>
        
    @else
        <h4 class="text-center">No accounts found</h4>
    @endif
    </div>
    </div>

    <div class="modal fade" id="newUser" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add new user</h4><br>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
          
                <form method="post" action="{{route('staff.accounts.store')}}">
                {{csrf_field()}}
                <div class="modal-body">
                    <div class="input-group mb-3">
                        <span class="input-group-addon"><i class="icon-question"></i></span>
                        <select name="is_company" class="form-control" required>
                            <option value="1">Company</option>                                
                            <option value="0">Individual</option>                                
                        </select>
                    </div>
                    
                    <div class="input-group mb-3">
                        <span class="input-group-addon"><i class="icon-user"></i></span>
                        <input type="text" name="name" class="form-control" required placeholder="Company/Individual Name">
                    </div>
                    
                    <div class="input-group mb-3">
                        <span class="input-group-addon">@</span>
                        <input type="email" name="email" class="form-control" required placeholder="Email">
                    </div>
                    
                    <div class="input-group mb-3">
                        <span class="input-group-addon"><i class="icon-phone"></i></span>
                        <input type="number" name="phone" class="form-control" required placeholder="Phone">
                    </div>
                    
                </div>
          
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Add</button>
                </div>
                </form>
          
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
@endsection

@section('page-js')
@endsection