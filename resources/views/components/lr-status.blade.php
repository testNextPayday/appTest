@switch($loanRequest->status)
    @case(0)
        <span class="badge badge-danger">Pending Employer approval</span>
        @break
    @case(1)
        <span class="badge badge-warning">Pending Admin Approval</span>
        @break
    @case(2)
        <span class="badge badge-success">Active</span>
        @break
    @case(3)
        <span class="badge badge-danger">Cancelled</span>
        @break
    @case(4)
        <span class="badge badge-success">Taken</span>
        @break
    @case(5)
        <span class="badge badge-danger">Declined - Employer</span>
        @break
    @case(7)
        <span class="badge badge-warning">Referred - Admin</span>
        @break
    @case(6)
    @default
        <span class="badge badge-danger">Declined - Admin</span>
@endswitch