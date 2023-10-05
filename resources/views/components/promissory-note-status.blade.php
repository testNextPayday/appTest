@switch($note->status)
    @case(0)
        <span class="badge badge-primary text-white">Pending</span>
    @break
    @case(1)
        <span class="badge badge-primary text-white">Active</span>
    @break

    @case(2)
        <span class="badge badge-success text-white">Ended</span>
    @break

    @case(3)
        <span class="badge badge-success text-white">Rolled Over</span>
    @break

    @case(4)
        <span class="badge badge-warning text-white">Liquidated</span>
    @break

@endswitch