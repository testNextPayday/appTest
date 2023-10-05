@extends('layouts.staff-new')

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

      <form method="POST" onmousemove="formButton()" action="{{route('staff.loan-requests.store') }}" enctype="multipart/form-data">
        {{ csrf_field() }}
        
        <max-request-amount 
          :url="'{{route('staff.loan-requests.checkmax')}}'"
          :emi-url="'{{route('staff.loan-requests.checkemi')}}'"
          :users-url="'{{route('staff.loan-requests.users')}}'">  
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