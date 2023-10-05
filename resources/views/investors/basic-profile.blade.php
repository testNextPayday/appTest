@extends('layouts.investor-auth.master')
@section('content')
<section class="register">
        <div id="reg_con" class="container">
          <div class="row">
            <div class="form_block">
              <h2 class="form_title">Provide Your Profile Information</h2>
              <br />
                <form action="{{route('investors.save.profile.data')}}" method="post">
                    @csrf
                    <div class="form-group">
                        <div class="input_cover">

                        <input
                            id="name"
                            class="form_input"
                            type="text"
                            placeholder="Full Name"
                            name="name"
                            
                            required
                        />

                        <label for="name" class="input_block">
                            <span class="">
                            <img class="input_icon" src="{{asset('investorpage/assets/usericon.png')}}"
                            /></span>
                            <span class="input_label"> Name</span>
                        </label>

                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input_cover">
                        <input
                            id="email"
                            class="form_input"
                            type="email"
                            name="email"
                            placeholder="Email"
                            required                            
                        />
                        <label for="email" class="input_block">
                            <span class="">
                            <img
                                style="max-width: 25px"
                                class="input_icon"
                                src="{{asset('investorpage//assets/emailicon.png')}}"
                            /></span>
                            <span class="input_label"> Email</span>
                        </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input_cover">
                            <input
                                id="phone"
                                class="form_input"
                                type="text"
                                placeholder="Phone Number"
                                name="phone"
                                required
                            />
                            <label for="phone" class="input_block">
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
                    </div><br>
                    <div class="form-group">
                        <div class="input_cover">
                           <select style='z-index:33' name="role" class="form_input" id="category" required>
                                <option  value="1" >Colend</option>
                                <option value="2">Payday Note</option>                                                             
                            </select>
                        
                            <label for="category" class="input_block">
                                <span class="">
                                <img
                                    style="max-width: 20px"
                                    class="input_icon"
                                    src="{{asset('investorpage/assets/caticon.png')}}"
                                /></span>
                                <span class="input_label">Select Category</span>
                            </label>

                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input_cover">
                        <input
                            id="password"
                            class="form_input"
                            type="password"
                            placeholder="test"
                            name="password"
                            value="{{$profile->password ?? ''}}"
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

                    <div class="form-group">
                        <div class="input_cover">
                        <input
                            id="referal"
                            class="form_input"
                            type="text"
                            placeholder="test"
                            name="ref_code"
                            value="{{$profile->ref_code ?? ''}}"
                        />
                        <label for="referal" class="input_block">
                            <span class="">
                            <img
                                style="max-width: 25px"
                                class="input_icon"
                                src="{{asset('investorpage/assets/reficon.png')}}"
                            /></span>
                            <span class="input_label">Referal Code</span>
                        </label>
                        </div>
                    </div>
                    <div class="actions">
                        <button type="submit" class="confirm_btn form_btn">Next</button>
                    </div>
                </form>
            </div>
          </div>
        </div>
      </section>
@endsection