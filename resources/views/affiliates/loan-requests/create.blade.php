@extends('layouts.affiliates')

@section('content')

<div class="content-wrapper">
  <div class="row mb-4">
      <div class="col-12 d-flex align-items-center justify-content-between">
          <h4 class="page-title">
              Place a new Loan Request
          </h4>
      </div>
  </div>
    
  @include('layouts.shared.error-display')
    
  <div class="row justify-content-center">
    <div class="col-md-8">
      @php $pay_charge = $loanApplicationFee + paystack_charge($loanApplicationFee); @endphp;
      <form method="POST" onmousemove="formButton()" action="{{route('affiliates.loan-requests') }}" enctype="multipart/form-data">
        {{ csrf_field() }}
        
        <max-request-amount 
          :url="'{{route('affiliates.loan-requests.checkmax')}}'"
          :emi-url="'{{route('affiliates.loan-requests.checkemi')}}'"
          :users-url="'{{route('affiliates.loan-requests.users')}}'"
          :application_fee="{{$pay_charge}}">
        </max-request-amount>
        
      </form>
    </div>
  </div>
</div>
@endsection

@section('page-js')
<script type="text/javascript">
  function formButton() {
    var bank    = $('#bank_statement').val();
    var amount  = $('#amount').val();
    var comment = $('#comment').val();
       if (amount == '' || bank == '' || comment == '') {
        $("#booking-form").attr('disabled',true);
      }else{
         $("#booking-form").attr('disabled',false);
      }
  }
</script>
@endsection