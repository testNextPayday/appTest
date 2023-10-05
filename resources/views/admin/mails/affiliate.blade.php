@extends('layouts.admin-new')

@section('content')
<div class="content-wrapper">

    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">
                Mail - Agent
                <span class="badge badge-danger">
                    <i class=" fa fa-envelope"></i>
                </span>
            </h4>
        </div>
    </div>
    @component('components.admin-compose-mail')
        @slot('userType')
        <input type="type" hidden name="affiliates">
        @endslot
    @endcomponent
    @section('page-js')
         <script>
            CKEDITOR.replace( 'mail_content' );
        </script>
    @endsection




</div>
@endsection