@php($owner = $conversation->owner)
<div class="card">
    <div class="card-header">
        <span class="float-right">
            {{date('dS F Y (H:m a)', strtotime($conversation->created_at))}}
        </span>
        <i class="fa fa-user"></i>
        <span>{{$owner->name}} - </span>
        <span>
            @switch(get_class($owner)) 
                @case ('App\Models\Staff')
                @case ('App\Models\Admin')
                    <b>{{('Staff')}}</b>
                @break
                @default
                    <b>{{('Client')}}</b>
            @endswitch
        </span>
    </div>

    <div class="card-body">
        <p class="message">{{$conversation->message}}</p>
    </div>

    <div class="card-footer">
        @if($conversation->files)
            <<<< An image was attached to the complaint <a href="{{$conversation->files}}" target="_blank">View Image</a>
        @endif
    </div>
</div>