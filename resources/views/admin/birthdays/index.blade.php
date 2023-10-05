@extends('layouts.admin-new')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="{{route('admin.birthdays.index')}}">Birthdays</a></li>
        <li class="breadcrumb-item active">All</li>
    </ol>

    <div class="container-fluid">

        <div class="animated fadeIn">
            
            @include('layouts.shared.error-display')
            
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <birthdays></birthdays>
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
@endsection