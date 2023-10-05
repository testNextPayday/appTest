@extends('layouts.admin-new')

@section('content')

<main class="main">
  <!-- Breadcrumb -->
  <ol class="breadcrumb">
    <li class="breadcrumb-item">Home</li>
    <li class="breadcrumb-item"><a href="{{route('admin.promissory-notes.index')}}">Promissory Note</a></li>
    <li class="breadcrumb-item active">New</li>
  </ol>
      <div class="container-fluid">

        <div class="animated fadeIn">
          <div class="row justify-content-center">
              
          <div class="col-sm-8">
             
              <div class="card">
                <div class="card-header">
                  <strong>New</strong>
                  <small>Promissory Note</small>
                    <br>
                  <small>No investor account? <a target="_blank" href="{{route('admin.investors.create')}}">Create an Account</a></small>
                </div>
                
                <form method="POST" action="{{route('admin.promissory-notes.store') }}">
                    {{ csrf_field() }}
                
                    <div class="card-body">
                        <div class="form-group">
                            <label for="investor_id">Select Investor</label>
                            <select class="form-control" name="investor_id" data-live-search="true">
                                @forelse($investors as $investor)
                                    <option value="{{$investor->id}}">{{$investor->name}}</option>
                                @empty
                                    <option value="">No Promissory Investor Found</option>
                                @endforelse
                            </select>
                        </div>
                    
                        
                        <div class="form-group">
                            <label for="start_date">Start Date</label>
                            <input type="date" class="form-control" name="start_date" id="start_date" placeholder="Enter Investment Start Date" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="amount">Amount</label>
                            <input type="number" class="form-control" name="amount" id="amount" placeholder="Enter Investment Amount" required>
                        </div>
                        
                       <admin-edit-settings-on-note :settings="{{$settings}}"></admin-edit-settings-on-note>
                        
                        <div class="form-group">
                            <label for="tenure">Tenure</label>
                            <select class="form-control" name="tenure" id="tenure" required>

                                <option value="3">3 Months</option>   
                                <option value="4">4 Months</option>
                                <option value="5">5 Months</option> 
                                <option value="6">6 Months</option>
                                <option value="7">7 Months</option>
                                <option value="8">8 Months</option>     
                                <option value="9">9 Months</option> 
                                <option value="10">10 Months</option>
                                <option value="11">11 Months</option>    
                                <option value="12">12 Months</option>   
                                <option value="13">13 Months</option> 
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="interest_payment_cycle" class="form-control-label">Interest Payment Cycle</label>
                            <select name="interest_payment_cycle" class="form-control" id="interest_payment_cycle" required>
                                <option value="upfront" selected>Upfront</option>
                                <option value="backend">Backend</option>
                                <option value="monthly">Monthly</option>
                                
                            </select>
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