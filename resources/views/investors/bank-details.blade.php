@extends('layouts.investor-auth.master')
@section('content')
<section class="register">
        <div id="reg_con" class="container">
          <div class="row">
            <div class="form_block">
              <h2 class="form_title">Bank Details</h2>
              <br />
              
              <form method="post" action="{{route('investors.save.bank.data')}}">
                @csrf
                  <div class="form-group">
                    <div class="input_cover">
                        <label for="bank_code" ><b> Select Bank   </b></label><br>
                        <select class="form-control" name="bank_code" required id="bank_code">
                            @foreach(config('remita.banks') as $code => $bank)                            
                                <option value="{{ $code }}">{{$bank}}</option>
                            @endforeach
                        </select>                       
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="input_cover">
                      <input
                        id="account_number"
                        class="form_input"
                        type="text"
                        name="account_number"
                        placeholder="account number"
                        required
                      />
                      <label for="account_number" class="input_block">
                        <span class="">
                          <img
                          style='max-width:25px'
                            class="input_icon"
                            src="{{asset('investorpage/assets/acctnumicon.png')}}"
                        /></span>
                        <span class="input_label">Account Number</span>
                      </label>
                    </div>
                  </div>
                  
                  <div class="actions">
                    <input type="submit" class="confirm_btn form_btn" value="Submit">
                  </div>
              </form>
            </div>
          </div>
        </div>
</section>
@endsection