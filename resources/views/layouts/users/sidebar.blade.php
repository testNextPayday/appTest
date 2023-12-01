<div class="sidebar">
    <nav class="sidebar-nav">
        <ul class="nav">

            <li class="nav-item text-center mt-3">
                <img src="{{Auth::guard('web')->user()->avatar}}" class="img-fluid img-thumbnail img-circle" style="width:70%;" alt="Display Image">
                <h6>{{Auth::guard()->user()->name}}</h6>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{url('/')}}"><i class="icon-speedometer"></i> Dashboard</a>
            </li>

            
            <li class="nav-title">
                My Financials
            </li>
            
            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-diamond"></i>My Loan Requests</a>
                <ul class="nav-dropdown-items">
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('users.loan-requests.create') }}"><i class="icon-star"></i> New</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('users.loan-requests.index')}}"><i class="icon-star"></i> View All</a>
                    </li>
                </ul>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="{{route('users.loans.received')}}">
                    <i class="icon-credit-card"></i>Received Loans</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{route('users.refund.index')}}">
                    <i class="icon-credit-card"></i>My Refunds</a>
            </li>

            <li class="nav-title">
                Saving
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{route('users.savings.index')}}">
                <i class="icon-credit-card"></i>My Savings</a>
            </li>
            
            <li class="nav-title">
                Transactions
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="{{route('users.transactions.wallet')}}"><i class="icon-calculator"></i> Wallet Transactions</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{route('users.withdrawals.index')}}"><i class="icon-tag"></i> My Withdrawals</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{route('users.notification.index')}}">
                    <i class="icon-tag"></i>Notification Center
                    
                    </a>
                    @if($count = Auth::user()->unreadNotifications->count())
                        <span class="badge badge-danger" style="position: relative;
    top: -45px;
    left: 170px">{{$count}}</span>
                    @endif
            </li>


            <li class="nav-title">
                Support
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{route('users.ticket.index')}}"><i class="icon-calculator"></i> My Tickets</a>
            </li>
            
            <li class="divider"></li>
            <li class="nav-title">
                Me
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{route('users.profile.index')}}"><i class="icon-user"></i> My Profile</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{route('users.billing.index')}}"><i class="icon-credit-card"></i> Billing</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{route('logout')}}"
                    onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                    <i class="icon-logout"></i> Logout</a>
                
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
            </li>
            <li class="nav-item mt-auto" data-toggle="modal" data-target="#fundWallet">
                <a class="nav-link nav-link-success" href="#">
                    <i class="icon-credit-card"></i> Fund Your Wallet</a>
            </li>
            <li class="nav-item">
                <a class="nav-link nav-link-primary" href="#">
                    <i class="icon-wallet"></i> Balance: <strong>₦ <wallet/></strong></a>
            </li>
            <li class="nav-item">
                <a class="nav-link nav-link-danger" href="#">
                    <i class="icon-wallet"></i> Escrow: <strong>₦ <escrow/></strong></a>
            </li>
        </ul>
    </nav>
    <button class="sidebar-minimizer brand-minimizer" type="button"></button>
</div>