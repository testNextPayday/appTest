@extends('layouts.staff-new')

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
                
                        <form method="POST" action="{{route('staff.investors.create') }}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                
                            <div class="card-body">
                  
                                <div class="form-group">
                                    <label for="company">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{old('name')}}" placeholder="Enter investor's name">
                                </div>
                                
                                <div class="form-group">
                                    <label for="company">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{old('email')}}" placeholder="Enter investor's email">
                                </div>
                                
                                <div class="form-group">
                                    <label for="company">Phone</label>
                                    <input type="text" class="form-control" id="phone" name="phone" value="{{old('phone')}}" placeholder="Enter investor's phone number">
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