@extends('layouts.user')

@section('content')
<main class="main">
  <!-- Breadcrumb -->
  <ol class="breadcrumb">
    <li class="breadcrumb-item">Home</li>
    <li class="breadcrumb-item active">Remita Activation</li>
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
              <div class="card">
                <div class="card-header">
                  <strong>Loan</strong>
                  <small>Request</small>
                </div>
                
                <form method="POST" action="{{route('users.loan-request.mandate.activate') }}">
                    {{ csrf_field() }}
                
                <div class="card-body">
                  @foreach($authParams as $param)
                    <div class="form-group">
                        <?php
                            $description = 'description' . $loop->iteration;
                            $value = 'param' . $loop->iteration;
                        ?>
                        <label for="{{$param->$value}}">{{$param->$description}}</label>
                        <input type="number" class="form-control" id="{{$param->$value}}" name="{{$param->$value}}" value="{{$param->$value}}" 
                            placeholder="{{$param->$description}}">
                    </div>
                  @endforeach
                  <input type="hidden" value="{{$requestId}}" name="requestId"> 
                  <input type="hidden" value="{{$mandateId}}" name="mandateId"> 
                  <input type="hidden" value="{{$remitaTransRef}}" name="remitaTransRef"> 
                  
                  <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-dot-circle-o"></i> Submit</button>
                 </form>
                </div>
              </div>

            </div>
        </div>
    </div>
    </div>
</main>
@endsection

@section('page-js')
@endsection