@extends('layouts.admin-new')

@section('content')
<div class="content-wrapper">

    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">
                Mail - Borrowers
                <span class="badge badge-danger">
                    <i class=" fa fa-envelope"></i>
                </span>
            </h4>
        </div>
    </div>
    @component('components.admin-compose-mail')
   @slot('userType')
    <input type="type" hidden name="borrowers">
   @endslot
   <div class="form-group">
        <label class="form-control-label"> User Group</label>
        <select name="user_group" class="form-control">
             @foreach($employers as $employer)
                <option value="{{$employer->id}}"> {{$employer->name}}</option>
            @endforeach
        </select>
    </div>
  
    @endcomponent
      @section('page-js')
         <script>
            CKEDITOR.replace( 'mail_content' );
        </script>
    @endsection



</div>
@endsection