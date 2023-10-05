@extends('layouts.admin-new')

@section('content')
<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">
                App Settings
                <span class="badge badge-danger">
                    <i class=" fa fa-cogs"></i>
                </span>
            </h4>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-sm-6">

            @include('layouts.shared.error-display')

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{route('admin.settings.update') }}">
                        {{ csrf_field() }}

                        @foreach($settings as $setting)
                        @if($setting->slug == 'enable_loan_request' || $setting->slug == 'bank_statement_enabled')
                        <div class="form-group">
                            <label for="{{$setting->slug}}">{{$setting->name}}</label>
                            <select class="form-control" id="{{$setting->slug}}" name="{{$setting->slug}}">
                                <option value="1" {{$setting->value == '1' ? 'selected' : ''}}>Yes</option>
                                <option value="0" {{$setting->value == '0' ? 'selected' : ''}}>No</option>
                            </select>
                        </div>
                        @continue

                        @endif

                        @if($setting->slug == "salary_now_employer")
                        <div class="form-group">
                            <label for="{{$setting->slug}}">{{$setting->name}}</label>
                            <select class="form-control" id="{{$setting->slug}}" name="{{$setting->slug}}">
                                @foreach($employers as $employer)
                                <option value="{{$employer->id}}" {{$employer->id == $setting->value ? 'selected' : ''}}>{{$employer->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        @continue
                        @endif
                        <div class="form-group">
                            <label for="{{$setting->slug}}">{{$setting->name}}</label>
                            <input type="text" class="form-control" id="{{$setting->slug}}" name="{{$setting->slug}}" value="{{old($setting->slug) ? old($setting->slug) : $setting->value}}" placeholder="{{$setting->name}}">
                        </div>



                        @endforeach

                        @if(count($settings))
                        <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-dot-circle-o"></i> Update Values</button>
                        @endif
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('page-js')
@endsection