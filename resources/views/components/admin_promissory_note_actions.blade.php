@if ($promissoryNote->status == 0)
    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#approve-note">
        <i class="fa fa-link"></i>
        Approve Note
    </button>
@endif

@if($promissoryNote->status == 1)

    @if ($promissoryNote->monthsLeft > 0)

        <form  method="POST" style="display:inline;" action="{{route('admin.promissory-notes.liquidate', ['promissory-note'=> $promissoryNote->reference])}}">
            @csrf
            <button type="submit" class="btn btn-info btn-sm">
                <i class="fa fa-link"></i>
                Liquidate Note
            </button>
        </form>
    @else 

        <form method="POST" style="display:inline;" action="{{route('admin.promissory-notes.withdraw', ['promissory-note'=> $promissoryNote->reference])}}">
            @csrf
            <button type="submit" class="btn btn-danger btn-sm">
                <i class="fa fa-link"></i>
                Create Withdraw
            </button>
        </form>

        <form method="POST" style="display:inline;"  action="{{route('admin.promissory-notes.rollover', ['promissory-note'=> $promissoryNote->reference])}}">
            @csrf
            <button type="submit" class="btn btn-success btn-sm">
                <i class="fa fa-link"></i>
                Rollover Note
            </button>
        </form>

    @endif
@endif