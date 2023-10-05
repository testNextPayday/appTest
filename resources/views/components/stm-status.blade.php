@switch($settlement->status)
   
    @case(1)
        <span class="badge badge-warning">Pending Admin Approval</span>
        @break
    @case(2)
        <span class="badge badge-success">Confirmed</span>
        @break
    @case(3)
        <span class="badge badge-danger">Declined</span>
        @break
    @case(6)
    @default
        <span class="badge badge-danger">Declined - Admin</span>
@endswitch