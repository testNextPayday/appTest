@extends('layouts.staff-new')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/css/bootstrap-select.min.css">
@section('content')
<div class="content-wrapper">
    <div class="card">
        <div class="card-body">
            <h2 class="card-title"> Create Groups </h2>
            <staff-group-borrowers>
            </staff-group-borrowers>


        </div>
    </div>
</div>
@endsection

@section('page-js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/js/bootstrap-select.min.js"></script>
<script src="{{asset('assets/js/data-table.js')}}">
</script>
<script type="text/javascript" src="//cdn.datatables.net/plug-ins/1.10.19/api/sum().js"></script>

@endsection