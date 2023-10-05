@extends('layouts.staff-new')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="#">Promissory Notes</a></li>
        <li class="breadcrumb-item active">Active</li>
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
                <div class="col-md-8 offset-md-2">
                    <div class="card">
                        <div class="card-header">
                            Promissory Notes Banks
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{route('admin.promissory-notes.bank-store')}}">
                                @csrf

                                <div class="form-group">
                                    <label class="form-control-label">Select Investor</label>
                                    <select name="investor_id" class="form-control">
                                        @foreach($investors as $investor)  
                                            <option value="{{$investor->id}}">{{$investor->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="form-control-label">Select Bank</label>
                                    <select name="bank_code" class="form-control" >
                                        @foreach(config('remita.banks') as $index=>$bank)  
                                            <option value="{{$index}}">{{$bank}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="form-control-label">Account Number</label>
                                    <input type="text" class="form-control" name="account_number">
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Save <i class="fa fa-spin"></i></button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
                <!--/.col-->
            </div>
            <!--/.row-->
        </div>

    </div>
    <!-- /.conainer-fluid -->
</main>
@endsection

@section('page-js')

    <script src="{{asset('assets/js/data-table.js')}}">
    </script>
    <script type="text/javascript" src="//cdn.datatables.net/plug-ins/1.10.19/api/sum().js"></script>
   
@endsection