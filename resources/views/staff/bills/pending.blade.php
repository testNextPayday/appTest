@extends('layouts.staff-new')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="">Bills</a></li>
        <li class="breadcrumb-item active">Pending</li>
    </ol>

    <div class="container-fluid">

        <div class="animated fadeIn">
            
            <bill-requests></bill-requests>
            
           
            <!--/.row-->
        </div>

        
    </div>
    <!-- /.conainer-fluid -->
</main>
@endsection

@section('page-js')
@endsection