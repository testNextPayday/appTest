@extends('layouts.affiliates')


@section('page-css')

@endsection


@section('content')

<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">Message History</h4>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            @php($entityName = $entity instanceof \App\Models\Admin ? 'ADMIN' : $entity->name)
            @php($userClass = str_replace('\\', "\\\\", get_class($user)))
            <shared-conversation
                :existing-messages="{{ $messages }}"
                :entity-name="'{{ $entityName }}'"
                :send-route="'{{ route('affiliates.messages.send-admin') }}'"
                :user="{{ $user }}"
                :user-class="'{{ $userClass }}'"
                :code="'{{App\Helpers\Constants::AFFILIATE_CODE}}'"
                :base="'affiliates'"></shared-conversation>
        </div>
    </div>
</div>
@endsection