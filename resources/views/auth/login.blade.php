@extends('layouts.new-auth-2023')

@section('title')
    Login
@endsection

@section('page-css')
    <style>
        @media screen and (min-width: 1000px) {
            .investor-login-section {
                display: none;
            }
        }
    </style>
@endsection

@section('auth_content')
    <div class="col-md-12">
        <div class="card-group">
            <div class="">
                <div class="">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form class="form-horizontal" method="POST" action="{{ route('login') }}">
                        {{ csrf_field() }}

                        <div class="input-group mb-3">
                            <input type="text" name="phone"
                                class="form-control {{ $errors->has('phone') ? 'is-invalid' : '' }}" required
                                value="{{ old('phone') }}" placeholder="Phone">
                        </div>

                        <div class="input-group mb-3">
                            <input type="password" name="password"
                                class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}" required
                                placeholder="Password">
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary px-4">Login</button>
                            </div>
                            <div class="col-12 text-right">
                                <!-- <a class="btn btn-link px-0" href="{{ url('password/reset') }}">Forgot password?</a> -->
                                <a class="btn btn-link px-0" href="{{ route('password.request.phone') }}">Forgot
                                    password?</a>
                            </div>
                        </div>
                    </form>
                    <br />
                    <div class="row investor-login-section">
                        <div class="col-12 text-center">
                            <a href="{{ route('investors.login') }}" class="btn btn-block btn-danger mt-3"
                                style="background-color:#a61e23">
                                LOGIN AS AN INVESTOR
                            </a>
                        </div>
                        <br /><br /><br />
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <p>Dont have an account? <a class="btn btn-link px-0" href="https://nextpayday.co/signup">Sign
                                    up here</a></p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
