@extends('layouts.admin')

@section('content')
<main class="main">
  <!-- Breadcrumb -->
  <ol class="breadcrumb">
    <li class="breadcrumb-item">Home</li>
    <li class="breadcrumb-item"><a href="{{route('admin.hilcop-certificates.investments.index')}}">Certificates</a></li>
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
                  <strong>New</strong>
                  <small>Certificate</small>
                </div>
                
                <form method="POST" action="{{route('admin.hilcop-certificates.investments.new') }}">
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
                            <label for="no_of_shares">No. of Shares</label>
                            <input type="number" class="form-control" name="no_of_shares" id="no_of_shares" placeholder="Enter No of Shares" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="value_per_share">Value Per Share</label>
                            <input type="number" class="form-control" name="value_per_share" id="value_per_share" placeholder="Enter Value per Share" required>
                        </div>

                         <div class="form-group">
                            <label for="membership_date">Membership Date</label>
                            <input type="date" class="form-control" name="membership_date" id="membership_date" placeholder="Enter Investor Membership Date" required>
                        </div>

                        <div class="form-group">
                            <label for="address">Address</label>
                            <input type="text" class="form-control" name="address" id="address" placeholder="Enter Address">
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