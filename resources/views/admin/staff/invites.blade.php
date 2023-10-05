@extends('layouts.admin-new')

@section('content')
<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">Invite Staff</h4>
        </div>
    </div>
    
    @include('layouts.shared.error-display')
    
    <div class="row justify-content-center">
        <div class="card col-sm-8">
            <div class="card-body">
                <h4 class="card-title uppercase mb-4">New Invitation</h4>
                
                <form method="POST" action="{{route('admin.staff.invites')}}">
                    {{csrf_field()}}
                    <div class="form-group">
                        <strong>ROLES </strong>
                    </div>
                    
                    @component('components.staff_roles', ['staff'=>optional(null)])
                    @endcomponent

                    <div class="input-group mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Email Address" aria-label="Staff Email Address" aria-describedby="basic-addon">
                        <div class="input-group-append">
                            <button class="btn btn-outline-primary" type="submit">Invite</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <br/>
    
    <div class="row justify-content-center">
        <div class="card col-sm-8">
            <div class="card-body">
                <h4 class="card-title uppercase">Pending Invites</h4>
                <br/>
                <div class="table-responsive">
                    <table id="order-listing" class="table">
                        <thead>
                            <tr>
                                <th>Email</th>
                                <th>Roles</th>
                                <th class="text-center">Inviter</th>
                                <th class="text-center">Manage</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invites as $invite)
                                <tr>
                                    <td>
                                        <div>{{$invite->email}}</div>
                                    </td>
                                    <td>{{$invite->roles}}</td>
                                    <td class="text-center">
                                        @if($invite->inviter == 1)
                                        <button class="btn btn-sm btn-success">Admin</button>
                                        @else
                                        <button class="btn btn-sm btn-primary">Null</button>
                                        @endif
                                    </td>
                                    
                                     <td class="text-center">
                                        <a href="{{route('admin.staff.invites.delete', ['invite_id' => $invite->id])}}" 
                                            onclick="return confirm('Are you sure you want to delete this invitation?');"
                                            class="btn btn-sm btn-warning"><i class="icon-close"></i> Delete Invitation</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">There are no pending staff invites</td>
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