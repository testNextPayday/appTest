@extends('layouts.admin-new')

@section('content')

<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">Add Employer</h4>
        </div>
    </div>
    
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
        <div class="col-sm-12">
              
                <manage-employer-form
                    :states="{{ json_encode(config('unicredit.states')) }}"
                    :collection-plans="{{ json_encode(config('settings.collection_methods')) }}"
                    :existing-employer="{{ $employer ?? '\'\'' }}"
                    :primary-employers="{{$primaryEmployers}}">
                </manage-employer-form>
        </div>
    </div>
</div>
@endsection

@section('page-js')


@endsection