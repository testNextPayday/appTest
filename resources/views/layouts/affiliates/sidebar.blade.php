@php($user = auth('affiliate')->user())
<!-- partial:partials/_sidebar.html -->
<nav class="sidebar sidebar-offcanvas sidebar-dark" id="sidebar">
    <ul class="nav">
        <li class="nav-item nav-profile">
            <img src="{{ $user->avatar }}" alt="profile image">
            <p class="text-center font-weight-medium">{{ $user->name }}</p>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('affiliates.dashboard') }}">
                <i class="menu-icon icon-speedometer"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#emiModal" data-toggle="modal">
                <i class="menu-icon icon-calculator"></i>
                <span class="menu-title">EMI Calculator</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#accounts" aria-expanded="false" aria-controls="page-layouts">
                <i class="menu-icon icon-people"></i>
                <span class="menu-title">Accounts</span>
            </a>
            <div class="collapse" id="accounts">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('affiliates.borrowers') }}">Borrowers</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#loansMenu" aria-expanded="false" aria-controls="loansMenu">
                <i class="menu-icon icon-note"></i>
                <span class="menu-title">Loans</span>
            </a>
            <div class="collapse" id="loansMenu">
                <ul class="nav flex-column sub-menu">
                    @if ($user->settings('booking_status') == 'book_loans')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('affiliates.loan-requests.create') }}">Book a new Loan</a>
                    </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('affiliates.loan-requests') }}">My Loan Requests</a>
                    </li>
                     <li class="nav-item">
                        <a class="nav-link" href="{{route('affiliates.loans.eligible')}}">Eligible Top Loans</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('affiliates.loans.fulfilled')}}">Fulfilled Loans</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('affiliates.loans.active') }}">Active Loans</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('affiliates.messages.index') }}">
                <i class="menu-icon icon-envelope-letter"></i>
                <span class="menu-title">Messages</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#withdrawal-requests" aria-expanded="false" aria-controls="page-layouts">
                <i class="menu-icon icon-briefcase"></i>
                <span class="menu-title">Withdrawal Requests</span>
            </a>
            <div class="collapse" id="withdrawal-requests">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="modal" href="#withdrawalRequestModal">New</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('affiliates.withdrawals.index') }}">View all</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
        <a class="nav-link" href="{{ route('affiliates.transactions.wallet') }}">
          <i class="menu-icon icon-calculator"></i>
          <span class="menu-title">Transactions</span>
        </a>
      </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('affiliates.profile.index') }}">
                <i class="menu-icon icon-user"></i>
                <span class="menu-title">My Profile</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{ route('affiliates.commissions.index') }}">
                <i class="menu-icon icon-user"></i>
                <span class="menu-title">Commissions Recieved</span>
            </a>
        </li>
      
    </ul>
</nav>
<!-- partial -->