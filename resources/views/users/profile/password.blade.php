
@extends('layouts.user')

@section('content')

<main class="main">
<!-- Breadcrumb -->
<ol class="breadcrumb">
    <li class="breadcrumb-item">Home</li>
    <li class="breadcrumb-item"><a href="{{route('users.profile.index')}}">Profile</a></li>
    <li class="breadcrumb-item active">Password reset</li>

    <li class="breadcrumb-menu">
        <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
        <a class="btn btn-primary btn-lg waves-effect text-white" href="{{route('users.loan-requests.create')}}" style="border-radius: 20px;"> 
            <span style="font-size: 0.9rem;"> <i class="icon-cursor text-white"></i> Get Loan</span>
        </a>
        </div>
    </li>
</ol>

<div class="container-fluid">

<div class="row justify-content-center my-3">
    <div class="col-sm-6">
        <form class="form-horizontal" method="POST" action="{{route('users.profile.password')}}" enctype="multipart/form-data">
            {{ csrf_field() }}
            
            <div class="form-group">
                <label for="current-password">Current Password</label>
                <input type="password" class="form-control{{ $errors->has('current_password') ? 'is-invalid' : '' }}"
                    id="current-password" placeholder="Current Password" name="current_password" required>
            </div>
            <div class="form-group">
                <label for="new-password">New Password</label>
                <input type="password" class="form-control{{ $errors->has('new_password') ? 'is-invalid' : '' }}"
                    id="new-password" placeholder="New Password" name="new_password" required>
            </div>
            <div class="form-group">
                <label for="new-password-confirm">Confirm New Password</label>
                <input type="password" class="form-control{{ $errors->has('new_password_confirmation') ? 'is-invalid' : '' }}"
                    id="new-password-confirm" placeholder="Confirm New Password" name="new_password_confirmation" required>
            </div>
            
            <button type="submit" class="btn btn-warning">Update Password</button>
        </form>  
    </div>

</div>


</div>
    <!-- /.conainer-fluid -->
</main>
@endsection

@section('page-js')
@endsection