<!-- partial:partials/_sidebar.html -->
<nav class="sidebar sidebar-offcanvas sidebar-dark" id="sidebar">
    <ul class="nav">
        <!--<li class="nav-item nav-profile">-->
        <!--    <img src="{{ asset('images/admin.png') }}" alt="profile image">-->
        <!--    <p class="text-center font-weight-medium">Nextpayday Admin</p>-->
        <!--</li>-->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                <i class="menu-icon icon-speedometer"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.loans.get-managed-sweepable') }}">
                <i class="menu-icon icon-credit-card"></i>
                <span class="menu-title">Sweep   <span class="badge badge-danger feature-badge">New </span> </span>
               
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#loans" aria-expanded="false" aria-controls="loans">
                <i class="menu-icon icon-diamond"></i>
                <span class="menu-title">Loans</span>
            </a>
            <div class="collapse" id="loans">
                <ul class="nav flex-column sub-menu">

                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.loans.sweepable')}}">Sweepable Loans</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.loans.auto_sweeping') }}">Temporary Sweep  Loans</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.loans.remita_sweeping') }}">Remita Sweep  Loans</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.loans.eligible')}}">Eligible Top Loans</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.loans.fulfilled')}}">Fulfilled Loans</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.loans.penalized')}}">Penalized Loans</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.loans.pending') }}">Pending Loans</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.loans.active') }}">Active Loans</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.loans.inactive') }}">Cool-off Loans</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.loans.managed') }}">Managed Loans</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.loans.restructured') }}">Restructured Loans</a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.loans.void') }}">Void Loans</a>
                    </li> -->
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.loans.received') }}">Collected Loans</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.loans.given') }}">Loan Funds</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.loans.acquired') }}">Acquired Loans</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.loans.purchase.available') }}">Loans on Transfer</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.investor.loanFund.migrate')}}">Loan Investor Migrate</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#repayments" aria-expanded="false" aria-controls="repayments">
                <i class="menu-icon icon-diamond"></i>
                <span class="menu-title">Repayments</span>
            </a>
            <div class="collapse" id="repayments">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.show.repayments')}}">View Repayments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.approve.repayments')}}">
                            Approve Repayments
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.bulk.repayments')}}">
                            Add Bulk Repayments
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#okra" aria-expanded="false" aria-controls="okra">
                <i class="menu-icon icon-diamond"></i>
                <span class="menu-title">Okra Collections</span>
            </a>
            <div class="collapse" id="okra">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.okra.collection')}}">Start Collections</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.okra.records')}}">
                            Okra Log
                        </a>
                    </li>
                    
                </ul>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#settlements" aria-expanded="false" aria-controls="settlements">
                <i class="menu-icon icon-diamond"></i>
                <span class="menu-title">Settlement</span>
            </a>
            <div class="collapse" id="settlements">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.settlement.pending')}}">Pending Settlement</a>
                    </li>
                   
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.settlement.confirmed')}}">
                            Confirmed Settlement
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.loan-funds.index') }}">
                <i class="menu-icon icon-credit-card"></i>
                <span class="menu-title">Loan Funds</span>
            </a>
        </li>

        


        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#loan-requests" aria-expanded="false" aria-controls="loan-requests">
                <i class="menu-icon icon-docs"></i>
                <span class="menu-title">Loan Requests</span>
            </a>
            <div class="collapse" id="loan-requests">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.loan-requests.pending') }}">Pending</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.loan-requests.pending-setup') }}">Awaiting Setup</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.loan-requests.available') }}">Active</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.loan-requests.employer-declined') }}">Employer Declined</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.loan-requests.index') }}">View All</a>
                    </li>

                </ul>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#collections" aria-expanded="false" aria-controls="collections">
                <i class="menu-icon icon-star"></i>
                <span class="menu-title">Collections</span>
            </a>
            <div class="collapse" id="collections">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.repayments.due_current_month') }}">Collections Made</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.repayments.insight') }}">Collections Insight</a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.repayments.overdue') }}">Overdue</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.repayments.unpaid') }}">Not Due</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.repayments.paid') }}">Liquidated</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.repayments.logs') }}">Logs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.repayments.manage') }}">Manage</a>
                    </li> -->
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#mails" aria-expanded="false" aria-controls="mails">
                <i class="menu-icon icon-envelope"></i>
                <span class="menu-title">Mails</span>
            </a>
            <div class="collapse" id="mails">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.mails.investors') }}">Investors</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.mails.borrowers') }}">Borrowers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.mails.staffs') }}">Staffs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.mails.affiliates') }}">Agent</a>
                    </li>
                    
                </ul>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#birthdays" aria-expanded="false" aria-controls="birthdays">
                <i class="menu-icon icon-diamond"></i>
                <span class="menu-title">Birthdays</span>
                @inject('birthdayService', 'App\Services\BirthdayService')
                @php($birthdays = $birthdayService->birthdaysToday()->count())

                @if ($birthdays > 0)
                    <span class="badge badge-danger">{{$birthdays}}</span>
                @endif
            </a>
            <div class="collapse" id="birthdays">
                <ul class="nav flex-column sub-menu">

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.birthdays.index') }}">Upcoming Birthdays</a>
                    </li>
                  
                   
                    
                </ul>
            </div>
        </li>
        
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#withdrawals" aria-expanded="false" aria-controls="withdrawals">
                <i class="menu-icon icon-credit-card"></i>
                <span class="menu-title">Withdrawal Requests</span>
            </a>
            <div class="collapse" id="withdrawals">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.withdrawal-requests.pending') }}">Pending</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.withdrawal-requests.approved') }}">Approved</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.withdrawal-requests.declined') }}">Declined</a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.transactions.index') }}">
                <i class="menu-icon icon-tag"></i>
                <span class="menu-title">Transaction Logs</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#payments" aria-expanded="false" aria-controls="page-layouts">
                <i class="menu-icon icon-user-following"></i>
                <span class="menu-title">Payments</span>
            </a>
            <div class="collapse" id="payments">

                <ul class="nav flex-column sub-menu">

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.bills.index') }}">Manage Bills</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.bills.stats') }}">Bill Statistics</a>
                    </li>
                   
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.payment.salaries') }}">Pay Salaries</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.payment.controls')}}">Transfer Controls</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.payment.transactions')}}">Transactions</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.paystack.sync.show')}}">Sync Customer</a>
                    </li>

                </ul>
            </div>
        </li>


        

        <li class="nav-item">
            <a class="nav-link" href="#fundWallet" data-toggle="modal">
                <i class="menu-icon icon-wallet"></i>
                <span class="menu-title">Fund Account Wallet</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#staff" aria-expanded="false" aria-controls="page-layouts">
                <i class="menu-icon icon-user-following"></i>
                <span class="menu-title">Staff</span>
            </a>
            <div class="collapse" id="staff">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.staff.invites') }}">Invites</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.staff.index') }}">View All</a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.employers.index') }}">
                <i class="menu-icon icon-briefcase"></i>
                <span class="menu-title">Employers</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.buckets.index') }}">
                <i class="menu-icon icon-social-dropbox"></i>
                <span class="menu-title">Buckets</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#users" aria-expanded="false" aria-controls="users">
                <i class="menu-icon icon-people"></i>
                <span class="menu-title">Users</span>
            </a>
            <div class="collapse" id="users">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.investors.index') }}">Investors</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.users.index') }}">Borrowers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.users.wallet.bal') }}">Wallet Balances</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.users.salary') }}">Salary Information</a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#borrowersGroup" aria-expanded="false" aria-controls="borrowersGroup">
                <i class="menu-icon icon-people"></i>
                <span class="menu-title">Group Borrowers</span>
            </a>
            <div class="collapse" id="borrowersGroup">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.group.create') }}">Create New</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.group.view') }}">View Groups</a>
                    </li>                    
                </ul>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#investors" aria-expanded="false" aria-controls="investors">
                <i class="menu-icon icon-people"></i>
                <span class="menu-title">Investors</span>
            </a>
            <div class="collapse" id="investors">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.investors.active') }}">Active</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.investors.inactive') }}">Inactive</a>
                    </li>
                    
                </ul>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#affiliates" aria-expanded="false" aria-controls="collections">
                <i class="menu-icon icon-link"></i>
                <span class="menu-title">Agent System</span>
            </a>
            <div class="collapse" id="affiliates">
                <ul class="nav flex-column sub-menu">

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.affiliates.index') }}">Users</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.affiliates.settle') }}">Settle Agents</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.affiliates.targets') }}">Set Agent Targets</a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#comms" aria-expanded="false" aria-controls="users">
                <i class="menu-icon icon-envelope-letter"></i>
                <span class="menu-title">Communications</span>
            </a>
            <div class="collapse" id="comms">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.communications.sms') }}">SMS</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.communications.conversations') }}">Conversations</a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#investor-verifications" aria-expanded="false" aria-controls="investor-verifications">
                <i class="menu-icon icon-note"></i>
                <span class="menu-title">Investor Verifications</span>
            </a>
            <div class="collapse" id="investor-verifications">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.investor-verifications.pending') }}">Pending</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.investor-verifications.approved') }}">Approved</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.investor-verifications.declined') }}">Declined</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.investors.unverified') }}">Unverified Investors</a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#certificates" aria-expanded="false" aria-controls="certificates">
                <i class="menu-icon icon-shield"></i>
                <span class="menu-title">Certificates</span>
            </a>
            <div class="collapse" id="certificates">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.certificates.investments.new') }}">Create New</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.certificates.investments.index') }}">Logs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.certificates.investments.archived')}}">Archived</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#promissory-notes" aria-expanded="false" aria-controls="promissory-notes">
                <i class="menu-icon icon-shield"></i>
                <span class="menu-title">Payday Notes</span>
            </a>
            <div class="collapse" id="promissory-notes">
                <ul class="nav flex-column sub-menu">

                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.promissory-notes.create')}}">New</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.promissory-notes.active')}}">Active Notes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.promissory-notes.index')}}">All Notes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.promissory-notes.pending')}}">Pending Notes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.promissory-notes.bank')}}">Investors Bank Details</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.promissory-settings.index')}}">Note Settings</a>
                    </li>
                </ul>
            </div>
        </li>

        


        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#hilcop-certificates" aria-expanded="false" aria-controls="hilcop-certificates">
                <i class="menu-icon icon-shield"></i>
                <span class="menu-title">Hilcop certificates</span>
            </a>
            <div class="collapse" id="hilcop-certificates">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.hilcop-certificates.investments.new') }}">Create New</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.hilcop-certificates.investments.index') }}">Logs</a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#management" aria-expanded="false" aria-controls="management">
                <i class="menu-icon icon-settings"></i>
                <span class="menu-title">Management</span>
            </a>
            <div class="collapse" id="management">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.settings.index') }}">App Settings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.meetings.index') }}">Meetings</a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="nav-item nav-dropdown">
            <a class="nav-link" data-toggle="collapse" href="#refunds" aria-expanded="false" aria-controls="refunds" >
            <i class="menu-icon icon-check"></i>
            <span class="menu-title">Refunds</span>
            </a>
            <div class="collapse" id="refunds">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.refund.pending' )}}">Pending</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.refund.logs' )}}">Logs</a></li>
              </ul>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.logs.index') }}">
                <i class="menu-icon icon-feed"></i>
                <span class="menu-title">App Logs</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.report.view') }}">
                <i class="menu-icon icon-chart"></i>
                <span class="menu-title">Reports</span>
            </a>
        </li>



        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.logout') }}" onclick="event.preventDefault();
                    document.getElementById('logout-form').submit();">
                <i class="menu-icon icon-logout"></i>
                <span class="menu-title">Logout</span>
            </a>
            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                {{ csrf_field() }}
            </form>
        </li>
    </ul>
</nav>
<!-- partial -->