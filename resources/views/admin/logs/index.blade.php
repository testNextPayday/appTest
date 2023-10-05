@extends('layouts.admin-new')

@section('content')

    <div class="content-wrapper">
        <div class="row mb-4">
            <div class="col-12 d-flex align-items-center justify-content-between">
                <h4 class="page-title">Log Data</h4>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-12 d-flex align-items-center">
                <div class="card px-4 py-4">
                    <form action="" class="row align-items-center">
                        <div class="form-group col-sm-4">
                            <label>Start Date</label>
                            <input type="date" class="form-control" name="start_date"/>
                        </div>
                        <div class="form-group col-sm-4">
                            <label>End Date</label>
                            <input type="date" class="form-control" name="end_date"/>
                        </div>
                        <div class="col-sm-4">
                            <button class="btn btn-primary btn-sm">Pull Logs</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        @if($logs)
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Log Data</h4>
                <div class="row">
                    <div class="col-12 table-responsive">
                        <table id="order-listing" class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Cron Entry</th>
                                    <th>View Resource</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td>{{ $log->created_at}}</td>
                                        <td>
                                            @if($log->auto_generated)
                                                <label class="badge badge-primary">CRON</label>
                                            @else
                                                <label class="badge badge-success">MANUAL</label>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ $log->resourceLink('admin') }}"
                                                class="btn btn-xs btn-danger">
                                                View Resource
                                            </a>
                                        </td>
                                        <td>{{ $log->title}}</td>
                                        <td>{{ $log->description }}</td>
                                        
                                        
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
        @endif
    </div>
@endsection

@section('page-js')
    @if($logs)
        <script src="{{asset('assets/js/data-table.js')}}"></script>
    @endif
@endsection