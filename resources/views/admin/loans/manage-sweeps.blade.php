@extends('layouts.admin-new')

@section('content')
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">

               <managed-loan-sweeper :batch="{{setBufferBatch()}}"></managed-loan-sweeper>

            </div>
        </div>
    </div>
@endsection

@section('page-js')


    <script src="{{asset('assets/js/data-table.js')}}">
    </script>
    <script type="text/javascript" src="//cdn.datatables.net/plug-ins/1.10.19/api/sum().js"></script>
   
   
   
        
     
    
@endsection