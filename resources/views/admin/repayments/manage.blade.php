@extends('layouts.admin-new')

@section('content')
<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">Manage Loans</h4>
        </div>
    </div>
    
    @include('layouts.shared.error-display')
    
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title bold">EXPORT DUE REPAYMENTS</h4>
                    <p>Export loans repayments that are due as an excel sheet</p>
                    <br/>
                    
                    <div class="row">
                        <form action="{{ route('admin.repayments.export-due') }}" class="col-md-4">
                            <div class="form-group">
                                <label>Select Loan Type</label>
                                <select name="type" class="form-control" required>
                                    <option value="ippis">IPPIS Loans</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Start Date</label>
                                <input class="form-control" type="date" name="start_date"/>
                            </div>
                            <div class="form-group">
                                <label>End Date</label>
                                <input class="form-control" type="date" name="end_date"/>
                            </div>
                            <button class="btn btn-primary btn-xs">
                                Export&nbsp;
                                <i class="fa fa-download"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <br/>
            </div>
        </div>
    </div>
    <br/>
    
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title bold">IMPORT SETTLED REPAYMENTS</h4>
                    <p>Import loans repayments that have been settled</p>
                    <br/>
                    
                    <div class="row">
                        <form action="{{ route('admin.repayments.import') }}" method="POST" enctype="multipart/form-data" class="col-md-4">
                            @csrf
                            <div class="form-group">
                                <label>Select Loan Type</label>
                                <select name="type" class="form-control" required>
                                    <option value="ippis">IPPIS Loans</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Select File</label>
                                <input type="file" name="file" class="form-control" required/>
                            </div>
                            <button class="btn btn-info btn-xs">
                                Import&nbsp;
                                <i class="fa fa-upload"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <br/>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-js')
@endsection