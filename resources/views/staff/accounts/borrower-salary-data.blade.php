@extends('layouts.staff-new')
@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item active">Salary Information</li>
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
                            Get Salary Information
                        </div>
                        <div class="card-body text-center">
                            <div class="row justify-content-center">
                                <div class="col-sm-4">
                                    <form method="post" action="">
                                        {{ csrf_field() }}
                                        <div class="form-group">
                                            <input type="text" name="phone" class="form-control" placeholder="Enter customer's phone number" required/>
                                        </div>
                                    </form>  
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--/.col-->
            </div>
            @if($salaryData)
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            Salary Information
                        </div>
                        <div class="card-body">
                            @component('components.salary-data', [
                                'gotValidData' => $gotValidData,
                                'salaryData' => $salaryData
                            ])
                            @endcomponent
                        </div>
                        <div class="card-footer text-right">
                            
                        </div>
                    </div>
                </div>
                <!--/.col-->
            </div>
            @endif
            
        </div>

    </div>
    <!-- /.conainer-fluid -->
    
</main>
@endsection

@section('page-js')
@endsection