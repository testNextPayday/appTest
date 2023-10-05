@switch($loanRequest->status)
    @case(0)
        <span class="text-danger">Inactive - Waiting for employer approval</span>
        @break
    @case(1)
        <span class="text-warning">Pending Admin Approval</span>
        @break
    @case(2)
        <span class="text-success">Active</span>
        @break
    @case(3)
        <span class="text-danger">Cancelled</span>
        @break
    @case(4)
        <span class="text-success">Taken</span>
        @break
    @case(5)
        <span class="text-danger">Declined - Employer</span>
        @break
    @case(7)
        <span class="text-warning">Referred - Admin</span>
        @break
    @case(6)
        <span class="text-danger">Declined - Admin</span>
        @break
    @default
@endswitch