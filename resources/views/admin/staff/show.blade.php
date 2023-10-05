@extends('layouts.admin-new')

@section('content')
<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">
                Staff {{$staff->reference}} &nbsp;
                @if($staff->is_active)
                <span class="text-success">Active</span>
                @else
                <span class="text-danger">Inactive</span>
                @endif
            </h4>
            @if($staff->is_active)
            <a href="{{route('admin.staff.toggle', ['reference' => $staff->reference])}}" onclick="return confirm('Are you sure you want to disable this user?');" class="btn btn-sm btn-warning"><i class="icon-close"></i> Disable</a>
            @else
            <a href="{{route('admin.staff.toggle', ['reference' => $staff->reference])}}" onclick="return confirm('Are you sure you want to enable this user?');" class="btn btn-sm btn-success"><i class="icon-check"></i> Enable</a>
            @endif
        </div>
    </div>

    @include('layouts.shared.error-display')

    <div class="row">
        <div class="col-md-6 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-0">Unique ID</h4>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-inline-block pt-3">
                            <div class="d-flex">
                                <h2 class="mb-0">{{ $staff->reference }}</h2>
                                <div class="d-none d-md-flex align-items-center ml-2">
                                    <!--<i class="mdi mdi-clock text-muted"></i>-->
                                    <!--<small class=" ml-1 mb-0">Updated: 9:10am</small>-->
                                </div>
                            </div>
                            <small class="text-gray">
                                Reference
                            </small>
                        </div>
                        <div class="d-inline-block">
                            <div class="bg-success px-4 py-2 rounded">
                                <i class="icon-user text-white icon-sm"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-0">Accounts</h4>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-inline-block pt-3">
                            <div class="d-flex">
                                <h2 class="mb-0">{{ $staff->users->count() }}</h2>
                                <div class="d-none d-md-flex align-items-center ml-2">
                                </div>
                            </div>
                            <small class="text-gray">
                                Users
                            </small>
                        </div>
                        <div class="d-inline-block">
                            <div class="bg-info px-4 py-2 rounded">
                                <i class="icon-people text-white icon-sm"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-0">Affiliates</h4>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-inline-block pt-3">
                            <div class="d-flex">
                                <h2 class="mb-0">{{ $staff->affiliates()->count() }}</h2>
                                <div class="d-none d-md-flex align-items-center ml-2">
                                </div>
                            </div>
                            <small class="text-gray">
                                Affiliate Accounts
                            </small>
                        </div>
                        <div class="d-inline-block">
                            <div class="bg-primary px-4 py-2 rounded">
                                <i class="icon-diamond text-white icon-sm"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-0">Wallet Balance</h4>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-inline-block pt-3">
                            <div class="d-flex">
                                <h2 class="mb-0">₦ {{ number_format($staff->wallet, 2) }}</h2>
                                <div class="d-none d-md-flex align-items-center ml-2">
                                </div>
                            </div>
                            <small class="text-gray">
                                &nbsp;
                            </small>
                        </div>
                        <div class="d-inline-block">
                            <div class="bg-dark px-4 py-2 rounded">
                                <i class="icon-wallet text-white icon-sm"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <br />

    <div class="row profile-page">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="profile-header text-white d-none d-md-block">
                        <div class="d-flex justify-content-around">
                            <div class="profile-info d-flex justify-content-center align-items-md-center">
                                <img class="rounded-circle img-lg" src="{{ $staff->avatar }}" alt="profile image">
                                <div class="wrapper pl-4">
                                    <p class="profile-user-name">
                                        {{ $staff->name}}
                                    </p>
                                    <div class="wrapper d-flex align-items-center">
                                        <p class="profile-user-designation">Nextpayday Staff</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="profile-body pt-0 pt-sm-4">
                        @include('layouts.shared.error-display')

                        <ul class="nav tab-switch " role="tablist ">
                            <li class="nav-item ">
                                <a class="nav-link active " id="user-profile-info-tab" data-toggle="pill" href="#user-profile-info" role="tab " aria-controls="user-profile-info" aria-selected="true ">Basic Information</a>
                            </li>
                            <li class="nav-item ">
                                <a class="nav-link" id="user-roles-tab" data-toggle="pill" href="#user-roles" role="tab " aria-controls="user-roles" aria-selected="true ">Manage Roles</a>
                            </li>
                        </ul>
                        <div class="row ">
                            <div class="col-12 col-md-9">
                                <div class="tab-content tab-body" id="profile-log-switch ">
                                    <div class="tab-pane fade show active pr-3 " id="user-profile-info" role="tabpanel" aria-labelledby="user-profile-info-tab">


                                        <div class="row ">
                                            <div class="col-12 mt-5">
                                                <h5>Basic Information</h5>
                                            </div>
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table table-borderless w-100 mt-4 ">
                                                <tr>
                                                    <td>
                                                        <strong>Full Name :</strong> {{ $staff->name }}
                                                    </td>
                                                    <td>
                                                        <strong>Phone :</strong> {{ $staff->phone }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <strong>Email :</strong> {{ $staff->email }}
                                                    </td>
                                                    <td>
                                                        <strong>Location :</strong> {{ $staff->address }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <strong>State :</strong> {{ $staff->state }}
                                                    </td>
                                                    <td>
                                                        <strong>Supervisor Commission Rate :</strong>
                                                        {{ $staff->commission_rate ? $staff->commission_rate . '%' : 'Not Set' }}
                                                        &nbsp;<button class="badge badge-primary" data-toggle="modal" data-target="#updateModal">
                                                            <i class="fa fa-edit"></i>
                                                        </button>
                                                    </td>
                                                </tr>

                                            </table>
                                        </div>
                                    </div>


                                    <div class="tab-pane fade pr-3 " id="user-roles" role="tabpanel" aria-labelledby="user-profile-info-tab">


                                        <div class="row ">
                                            <div class="col-12 mt-5">

                                                <form method="post" action="{{ route('admin.staff.roles', ['staff' => $staff->reference]) }}">
                                                    @csrf
                                                    <div class="form-group">
                                                        <strong>ROLES </strong>
                                                    </div>

                                                        @component('components.staff_roles', ['staff'=>$staff])
                                                        @endcomponent
                                                    
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-append">
                                                            <button class="btn btn-outline-primary" type="submit">Update</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <br />

    <div class="row justify-content-center">
        <div class="card col-sm-12">
            <div class="card-body">
                <h4 class="card-title uppercase">Users</h4>
                <br />
                <div class="table-responsive">
                    <table id="order-listing" class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th class="text-center">View</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($staff->users as $user)
                            <tr>
                                <td>{{$user->name}}</td>
                                <td>{{$user->email}}</td>
                                <td>
                                    <a href="{{ route('admin.users.view', ['user' => $user->reference]) }}" class="btn btn-sm btn-primary">
                                        View User
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center">No accounts</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <br />

    <div class="row justify-content-center">
        <div class="card col-sm-12">
            <div class="card-body">
                <h4 class="card-title uppercase">Affiliates</h4>
                <br />
                <div class="table-responsive">
                    <table id="order-listing" class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th class="text-center">View</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($staff->affiliates as $user)
                            <tr>
                                <td>{{$user->name}}</td>
                                <td>{{$user->email}}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.affiliates.show', ['affiliate' => $user->reference]) }}" class="btn btn-xs btn-info">
                                        View Affiliate
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center">No accounts</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel-2">Set Commission Rate</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" id="updateForm" action="{{ route('admin.staff.update', ['staff' => $staff->reference]) }}">
                        @csrf
                        <div class="form-group">
                            <label>Update Commission Rate</label>
                            <input type="text" name="commission_rate" value="{{ $staff->commission_rate }}" class="form-control" required />
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <button type="submit" form="updateForm" class="btn btn-success">Set</button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('page-js')
<script src="{{asset('assets/js/data-table.js')}}"></script>
@endsection