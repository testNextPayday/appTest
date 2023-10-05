@extends('layouts.admin-new')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item active"><a href="#">Investor Registration</a></li>
    </ol>
  
    <div class="container-fluid">

        <div class="animated fadeIn">
            <div class="row justify-content-center">
                <div class="col-sm-6">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
              
                    
                    <div class="card">
                        <div class="card-header">
                            <strong>Fresh</strong>
                            <small>Investor</small>
                        </div>
                
                        <form method="POST" action="{{route('admin.investors.create') }}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                
                            <div class="card-body">
                  
                                <div class="form-group">
                                    <label for="company">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{old('name')}}" placeholder="Enter investor name">
                                </div>
                                
                                <div class="form-group">
                                    <label for="company">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{old('email')}}" placeholder="Enter investor email">
                                </div>
                                
                                <div class="form-group">
                                    <label for="company">Phone</label>
                                    <input type="text" class="form-control" id="phone" name="phone" value="{{old('phone')}}" placeholder="Enter investor phone">
                                </div>

                                <div class="form-group">
                                    <label for="role">Investor Category</label>
                                    <select class="form-control" id="role" name="role" value="{{old('role')}}">
                                        <option value="1"> Premium </option>
                                        <option value="2"> Promissory Note </option>
                                    </select>
                                </div>
                                
                                <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-dot-circle-o"></i> Submit</button>
                            </div>
                        </form>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
    
</main>
@endsection

@section('page-js')
@endsection