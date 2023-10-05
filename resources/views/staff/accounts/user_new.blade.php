@extends('layouts.staff-new')

@section('content')
<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">
                Add New
            </h4>
        </div>
    </div>
    
    
    @include('layouts.shared.error-display')
    
    <div class="row justify-content-center">
        <div class="col-sm-10">
            <new-user
                :employer-states="{{App\Models\Employer::distinct()->get(['state'])}}" 
                :all-employers="{{App\Models\Employer::where('is_primary',false)->get()}}"
                :url="'{{route('staff.accounts.storeV2')}}'"
                :uploads-url="'{{route('staff.accounts.uploadDocs')}}'"
                :employer-add-url="'{{route('staff.employers.add')}}'"
                :banks="{{json_encode(config('remita.banks'))}}"
                :states="{{json_encode(config('unicredit.states'))}}"
            >
            </new-user>
        </div>
    </div>
</div>
@endsection

@section('page-js')
@endsection