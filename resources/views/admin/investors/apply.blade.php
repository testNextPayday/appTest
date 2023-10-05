@extends('layouts.admin-new')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item active"><a href="#">Investor Verification</a></li>
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
                    
                    <?php
                        $ref = '';
                        if (isset($reference)) $ref = $reference;
                    ?>
                    <lender-registration 
                        :reference="'{{$reference}}'"
                        :url="'{{route('admin.investors.submit-application')}}'"/>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@section('page-js')
@endsection