@extends('layouts.admin-new')

@section('content')

<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">Conversations</h4>
        </div>
    </div>
    
    
    <div class="row">
        <div class="col-sm-8">
            <div class="card">
                <div class="card-body">
                    @forelse($conversations as $conversation)
                        @php($avatar = $conversation->participant->avatar ?? asset(Storage::url('public/defaults/avatars/default.png')) )
                        
                        <?php
                            if ($conversation->participant instanceof App\Models\Affiliate) {
                                $route =  route('admin.communications.conversations.show', ['entityCode' => '004', 'entityId' => $conversation->participant->id]);
                            } else {
                                $route = "#";
                            }
                        ?>
                        
                        <div class="wrapper d-flex align-items-center py-2 border-bottom">
                            <img class="img-sm rounded-circle" src="{{ $avatar}}" alt="profile">
                            <div class="wrapper ml-3">
                                <h6 class="mb-1">
                                    <a href="{{ $route }}" class="text-info">
                                        {{ $conversation->participant->name }}
                                    </a>
                                </h6>
                                <small class="text-muted mb-0">
                                    <i class="icon-clock mr-1"></i>{{ $conversation->lastMessage->created_at->diffForHumans() }}
                                </small>
                            </div>
                            @if ($conversation->unread)
                            <div class="badge badge-pill badge-dark ml-auto px-1 py-1" style="border-radius:0">
                                <span class="font-weight-bold">{{ $conversation->unread }} Unread</span>
                            </div>
                            @endif
                        </div>
                        
                        
                    @empty
                        <div class="wrapper d-flex align-items-center py-2 border-bottom">
                            <button class="img-sm rounded-circle">
                                <i class="text-danger fa fa-close"></i>
                            </button>
                            <div class="wrapper ml-3">
                                <h6 class="ml-1 mb-1">
                                    No conversations
                                </h6>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection