@extends('layouts.affiliates')

@section('content')
    @php($affiliate = auth('affiliate')->user())
    <div class="content-wrapper">
        <div class="row mb-4">
            <div class="col-12 d-flex align-items-center justify-content-between">
                <h4 class="page-title">Dashboard</h4>
            </div>
        </div>
        
        @component('components.affiliate-statistics', ['affiliate' => $affiliate])
        @endcomponent
        
        <div class="row">
            <active-targets></active-targets>
        </div>
      
        <div class="row">
            <div class="col-md-7 grid-margin stretch-card">
              
            </div>
            <div class="col-md-5 grid-margin stretch-card">
    
            </div>
        </div>
    </div>
    <!-- content-wrapper ends -->
@endsection

@section('page-js')
@endsection