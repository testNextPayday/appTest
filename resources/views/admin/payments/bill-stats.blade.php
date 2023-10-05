@extends('layouts.admin-new')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="{{route('admin.bills.index')}}">Bills</a></li>
        <li class="breadcrumb-item active">All</li>
    </ol>

    <div class="container-fluid">

        <div class="animated fadeIn">
           
            <div style="height:100px;width:100%;"></div>
            <bills-statistics></bills-statistics>
            
           
            <!--/.row-->
        </div>

        
    </div>
    <!-- /.conainer-fluid -->
</main>
@endsection

@section('page-js')
@endsection