@extends('layouts.staff-new')

@section('content')

<main class="main">
  <!-- Breadcrumb -->
  <ol class="breadcrumb">
    <li class="breadcrumb-item">Home</li>
    <li class="breadcrumb-item"><a href="{{route('staff.promissory-notes.index')}}">Promissory Note</a></li>
    <li class="breadcrumb-item active">New</li>
  </ol>
      <div class="container-fluid">

        <div class="animated fadeIn">
          <div class="row justify-content-center">
              
          <div class="col-sm-8">
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
                  <strong>New</strong>
                  <small>Promissory Note</small>
                    <br>
                  <!-- <small>No investor account? <a target="_blank" href="{{route('staff.investors.create')}}">Create an Account</a></small> -->
                </div>
                
                <form method="POST" action="{{route('staff.promissory-notes.store') }}">
                    {{ csrf_field() }}
                
                    <div class="card-body">
                        <div class="form-group">
                            <label for="investor_id">Select Investor</label>
                            <select class="form-control" name="investor_id" data-live-search="true" required>
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
                        
                        <div class="form-group">
                            <label for="setting_id">Pick Preconfigured Settings</label>
                            <select class="form-control" name="setting_id" id="setting_id" required>
                                @forelse($settings as $setting)
                                    <option value="{{$setting->id}}">{{$setting->name}}</option>
                                @empty 
                                    <p>No pre configuration found</p>
                                @endforelse
                            </select>
                        </div>

                       

                        
                        <div class="form-group">
                            <label for="tenure">Tenure</label>
                            <select class="form-control" name="tenure" id="tenure" required>
                                <option value="3">3 Months</option>    
                                <option value="6">6 Months</option>    
                                <option value="9">9 Months</option>    
                                <option value="12">12 Months</option>    
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