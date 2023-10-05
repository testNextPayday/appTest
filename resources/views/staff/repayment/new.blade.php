@extends('layouts.staff-new')

@section('content')

<div class="content-wrapper">
  <div class="row mb-4">
      <div class="col-12 d-flex align-items-center justify-content-between">
          <h4 class="page-title">
             Add new Repayment for {{$borrower->name}}
          </h4>
      </div>
  </div>
    
  @include('layouts.shared.error-display')
    
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        
        <div class="card-body">
          
          <div class="d-flex align-items-center justify-content-between">
            
            <form action="{{route('staff.repayment.store',$loan->reference)}}" method="post">
                {{csrf_field()}}
               <div class="form-group">
                   <label for="">Amount</label>
                   <input type="text" class="form-control" name="amount" placeholder="Enter Amount paid">
               </div>
               
              <div class="form-group">
                  <label for="">Method of Payment</label>
                  <select name="payment_method" id="" class="form-control">
                      <option value="Cheque">Cheque</option>
                  </select>
              </div>
              <input type="hidden" name="user_id" value="{{$borrower->id}}"/>
              <input type="hidden" name="loan_id" value="{{$loan->id}}"/>
              <div class="form-group">
                  <label for="">Collection Date</label>
                  <input type="date" class="form-control" name="collection_date" placeholder="Enter collection date"/>
              </div>
              <div class="form-group">
                  <label for="">Collection Method</label>
                  <select name="collector" id="" class="form-control">
                      <option value="Remita">Remita</option>
                      <option value="{{auth()->user()->name}}">{{auth()->user()->name}}</option>
                      <option value="DAS">DAS</option>
                  </select>
              </div>
              <div class="form-group">
                  <label for="">Description</label>
                  <input type="text" name="description" placeholder="Enter Description" class="form-control"/>
              </div>
              <button type="submit" class="btn btn-primary">Create</button>
            </form>
          <br/>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('page-js')
@endsection