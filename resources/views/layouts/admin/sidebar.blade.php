<div class="sidebar">
    <nav class="sidebar-nav">
        <ul class="nav">
            <li class="nav-item">
                <a class="nav-link" href="{{route('admin.dashboard')}}"><i class="icon-speedometer"></i> Dashboard </a>
            </li>

            <li class="nav-title">
                Financials
            </li>
            
            
            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-layers"></i> Loans</a>
                <ul class="nav-dropdown-items">
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.loans.active')}}"><i class="icon-energy"></i>Eligible Top Loans</a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.loans.active')}}"><i class="icon-energy"></i>Active Loans</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.loans.managed')}}"><i class="icon-energy"></i>Managed Loans</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.loans.received')}}"><i class="icon-energy"></i>Collected Loans</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.loans.given')}}"><i class="icon-energy"></i> Loans Funds</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.loans.acquired')}}"><i class="icon-energy"></i> Acquired Funds</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.loans.purchase.available')}}"><i class="icon-energy"></i> Loans on Transfer</a>
                    </li>
                </ul>
            </li>
            
            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-diamond"></i> Loan Requests</a>
                <ul class="nav-dropdown-items">

                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.loan-requests.pending') }}"><i class="icon-star"></i> Pending Requests</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.loan-requests.pending-setup') }}"><i class="icon-star"></i> Awaiting Setup</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.loan-requests.available') }}"><i class="icon-star"></i> Active Requests</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.loan-requests.employer-declined') }}"><i class="icon-star"></i> Employer Declined</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.loan-requests.index')}}"><i class="icon-star"></i> View All</a>
                    </li>
                </ul>
            </li>
            
            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-docs"></i> Collections</a>
                <ul class="nav-dropdown-items">
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.repayments.due_current_month')}}"><i class="icon-calendar"></i> Due This Month</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.repayments.overdue')}}"><i class="icon-dislike"></i> Overdue</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.repayments.unpaid')}}"><i class="icon-graph"></i> Not Due</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.repayments.paid')}}"><i class="icon-like"></i> Liquidated</a>
                    </li>
                </ul>
            </li>
            
           
            
            <li class="nav-title">
                Transactions
            </li>
            
            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-note"></i> Withdrawals</a>
                <ul class="nav-dropdown-items">
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.withdrawal-requests.pending')}}"><i class="icon-action-undo"></i> Pending</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.withdrawal-requests.approved')}}"><i class="icon-action-redo"></i> Approved</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.withdrawal-requests.declined')}}"><i class="icon-cursor-move"></i> Declined</a>
                    </li>
                </ul>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{route('admin.transactions.index')}}"><i class="icon-calculator"></i>Transaction Logs</a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="javascript:;" data-target="#fundWallet" data-toggle="modal"><i class="icon-calculator"></i> Fund Account Wallet</a>
            </li>
            
            
            <li class="divider"></li>
            <li class="nav-title">
                Personnel
            </li>
            
            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-people"></i> Staff</a>
                <ul class="nav-dropdown-items">
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.staff.invites') }}"><i class="icon-user"></i> Invites</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.staff.index') }}"><i class="icon-user"></i> All Staff</a>
                    </li>
                </ul>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="{{route('admin.employers.index')}}"><i class="icon-people"></i> Employers</a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="{{route('admin.buckets.index')}}"><i class="icon-social-dropbox"></i> Buckets</a>
            </li>
            
            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-people"></i> Users</a>
                <ul class="nav-dropdown-items">
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.investors.index') }}"><i class="icon-user"></i> Investors</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.users.index')}}"><i class="icon-user"></i> Borrowers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.users.salary')}}"><i class="icon-user"></i> Salary Information</a>
                    </li>
                </ul>
            </li>

            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-people"></i> Investors</a>
                <ul class="nav-dropdown-items">
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.investors.active') }}"><i class="icon-user"></i>Active</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.investors.inactive')}}"><i class="icon-user"></i>Inactive</a>
                    </li>
                    
                </ul>
            </li>
            
            <li class="nav-title">
                Verifications
            </li>
            
            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-note"></i> Investor Verifications</a>
                <ul class="nav-dropdown-items">
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.investor-verifications.pending')}}"><i class="icon-action-undo"></i> Pending</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.investor-verifications.approved')}}"><i class="icon-action-redo"></i> Approved</a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.investor-verifications.declined')}}"><i class="icon-action-redo"></i> Declined</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.investors.unverified') }}"><i class="icon-user-unfollow"></i> Unverified Investors</a>
                    </li>
                </ul>
            </li>
            
            <li class="nav-title">
                Certificates
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="{{route('admin.certificates.investments.new')}}"><i class="icon-shield"></i> New</a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="{{route('admin.certificates.investments.index')}}"><i class="icon-shield"></i> Logs</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{route('admin.certificates.investments.archived')}}"><i class="icon-shield"></i> Archived</a>
            </li>

            <li class="nav-title">
                Hilcop Certificates
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="{{route('admin.hilcop-certificates.investments.new')}}"><i class="icon-shield"></i> New</a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="{{route('admin.hilcop-certificates.investments.index')}}"><i class="icon-shield"></i> Logs</a>
            </li>


            

            
            <li class="nav-title">
                Settings
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="{{route('admin.settings.index')}}"><i class="icon-shield"></i> App Defaults</a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="{{route('admin.logs.index')}}"><i class="icon-feed"></i> App Logs</a>
            </li>
            <!-- <li class="nav-item">
                <a class="nav-link" href="{{route('admin.report.view')}}"><i class="icon-chart"></i> Reports</a>
            </li> -->
            
            <li class="divider"></li>
            
            <li class="nav-item">
                <a class="nav-link" href="{{route('admin.logout')}}"
                    onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                    <i class="icon-logout"></i> Logout</a>
                
                <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
            </li>
        </ul>
    </nav>
    <button class="sidebar-minimizer brand-minimizer" type="button"></button>
</div>