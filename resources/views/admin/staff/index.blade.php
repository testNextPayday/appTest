@extends('layouts.admin-new')

@section('content')

<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">{{ config('app.name') }} Staff</h4>
        </div>
    </div>
    
    @include('layouts.shared.error-display')
    
    
        <div class="row justify-content-center">
        <div class="card col-sm-12">
            <div class="card-body">
                <h4 class="card-title uppercase">All Staff</h4>
                <br/>
                <div class="table-responsive">
                    <table id="order-listing" class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Avatar</th>
                                <th>Staff ID</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Manage</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($staff as $user)
                                <tr>
                                    <td>
                                        <div>{{$user->lastname}}, {{$user->firstname}} {{$user->midname}}</div>
                                    </td>
                                    <td class="text-center">
                                        <div class="avatar">
                                            <img src="{{$user->avatar}}" class="img-avatar" alt="avatar">
                                         </div>
                                    </td>
                                    
                                    <td>
                                        <div>{{$user->reference}}</div>
                                    </td>
                                    <td class="text-center">
                                        @if($user->is_active)
                                        <button class="btn btn-sm btn-success">Active</button>
                                        @else
                                        <button class="btn btn-sm btn-warning">Disabled</button>
                                        @endif
                                    </td>
                                    
                                     <td class="text-center">
                                        <a href="{{route('admin.staff.view', ['reference' => $user->reference])}}" class="btn btn-sm btn-info"><i class="icon-eye"></i> View</a>
                                        @if($user->is_active)
                                        <a href="{{route('admin.staff.toggle', ['reference' => $user->reference])}}" 
                                            onclick="return confirm('Are you sure you want to disable this staff?');"
                                            class="btn btn-sm btn-warning"><i class="icon-close"></i> Disable</a>
                                        @else
                                        <a href="{{route('admin.staff.toggle', ['reference' => $user->reference])}}" 
                                            onclick="return confirm('Are you sure you want to enable this staff?');"
                                            class="btn btn-sm btn-success"><i class="icon-check"></i> Enable</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">There are no registered staff</td>
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