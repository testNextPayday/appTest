@extends('layouts.auth')

@section('title')
Register
@endsection

@section('content')
<div class="col-md-6">
  <register-component :redirect="'{{route('email.unverified')}}'"
    :refcode="'{{ $refcode }}'"/>
</div>
@endsection