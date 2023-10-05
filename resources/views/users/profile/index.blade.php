@extends('layouts.user')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="#">Profile</a></li>
        <li class="breadcrumb-item active">My Profile</li>

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
            @if ($errors->any())
                <div class="row justify-content-center">
                    <div class="col-sm-6">
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
            <div class="row">
                <div class="col-md-12 mb-4">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#basic" role="tab" aria-controls="basic">
                                <i class="icon-user"></i> Basic &nbsp;
                                @if(Auth::user()->basicProfileIsComplete()) 
                                <span class="badge badge-pill badge-success"><i class="icon-check"></i></span>
                                @else
                                <span class="badge badge-pill badge-danger"><i class="icon-close"></i></span>
                                @endif
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#work" role="tab" aria-controls="work">
                                <i class="icon-briefcase"></i> Work &nbsp;
                                @if(count(Auth::user()->employments)) 
                                <span class="badge badge-pill badge-success"><i class="icon-check"></i></span>
                                @else
                                <span class="badge badge-pill badge-danger"><i class="icon-close"></i></span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#bankDetails" role="tab" aria-controls="bankDetails">
                                <i class="icon-grid"></i> Bank Details &nbsp;
                                @if(count(Auth::user()->bankDetails)) 
                                <span class="badge badge-pill badge-success"><i class="icon-check"></i></span>
                                @else
                                <span class="badge badge-pill badge-danger"><i class="icon-close"></i></span>
                                @endif
                            </a>
                        </li>
                        @if(config('unicredit.modules.phone_verification'))
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#verifications" role="tab" aria-controls="verifications">
                                <i class="icon-like"></i> Verifications &nbsp;
                                @if(Auth::user()->phone_verification) 
                                <span class="badge badge-pill badge-success"><i class="icon-check"></i></span>
                                @else
                                <span class="badge badge-pill badge-danger"><i class="icon-close"></i></span>
                                @endif
                            </a>
                        </li>
                        @endif
              
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane active" id="basic" role="tabpanel">
                            <div class="row justify-content-center my-3">
                                <div class="col-sm-10">
                                  <form class="form-horizontal" method="POST" action="{{route('users.profile.basic.update')}}" enctype="multipart/form-data">
                                        {{ csrf_field() }}
                                        <div class="row">
                                            <div class="form-group col-sm-6">
                                                <label class="control-label" for="name"><strong>Name</strong></label>
                                                <div class="input-group mb-3">
                                                  <span class="input-group-addon"><i class="icon-user"></i></span>
                                                    <input type="text" name="name" id="name"
                                                        class="form-control{{ $errors->has('name') ? 'is-invalid' : '' }}" 
                                                        required value="{{ old('name') ? old('midname') : Auth::guard('web')->user()->name }}" 
                                                        placeholder="Name" title="Your Name">
                                                </div>
                                            </div>
                                            
                                            <div class="form-group col-sm-6">
                                              <label class="control-label" for="phone"><strong>Phone Number</strong></label>
                                                <div class="input-group mb-3">
                                                    <span class="input-group-addon"><i class="icon-phone"></i></span>
                                                    <input type="number" name="phone" id="phone" maxlength="11"
                                                        class="form-control{{ $errors->has('phone') ? 'is-invalid' : '' }}" 
                                                        required value="{{ old('phone') ? old('phone') : Auth::guard('web')->user()->phone }}"
                                                        placeholder="Phone Number" title="Your Phone Number">
                                                </div>    
                                            </div>

                                   <div class="form-group col-sm-6">
                                                <label class="control-label" for="bvn"><strong>BVN</strong></label>
                                                <div class="input-group mb-3">
                                                    <span class="input-group-addon"><i class="icon-phone"></i></span>
                                                    <input type="number" name="bvn" id="bvn" maxlength="11"
                                                        class="form-control{{ $errors->has('bvn') ? 'is-invalid' : '' }}" 
                                                        required value="{{ old('bvn') ? old('bvn') : Auth::guard('web')->user()->bvn }}"
                                                        placeholder="bvn Number" title="Your bvn Number">
                                                </div>    
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="form-group col-sm-6">
                                                <label class="control-label" for="email"><strong>Email Address</strong></label>
                                                <div class="input-group mb-3">
                                                    <span class="input-group-addon">@</span>
                                                    <input type="email" name="email" id="email"
                                                        class="form-control{{ $errors->has('email') ? 'is-invalid' : '' }}" 
                                                        required value="{{ Auth::guard('web')->user()->email }}" 
                                                        pbblaceholder="Email" readonly>
                                                </div> 
                                            </div>
                                            
                                            <div class="form-group col-sm-6">
                                                <label class="control-label" for="avatar"><strong>Profile Picture</strong></label>
                                                <div class="input-group mb-3">
                                                    <span class="input-group-addon"><i class="icon-user"></i></span>
                                                    <input type="file" name="avatar" id="avatar"
                                                        class="form-control{{ $errors->has('avatar') ? 'is-invalid' : '' }}" 
                                                        title="Select a profile image">
                                                </div>    
                                            </div>
                                            
                                        
                                        <!-- <div class="row"> -->
                                            <div class="form-group col-sm-6">
                                                <label class="control-label" for="gender"><strong>Gender</strong></label>
                                                <div class="input-group mb-3">
                                                    <span class="input-group-addon"><i class="icon-puzzle"></i></span>
                                                    <select class="form-control{{ $errors->has('gender') ? ' is-invalid': ''}}" name="gender" id="gender" required>
                                                        <option disabled>Choose your gender</option>
                                                        <option value="1" {{(Auth::guard('web')->user()->gender === 1) ? 'selected': ''}}>Male</option>
                                                        <option value="2" {{(Auth::guard('web')->user()->gender === 2) ? 'selected': ''}}>Female</option>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group col-sm-6">
                                                <label class="control-label" for="dob"><strong>Date of Birth</strong></label>
                                                <div class="input-group mb-3">
                                                    <span class="input-group-addon"><i class="icon-present"></i></span>
                                                    <input type="date" name="dob" id="dob"
                                                        class="form-control{{ $errors->has('dob') ? 'is-invalid' : '' }}" 
                                                        required value="{{ old('dob') ? old('dob') : Auth::guard('web')->user()->dob }}" 
                                                        placeholder="Your Birthday - mm/dd/YYYY" title="Your Date of Birth">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="control-label" for="occupation"><strong>Your Profession</strong></label>
                                            <div class="input-group mb-3">
                                                <span class="input-group-addon"><i class="icon-anchor"></i></span>
                                                <input type="text" name="occupation" id="occupation"
                                                    class="form-control{{ $errors->has('occupation') ? 'is-invalid' : '' }}" 
                                                    required value="{{ old('occupation') ? old('occupation') : Auth::guard('web')->user()->occupation }}" 
                                                    placeholder="Your Profession" title="Your Profession">
                                            </div>    
                                        </div>
                                        
                                        <div class="row">
                                            <div class="form-group col-sm-6">
                                                <label class="control-label" for="passport"><strong>Work ID Card</strong></label>
                                                <div class="input-group mb-3">
                                                    <span class="input-group-addon"><i class="icon-user"></i></span>
                                                    <input type="file" name="passport" id="passport"
                                                        class="form-control{{ $errors->has('passport') ? 'is-invalid' : '' }}" 
                                                        title="Select your passport">
                                                </div>    
                                            </div>
                                            
                                            <div class="form-group col-sm-6">
                                                <label class="control-label" for="govt_id_card"><strong>Govt. Issued ID Card</strong></label>
                                                <div class="input-group mb-3">
                                                    <span class="input-group-addon"><i class="icon-user"></i></span>
                                                    <input type="file" name="govt_id_card" id="govt_id_card"
                                                        class="form-control{{ $errors->has('govt_id_card') ? 'is-invalid' : '' }}" 
                                                        title="Select your government issued ID Card">
                                                </div>    
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="form-group col-sm-6">
                                                <label class="control-label" for="address"><strong>Address</strong></label>
                                                <div class="input-group mb-3">
                                                    <span class="input-group-addon"><i class="icon-compass"></i></span>
                                                    <input type="text" name="address" id="address" 
                                                        class="form-control{{ $errors->has('address') ? 'is-invalid' : '' }}" 
                                                        required value="{{ old('address') ? old('address') : Auth::guard('web')->user()->address }}" 
                                                        placeholder="Address" title="Your Residential Address">
                                                </div>    
                                            </div>
                                            
                                            <div class="form-group col-sm-6">
                                                <label class="control-label" for="lga"><strong>Local Government Area</strong></label>
                                                <div class="input-group mb-3">
                                                    <span class="input-group-addon"><i class="icon-direction"></i></span>
                                                    <input type="text" name="lga" id="lga" 
                                                        class="form-control{{ $errors->has('lga') ? ' is-invalid' : '' }}" 
                                                        required value="{{ old('lga') ? old('lga') : Auth::guard('web')->user()->lga }}" 
                                                        placeholder="Your Local Government Area" title="Your Local Government Area">
                                                </div>    
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="form-group col-sm-6">
                                                <label class="control-label" for="city"><strong>City</strong></label>
                                                <div class="input-group mb-3">
                                                    <span class="input-group-addon"><i class="icon-directions"></i></span>
                                                    <input type="text" name="city" id="city" 
                                                        class="form-control{{ $errors->has('city') ? ' is-invalid' : '' }}" 
                                                        required value="{{ old('city') ? old('city') : Auth::guard('web')->user()->city }}" 
                                                        placeholder="City" title="Your City">
                                                </div>
                                            </div>
                                            
                                            <div class="form-group col-sm-6">
                                                <label class="control-label" for="state"><strong>State</strong></label>
                                                <div class="input-group mb-3">
                                                    <span class="input-group-addon"><i class="icon-directions"></i></span>
                                                    <select name="state" id="state" 
                                                        class="form-control{{ $errors->has('state') ? ' is-invalid' : '' }}" 
                                                        required title="Your State">
                                                        @foreach(config('unicredit.states') as $key => $state)
                                                            <option value="{{$key}}"
                                                                @if(old('state') && old('state') == $key)
                                                                selected
                                                                @elseif(Auth::guard('web')->user()->state == $key)
                                                                selected
                                                                @endif
                                                            >
                                                            {{$state}}
                                                            
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>    
                                            </div>
                                
                                         <button type="submit" class="btn btn-block btn-success">Update Basic Information</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="tab-pane" id="work" role="tabpanel">
                        <employment :user-employments="{{Auth::user()->employments()->latest()->get()}}" 
                                    :employer-add-url="'{{route('users.employer.add')}}'"
                                    :employer-states="{{App\Models\Employer::distinct()->get(['state'])}}" 
                                    :states="{{json_encode(config('unicredit.states'))}}"
                                    :all-employers="{{App\Models\Employer::where('is_primary','0')?->get()}}"
                                    :verification-fee="{{App\Models\Settings::whereSlug('employer_verification_fee')?->first()?->value ?? 0}}"
                                    :wallet="{{Auth::guard('web')->user()->wallet}}"
                                    :email="'{{Auth::guard('web')->user()->email}}'"
                                    :pay-key="'{{config('paystack.publicKey')}}'">
                                    
                         </employment>
                        </div>
                        <div class="tab-pane" id="bankDetails" role="tabpanel">
                            <bank-details :user-bank-details="{{Auth::user()->bankDetails}}" :banks="{{json_encode(config('remita.banks'))}}"></bank-details>
                        </div>
                        @if(config('unicredit.modules.phone_verification'))
                        <div class="tab-pane" id="verifications" role="tabpanel">
                            <div class="row justify-content-center my-3">
                                <div class="col-sm-10">
                                    <fieldset>
                                        <legend>Phone Number</legend>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="icon-phone"></i></span>
                                            <input type="number" name="phone" 
                                                class="form-control{{ $errors->has('phone') ? 'is-invalid' : '' }}" 
                                                required value="{{ old('phone') ? old('phone') : Auth::guard('web')->user()->phone }}"
                                                placeholder="Phone Number" title="Your Phone Number" readonly>
                                            <div class="input-group-append">
                                                <phone-verification 
                                                    :phone-verified="{{Auth::guard('web')->user()->phone_verification}}"
                                                    :phone="'{{Auth::guard('web')->user()->phone}}'"></phone-verification>
                                            </div>
                                        </div>
                                    </fieldset>
                                    <hr/>
                                </div>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
                <!--/.col-->
            </div>
            <!--/.row-->
        </div>

    </div>
    <!-- /.conainer-fluid -->
</main>
@endsection

@section('page-js')
@endsection