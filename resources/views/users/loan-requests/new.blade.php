@extends('layouts.user')

@section('content')
<main class="main">
  <!-- Breadcrumb -->
  <ol class="breadcrumb">
    <li class="breadcrumb-item">Home</li>
    <li class="breadcrumb-item"><a href="{{route('users.loan-requests.index')}}">Loan Requests</a></li>
    <li class="breadcrumb-item active">New</li>

      <li class="breadcrumb-menu">
        <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
          <a class="btn btn-primary btn-lg waves-effect text-white" href="{{route('users.loan-requests.create')}}" style="border-radius: 20px;"> 
           <span style="font-size: 0.9rem;"> <i class="icon-cursor text-white"></i> Get Loan</span>
          </a>
        </div>
      </li>
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
              <?php 
                  $loanApplicationFee = App\Models\Settings::loanRequestFee();
                  $pay_charge = $loanApplicationFee + paystack_charge($loanApplicationFee);
                  
                  $employment = Auth::user()->employments()->with('employer')->get()->last();
                  
                  $referrer = optional();

                  if (!Auth::user()->adder_type == 'AppModelsInvestor') {
                    $referrer = Auth::user()->referrer;
                  }
                  // Get the last incomplete requests
                  $incompleteRequest = Auth::user()->incompleteRequests->last();
              ?>
              <form method="POST" onmousemove="formButton()" enctype="multipart/form-data">
                {{ csrf_field() }}
                  <loan-request-form-section 
                      :employments="{{Auth::user()->employments()->with('employer')->get()}}" :loan_fee="{{$loanApplicationFee}}" 
                      :application_fee="{{$pay_charge}}" :users="{{json_encode($users)}}" :user="{{Auth::user()}}" :payurl="'{{config('paystack.publicKey')}}'" :monokey="'{{config('monostatement.clientPublic')}}'" :affiliatecode="'{{optional($referrer)->reference}}'"
                      :request="{{json_encode($incompleteRequest)}}">
                  </loan-request-form-section>
                  {{-- <!-- <user-referral :code="'{{optional($referrer)->reference}}'" :users="{{json_encode($users)}}"></user-referral> --> --}}
              </form>
              {{-- @if(!$employment->employer || !$employment->employer->upfront_interest)                
                <form method="POST" onmousemove="formButton()" enctype="multipart/form-data">
                      {{ csrf_field() }}
                    <loan-request-form-section 
                        :employments="{{Auth::user()->employments()->with('employer')->get()}}" :loan_fee="{{$loanApplicationFee}}" 
                        :application_fee="{{$pay_charge}}" :users="{{json_encode($users)}}" :user="{{Auth::user()}}" :payurl="'{{config('paystack.publicKey')}}'" :monokey="'{{config('monostatement.clientPublic')}}'" :affiliatecode="'{{optional($referrer)->reference}}'"
                        :request="{{json_encode($incompleteRequest)}}">
                    </loan-request-form-section>
                    <!-- <user-referral :code="'{{optional($referrer)->reference}}'" :users="{{json_encode($users)}}"></user-referral> -->
                </form>
              @else
                <form method="POST" onmousemove="formButton()" enctype="multipart/form-data">
                      {{ csrf_field() }}
                    <salary-now-loan-request-form-section 
                        :employments="{{Auth::user()->employments()->with('employer')->get()}}" :loan_fee="{{$loanApplicationFee}}" 
                        :application_fee="{{$pay_charge}}" :users="{{json_encode($users)}}" :user="{{Auth::user()}}" :payurl="'{{config('paystack.publicKey')}}'" :monokey="'{{config('monostatement.clientPublic')}}'" :affiliatecode="'{{optional($referrer)->reference}}'"
                        :request="{{json_encode($incompleteRequest)}}">
                    </salary-now-loan-request-form-section>
                    <!-- <user-referral :code="'{{optional($referrer)->reference}}'" :users="{{json_encode($users)}}"></user-referral> -->
                </form>
              @endif --}}
            </div>
          </div>
       </div>
    </div>
  </div>
</main>
@endsection

@section('page-js')

<script src="https://js.paystack.co/v1/inline.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script> 
<script type="application/javascript" src="https://connect.withmono.com/connect.js"></script>

<script type="text/javascript">
  function formButton() {
    var bank    = $('#bank_statement').val();
    var amount  = $('#amount').val();
    var comment = $('#textarea-input').val();
    if (amount == '' || bank == '' || comment == '') {
        $("#booking-form").attr('disabled',true);
      }else{
         $("#booking-form").attr('disabled',false);
      }
  }
</script>
@endsection