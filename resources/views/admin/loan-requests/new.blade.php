@extends('layouts.admin')

@section('content')
<main class="main">
  <!-- Breadcrumb -->
  <ol class="breadcrumb">
    <li class="breadcrumb-item">Home</li>
    <li class="breadcrumb-item"><a href="{{route('admin.loan-requests.index')}}">Loan Requests</a></li>
    <li class="breadcrumb-item active">New</li>
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
                
                <form method="POST" action="{{route('admin.loan-requests.store') }}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                
                <div class="card-body">
                  <max-request-amount 
                    :url="'{{route('staff.loan-requests.checkmax')}}'"
                    :emi-url="'{{route('staff.loan-requests.checkemi')}}'"
                    :users="{{$users}}"></max-request-amount>
                  
                  <div class="form-group">
                    <label for="risk_rating">Risk Rating </label>
                    <select name="risk_rating" id="risk_rating" required>
                        <option value="5">5</option>
                        <option value="4">4</option>
                        <option value="3">3</option>
                        <option value="2">2</option>
                        <option value="1">1</option>
                    </select>
                  </div>
                  
                  <div class="checkbox">
                    <label for="checkbox1">
                      <input type="checkbox" id="checkbox1" name="will_collect_incomplete"> Will this loan be taken if its incomplete by the expected withdrawal date?
                    </label>
                  </div>
                  
                  <div class="form-group">
                    <label for="bank_statement">Bank Statement (3 months from today, JPG or PDF)</label>
                    <input type="file" class="form-control" name="bank_statement" id="bank_statement" required>
                  </div>

                  <div class="row">
                    <div class="form-group col-sm-12">
                      <label for="textarea-input">Purpose of Loan</label>
                      <textarea id="textarea-input" name="comment" rows="3" class="form-control" placeholder="This is your opportunity to increase your chances of getting a Loan">{{old('comment')}}</textarea>
                    </div>  
                  </div>
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