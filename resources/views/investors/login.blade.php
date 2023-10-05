@extends('layouts.investor-auth.master')
@section('content')
    <section class="register">
      <div style="display: none" class="checking">
        <div class="checking_block">
          <img class="checking_icon" src="{{asset('assets/usericon.png')}}" />
          <p>verifying ...</p>
        </div>
      </div>
      <div id="reg_con" class="container">
        <div class="form_block">
          <h2 class="form_title">Login as an Investor</h2>

          <p>
            Don't Have an Account?
            <a href="{{route('investors.signup')}}">
              <span class="reg_sub-t">SignUp Here </span>
            </a>
          </p>

          <form method="post" action="{{route('investors.verify.signin')}}">
              @csrf
            <div class="form-group mt-5">
                
                <div class="input_cover">
                <input
                    id="Phone"
                    class="form_input"
                    type="text"
                    placeholder="phone number"
                    name="phone"
                    required
                />
                <label for="Phone" class="input_block">
                    <span class="input_icon"
                    ><svg
                        xmlns="http://www.w3.org/2000/svg"
                        focusable="false"
                        style="transform: rotate(360deg)"
                        class="text-gray-900"
                        width="1em"
                        height="1em"
                        preserveAspectRatio="xMidYMid meet"
                        viewBox="0 0 1408 1408"
                    >
                        <path
                        d="M1408 1112q0 27-10 70.5t-21 68.5q-21 50-122 106q-94 51-186 51q-27 0-53-3.5t-57.5-12.5t-47-14.5T856 1357t-49-18q-98-35-175-83q-127-79-264-216T152 776q-48-77-83-175q-3-9-18-49t-20.5-55.5t-14.5-47T3.5 392T0 339q0-92 51-186Q107 52 157 31q25-11 68.5-21T296 0q14 0 21 3q18 6 53 76q11 19 30 54t35 63.5t31 53.5q3 4 17.5 25t21.5 35.5t7 28.5q0 20-28.5 50t-62 55t-62 53t-28.5 46q0 9 5 22.5t8.5 20.5t14 24t11.5 19q76 137 174 235t235 174q2 1 19 11.5t24 14t20.5 8.5t22.5 5q18 0 46-28.5t53-62t55-62t50-28.5q14 0 28.5 7t35.5 21.5t25 17.5q25 15 53.5 31t63.5 35t54 30q70 35 76 53q3 7 3 21z"
                        fill="currentColor"
                        ></path></svg
                    ></span>
                    <span class="input_label"> Phone Number</span>
                </label>
                </div>
                <div class="form-group  mt-5">
                        <div class="input_cover">
                        <input
                            id="password"
                            class="form_input"
                            type="password"
                            placeholder="password"
                            name="password"                            
                        />
                        <label for="password" class="input_block">
                            <span class="">
                            <img
                                style="max-width: 25px"
                                class="input_icon"
                                src="{{asset('investorpage/assets/usericon.png')}}"
                            /></span>
                            <span class="input_label">Password</span>
                        </label>
                        </div>
                    </div>
            </div>
            <div class="actions">
                <input type="submit" class="confirm_btn form_btn" value="Login">
                <a href="{{route('investors.passwords.forgot')}}" style="margin-left:200px;"> Forgot Password </a> 
            </div>
         </form>
        </div>
      </div>
    </section>


   @endsection