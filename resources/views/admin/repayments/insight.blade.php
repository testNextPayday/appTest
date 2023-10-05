@extends('layouts.admin-new')

@section('content')
    <div class="content-wrapper">
        <collection-insight></collection-insight>
    </div>
@endsection

@section('page-js')
    <script src="{{asset('assets/js/data-table.js')}}"></script>
@endsection