@extends('layouts.admin-new')

@section('content')

<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">{{ $employer->name }}'s Employees</h4>
            <a class="btn btn-sm btn-primary"
                href="{{ route('admin.employers.view', ['employer' => encrypt($employer->id)]) }}">
                <i class="fa fa-arrow-left"></i> {{ $employer->name }}
            </a>
        </div>
    </div>
    
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
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 table-responsive">
                            <table id="order-listing" class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Date Employed</th>
                                        <th>Date Confirmed</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($employees as $employee)
                                        <tr>
                                            <td>{{$employee->name}}</td>
                                            <td>{{$employee->email}}</td>
                                            <td>{{$employee->phone}}</td>
                                            <td>{{$employee->employment->date_employed }}</td>
                                            <td>{{$employee->employment->date_confirmed }}</td>
                                            <td class="text-center">
                                                <a href="{{route('admin.users.view', ['user' => $employee->reference])}}" class="badge badge-primary">
                                                    View Employee
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No employees</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page-js')
<script src="{{asset('assets/js/data-table.js')}}"></script>
@endsection