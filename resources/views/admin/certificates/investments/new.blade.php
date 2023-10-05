@extends('layouts.admin-new')

@section('content')
<main class="main">
  <!-- Breadcrumb -->
  <ol class="breadcrumb">
    <li class="breadcrumb-item">Home</li>
    <li class="breadcrumb-item"><a href="{{route('admin.certificates.investments.index')}}">Certificates</a></li>
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
                  <small>Certificate</small>
                </div>
                
                <form method="POST" action="{{route('admin.certificates.investments.new') }}">
                    {{ csrf_field() }}
                
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Recipient</label>
                            <input type="text" class="form-control" name="name" id="name" placeholder="Enter name of recipient" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email (optional - use if you want to send mail automatically)</label>
                            <input type="email" class="form-control" name="email" id="name" placeholder="Enter email of recipient">
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
                            <label for="phone">Phone</label>
                            <input type="text" class="form-control" name="phone" id="phone" placeholder="Enter Investor Phone" required>
                        </div>

                         <div class="form-group">
                            <label for="whatsapp">Whatsapp</label>
                            <input type="text" class="form-control" name="whatsapp" id="whatsapp" placeholder="Enter Investor whatsapp" required>
                        </div>

                        <div class="form-group">
                            <label for="account_name">Account Name</label>
                            <input type="text" class="form-control" name="account_name" id="account_name" placeholder="Enter Investor Account name" required>
                        </div>

                         <div class="form-group">
                            <label for="account_number">Account Number</label>
                            <input type="text" class="form-control" name="account_number" id="account_number" placeholder="Enter Investor Account Number" required>
                        </div>


                        <div class="form-group">
                            <label for="bank">Select Bank</label>
                            <select  class="form-control" name="bank" id="bank"  required>
                              @foreach(config('remita.banks') as $bank)
                                  <option value="{{$bank}}">{{$bank}}</option>
                              @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="rate">Rate</label>
                            <input type="number" class="form-control" name="rate" id="rate" placeholder="Enter rate" required>
                        </div>

                        <div class="form-group">
                            <label for="rate">Tax %</label>
                            <input type="number" class="form-control" name="tax" id="tax" placeholder="Enter Tax" required>
                        </div>

                        <div class="form-group">
                          <label for="next_kin_name">Name of Next Kin</label>
                          <input type="text" name="next_kin_name" class="form-control" placeholder="Enter Next Kin Name">
                        </div>

                        <div class="form-group">
                          <label for="next_kin_phone">Phone of Next Kin</label>
                          <input type="text" name="next_kin_phone" class="form-control" placeholder="Enter Next Kin Phone">
                        </div>

                        <div class="form-group">
                          <label for="address"> Address</label>
                          <input type="text" name="address" class="form-control">
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
                            <label for="interest_payment_cycle">Interest Payment Cycle</label>
                            <select name="interest_payment_cycle" id="interest_payment_cycle" required>
                                <option value="Upfront" selected>Upfront</option>
                                <option value="Backend">Backend</option>
                            </select>
                        </div>

                        @php
                            $staffs = \App\Models\Staff::all();
                            $affiliates = \App\Models\Affiliate::active()->get();
                            $investors = \App\Models\Investor::all();
                        @endphp
                        <div class="form-group">
                            <label class="form-control-control">Pick an someone to receive commission</label>
                            <assign-investor-commission :staffs="{{$staffs}}" :investors="{{$investors}}" :affiliates="{{$affiliates}}"></assign-investor-commission>
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