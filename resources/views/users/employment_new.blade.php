@extends('layouts.user')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="{{route('users.profile.index')}}">Employments</a></li>
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
                            <small>Employment</small>
                        </div>
                        
                        <div class="card-body">
                            <form method="POST" action="{{route('users.loan-requests.store') }}">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label class="control-label" for="position"><strong>Position</strong></label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-addon"><i class="icon-layers"></i></span>
                                        <input type="text" name="position" id="position"
                                            class="form-control{{ $errors->has('position') ? 'is-invalid' : '' }}" 
                                            required value="{{ old('position') }}" 
                                            placeholder="Your position at work" title="Your position at work">
                                    </div>    
                                </div>
                                
                                <div class="form-group">
                                    <label class="control-label" for="department"><strong>Department</strong></label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-addon"><i class="icon-briefcase"></i></span>
                                        <input type="text" name="department" id="department"
                                            class="form-control{{ $errors->has('department') ? 'is-invalid' : '' }}" 
                                            required value="{{ old('department') }}" 
                                            placeholder="Your department at work" title="Your department at work">
                                    </div>    
                                </div>
                                
                                <div class="form-group">
                                    <label class="control-label" for="date_employed"><strong>Date of Employment</strong></label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-addon"><i class="icon-briefcase"></i></span>
                                        <input type="date" name="date_employed" id="date_employed"
                                            class="form-control{{ $errors->has('date_employed') ? 'is-invalid' : '' }}" 
                                            required value="{{ old('date_employed') }}" 
                                            placeholder="Your date of employment (mm/dd/YYYY)" title="Your date of employment">
                                    </div>    
                                </div>
                                
                                <div class="form-group">
                                    <label class="control-label" for="date_confirmed"><strong>Date of Employment Confirmation</strong></label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-addon"><i class="icon-briefcase"></i></span>
                                        <input type="date" name="date_confirmed" id="date_confirmed"
                                            class="form-control{{ $errors->has('date_confirmed') ? 'is-invalid' : '' }}" 
                                            required value="{{ old('date_confirmed') }}" 
                                            placeholder="Date of your employment confirmation" title="Date of your employment confirmation">
                                    </div>    
                                </div>
                                
                                <!--<div class="form-group">
                                    <label class="control-label" for="monthly_salary"><strong>Monthly Salary</strong></label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-addon">₦</span>
                                        <input type="number" name="monthly_salary" id="monthly_salary"
                                            class="form-control{{ $errors->has('monthly_salary') ? 'is-invalid' : '' }}" 
                                            required value="{{ old('monthly_salary') }}" 
                                            placeholder="How much do you earn in a month" title="How much do you earn in a month">
                                    </div>    
                                </div>
                                
                                <div class="form-group">
                                    <label class="control-label" for="gross_pay"><strong>Gross Pay</strong></label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-addon">₦</span>
                                        <input type="number" name="gross_pay" id="gross_pay"
                                            class="form-control{{ $errors->has('gross_pay') ? 'is-invalid' : '' }}" 
                                            required value="{{ old('gross_pay') }}" 
                                            placeholder="Gross Pay" title="Gross Pay">
                                    </div>    
                                </div>-->
                                
                                <div class="form-group">
                                    <label class="control-label" for="net_pay"><strong>Net Salary</strong></label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-addon">₦</span>
                                        <input type="number" name="net_pay" id="net_pay"
                                            class="form-control{{ $errors->has('net_pay') ? 'is-invalid' : '' }}" 
                                            required value="{{ old('net_pay') }}" 
                                            placeholder="Net Salary" title="Net Salaray">
                                    </div>    
                                </div>
                                
                                <div class="form-group">
                                    <label class="control-label" for="payment_date"><strong>Date of Payment</strong></label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-addon"><i class="icon-briefcase"></i></span>
                                        <input type="date" name="payment_date" id="payment_date"
                                            class="form-control{{ $errors->has('payment_date') ? 'is-invalid' : '' }}" 
                                            required value="{{ old('payment_date') }}" 
                                            placeholder="When do you get paid (mm/dd/YYYY)" title="When do you get paid">
                                    </div>    
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="payment_mode"><strong>Mode of Payment</strong></label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-addon"><i class="icon-credit-card"></i></span>
                                        <select class="form-control{{ $errors->has('payment_mode') ? ' is-invalid': ''}}" name="payment_mode" id="payment_mode" required>
                                            <option disabled>Choose your mode of payment</option>
                                            <option value="1" {{(Auth::guard('web')->user()->payment_mode === 1) ? 'selected': ''}}>Cash</option>
                                            <option value="2" {{(AutH::guard('web')->user()->payment_mode === 2) ? 'selected': ''}}>Credit Card</option>
                                        </select>
                                    </div>
                                </div>
                                <div id="employer_section">
                                    <div class="form-group">
                                        <label class="control-label" for="employer_name"><strong>Employer Name</strong></label>
                                        <div class="input-group mb-3">
                                            <span class="input-group-addon"><i class="icon-user-follow"></i></span>
                                            <input type="text" name="employer_name" id="employer_name"
                                                class="form-control{{ $errors->has('employer_name') ? 'is-invalid' : '' }}" 
                                                required value="{{ old('employer_name') ? old('employer_name') : Auth::guard('web')->user()->employer_name }}" 
                                                placeholder="Name of your Employer" title="Name of your Employer">
                                        </div>    
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="control-label" for="employer_phone"><strong>Employer Phone</strong></label>
                                        <div class="input-group mb-3">
                                            <span class="input-group-addon"><i class="icon-phone"></i></span>
                                            <input type="number" name="employer_phone" id="employer_phone"
                                                class="form-control{{ $errors->has('employer_phone') ? 'is-invalid' : '' }}" 
                                                required value="{{ old('employer_phone') ? old('employer_phone') : Auth::guard('web')->user()->employer_phone }}" 
                                                placeholder="Enter the phone number of your employer" title="Phone number of your employer">
                                        </div>    
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="control-label" for="employer_address"><strong>Employer Address</strong></label>
                                        <div class="input-group mb-3">
                                            <span class="input-group-addon"><i class="icon-location-pin"></i></span>
                                            <input type="text" name="employer_address" id="employer_address"
                                                class="form-control{{ $errors->has('employer_address') ? 'is-invalid' : '' }}" 
                                                required value="{{ old('employer_address') ? old('employer_address') : Auth::guard('web')->user()->employer_address }}" 
                                                placeholder="Address of your Employer" title="Address of your Employer">
                                        </div>    
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="control-label" for="employer_state"><strong>Employer State</strong></label>
                                        <div class="input-group mb-3">
                                            <span class="input-group-addon"><i class="icon-map"></i></span>
                                            <input type="text" name="employer_state" id="employer_state"
                                                class="form-control{{ $errors->has('employer_state') ? ' is-invalid' : '' }}" 
                                                required value="{{ old('employer_state') ? old('employer_state') : Auth::guard('web')->user()->employer_state }}" 
                                                placeholder="State of your Employer" title="State of your Employer">
                                        </div>    
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="control-label" for="supervisor_name"><strong>Supervisor Name</strong></label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-addon"><i class="icon-user-follow"></i></span>
                                        <input type="text" name="supervisor_name" id="supervisor_name"
                                            class="form-control{{ $errors->has('supervisor_name') ? ' is-invalid' : '' }}" 
                                            required value="{{ old('supervisor_name') }}" 
                                            placeholder="Name of your Supervisor" title="Name of your Supervisor">
                                    </div>    
                                </div>
                                
                                <div class="form-group">
                                    <label class="control-label" for="supervisor_phone"><strong>Supervisor Phone</strong></label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-addon"><i class="icon-phone"></i></span>
                                        <input type="number" name="supervisor_phone" id="supervisor_phone"
                                            class="form-control{{ $errors->has('supervisor_phone') ? 'is-invalid' : '' }}" 
                                            required value="{{ old('supervisor_phone') }}" 
                                            placeholder="Enter the phone number of your supervisor" title="Phone number of your supervisor">
                                    </div>    
                                </div>
                                
                                <div class="form-group">
                                    <label class="control-label" for="employer_address"><strong>Supervisor Grade</strong></label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-addon"><i class="icon-layers"></i></span>
                                        <input type="text" name="supervisor_grade" id="supervisor_grade"
                                            class="form-control{{ $errors->has('supervisor_grade') ? 'is-invalid' : '' }}" 
                                            required value="{{ old('supervisor_grade') }}" 
                                            placeholder="Grade of your Supervisor" title="Grade of your Supervisor">
                                    </div>    
                                </div>
                                <button type="submit" class="btn btn-block btn-success">Save Employment Information</button>
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
  <script>
    function computeExpectedPayback() {
      var amount = parseInt($("#amount").val());
      var percentage = parseInt($("#interest_percentage").val());
      var payback = amount + (amount * percentage / 100);
      if(isNaN(payback)) {
        $("#payback").val("");
        return;
      }
      $("#payback").val(payback);
    }
  </script>
@endsection