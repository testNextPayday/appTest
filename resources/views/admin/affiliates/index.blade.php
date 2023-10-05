@extends('layouts.admin-new')

@section('content')
    <div class="content-wrapper">
        <div class="row mb-4">
            <div class="col-12 d-flex align-items-center justify-content-between">
                <h4 class="page-title">Agent Users</h4>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12 table-responsive">
                        <table id="order-listing" class="table">
                            <thead>
                                <tr>
                                    <th>Reference</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Wallet</th>
                                    <th>Date Created</th>
                                    <th>State</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($affiliates as $affiliate)
                                    <tr>
                                        <td>
                                            <a class="badge badge-info" 
                                                href="{{ route('admin.affiliates.show', ['affiliate' => $affiliate->reference]) }}">
                                                {{ $affiliate->reference }}
                                            </a>
                                        </td>
                                        <td>{{ $affiliate->name }}</td>
                                        <td>{{ $affiliate->email }}</td>
                                        <td>₦ {{number_format($affiliate->wallet, 2) }}</td>
                                        <td>{{$affiliate->created_at}}</td>
                                        <td>{{ $affiliate->state }}</td>
                                        <td class="text-center">
                                            @if ($affiliate->verified_at)
                                                @if($affiliate->status)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-warning">Inactive</span>
                                                @endif
                                            @else
                                                <span class="badge badge-danger">Unverified</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Data Unavailable</td>
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