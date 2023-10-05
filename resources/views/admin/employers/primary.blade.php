@extends('layouts.admin-new')

@section('content')

<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">Add Primary Employer</h4>
        </div>
    </div>
    
    @if ($errors->any())
        <div class="row justify-content-center">
            <div class="col-sm-6">
                <div class="alert alert-danger">
                    <ul class="list-group">
                        @foreach ($errors->all() as $error)
                            <li class="list-group-item">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif
    
    <div class="row">
        <div class="col-sm-12">
              <form action="{{route('admin.employer.primary')}}" method="post">
                  {{csrf_field()}}
                  <div class="form-group">
                      <label for="primary">Primary Employer Name</label>
                      <input type="text" id="primary" class="form-control" name="name"/>
                      
                  </div>
                  <button type="submit" class="btn btn-primary">Create</button>
              </form>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-sm-4">
            <ul class="list-group">
            @foreach($primaryEmployers as $employer)
                <li  class="list-group-item">{{$employer->name}} <button style="float:right;" onclick="document.getElementById('form{{$employer->id}}').submit()" class="btn btn-danger">Delete</button></li>
            @endforeach
            
            <form method="post" action="{{route('admin.employer.primary.delete',$employer)}}" id="form{{$employer->id}}">
                {{csrf_field()}}
                {{method_field('DELETE')}}
            </form>
            </ul>
           
        </div>
    </div>
</div>
@endsection

@section('page-js')


@endsection