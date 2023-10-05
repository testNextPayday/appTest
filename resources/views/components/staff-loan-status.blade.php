
@switch($loan->status)

@case(0)

    <span class="badge badge-primary">
        <i class="fa fa-spinner"></i>
        Pending
    </span>

    @if($loan->trashed())

        @if($loan->canRestore())
            <form action="{{route('admin.loans.restorevoid',['reference'=>$loan->reference])}}" method="POST"
                style="display:inline">
                @csrf
                <button class="btn">Restore Void</button>
            </form>
        @endif

    @else

        <!-- <form action="{{route('admin.loans.markasvoid',['reference'=>$loan->reference])}}" method="POST" style="display:inline">
            @csrf
            <button class="btn btn-warning">Move To Void</button>
        </form> -->

        <form action="{{route('admin.loans.markasinactive',['reference'=>$loan->reference])}}" method="POST"
            style="display:inline">
            @csrf
            <button class="btn btn-danger">Mark as Cool-off</button>
        </form>

        <form action="{{route('admin.loans.dissolveloan',['reference'=>$loan->reference])}}" method="POST"
            style="display:inline">
            @csrf
            <button class="btn btn-primary">Dissolve Loan</button>
        </form>

    @endif
@break

@case(1)       
    
    <span class="badge badge-primary">
        <i class="fa fa-dot-circle-o"></i>
        Active
    </span>
    
    @if(request()->user('admin'))           
       
        <form action="{{route('admin.loans.markasmanaged',['reference'=>$loan->reference])}}" method="POST"
            style="display:inline">
            @csrf
            <button class="btn btn-danger">Move To Managed</button>
        </form>

        <form action="{{route('admin.loans.markasinactive',['reference'=>$loan->reference])}}" method="POST"
            style="display:inline">
            @csrf
            <button class="btn btn-danger">Mark as Cool-off</button>
        </form>

        @if($loan->trashed())

            @if($loan->canRestore())

                <form action="{{route('admin.loans.restorevoid',['reference'=>$loan->reference])}}" method="POST"
                    style="display:inline">
                    @csrf
                    <button class="btn">Restore Void</button>
                </form>

            @endif

        @else

            <!-- <form action="{{route('admin.loans.markasvoid',['reference'=>$loan->reference])}}" method="POST" style="display:inline">
                @csrf
                <button class="btn btn-warning">Move To Void</button>
            </form> -->

        @endif

        

    @endif
    
@break

@case(2)

    <span class="badge badge-success">
        <i class="fa fa-check"></i>
        Fulfilled
    </span>
    @break

@case(3)

    <form action="{{route('admin.loans.markasactive',['reference'=>$loan->reference])}}" method="POST"
        style="display:inline">
        @csrf
        <button class="btn btn-success">Mark as Active</button>
    </form>
    @break

@case(5)

    <span class="badge badge-danger disabled">
        <i class="fa fa-dot-circle-o"></i>
        Managed
    </span>

    @if(request()->user('admin'))

        <form action="{{route('admin.loans.markasactive',['reference'=>$loan->reference])}}" style="display:inline"
            method="POST">
            @csrf
            <button class="btn btn-secondary">Restore</button>
        </form>

    @endif
    @break

@default

<span class="badge badge-danger">
    <i class="fa fa-close"></i>
    Defaulting
</span>

@endswitch