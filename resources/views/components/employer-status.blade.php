@switch($status)
    @case (0)
    <span class="badge badge-warning">Not Verified</span>
    @break
    @case (1)
    <span class="badge badge-info">Under Verification</span>
    @break
    @case (2)
    <span class="badge badge-danger">Verification Denied</span>
    @break
    @case (3)
    <span class="badge badge-success">Verified</span>
    @break
    
    @default
    <span class="badge badge-default">Unknown</span>
@endswitch