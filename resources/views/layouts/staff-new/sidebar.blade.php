@php
    $staff = auth('staff')->user();
@endphp
<!-- partial:partials/_sidebar.html -->
<nav class="sidebar sidebar-offcanvas sidebar-dark" id="sidebar">
    <ul class="nav">
        <li class="nav-item nav-profile">
            <img src="{{ $staff->avatar }}" alt="profile image">
            <p class="text-center font-weight-medium">{{ $staff->name }}</p>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('staff.dashboard') }}">
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

        @if ($staff->manages('followup_users'))
            <li class="nav-item">
                <a class="nav-link" href="{{ route('staff.followup.users') }}">
                    <i class="menu-icon icon-user"></i>
                    <span class="menu-title">New User List</span>
                </a>
            </li>
        @endif

        @if ($staff->manages('followup_investors'))
            <li class="nav-item">
                <a class="nav-link" href="{{ route('staff.followup.investors') }}">
                    <i class="menu-icon icon-user"></i>
                    <span class="menu-title">New Investor List</span>
                </a>
            </li>
        @endif


        @if ($staff->manages('support'))
        @inject('groupNotification', 'App\Models\GroupNotification')
        @php($supports = $groupNotification->unattendTickets()->get()->count())
            <li class="nav-item">
                <a class="nav-link" href="{{ route('staff.ticket.active') }}">
                    <i class="menu-icon icon-support"></i>
                    <span class="menu-title">Support</span>
                    @if ($supports > 0)
                        <span class="badge badge-danger">{{$supports}}</span>
                    @endif
                </a>
            </li>
        @endif
       <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#loans" aria-expanded="false" aria-controls="loans">
                <i class="menu-icon icon-diamond"></i>
                <span class="menu-title">Loans</span>
            </a>
            <div class="collapse" id="loans">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('staff.loans.eligible')}}">Eligible Top Loans</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('staff.loans.fulfilled')}}">Fulfilled Loans</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('staff.loans.active') }}">Active Loans</a>
                    </li>

                    @if($staff->manages('loan_disbursement'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('staff.loans.pending') }}">Pending Loans</a>
                    </li>
                    @endif
                   

                </ul>
            </div>
        </li>
        @if($staff->manages('borrowers'))
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#page-layouts" aria-expanded="false" aria-controls="page-layouts">
                <i class="menu-icon icon-briefcase"></i>
                <span class="menu-title">Loan Requests</span>
            </a>
            <div class="collapse" id="page-layouts">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('staff.loan-requests.create') }}">New</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('staff.loan-requests.index') }}">View all</a>
                    </li>
                    @if($staff->manages('loan_request_setup'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('staff.loan-requests.pending-setup')}}">Awaiting Setup</a>
                        </li>
                    @endif
                    @if($staff->manages('loan_request'))
                    <li class="nav-item">
                            <a class="nav-link" href="{{route('staff.loan-requests.pending')}}">Pending</a>
                    </li>
                        
                    @endif
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('staff.loans.index') }}">
                <i class="menu-icon icon-arrow-left-circle"></i>
                <span class="menu-title">Received Loans</span>
            </a>
        </li>

        <li class="nav-item nav-dropdown">
            <a class="nav-link" data-toggle="collapse" href="#refunds" aria-expanded="false" aria-controls="refunds" >
                <i class="menu-icon icon-check"></i>
                <span class="menu-title">Refunds</span>
            </a>
            
            <div class="collapse" id="refunds">
              <ul class="nav flex-column sub-menu">
                @if($staff->manages('approve_refunds'))
                    <li class="nav-item"><a class="nav-link" href="{{ route('staff.refund.pending' )}}">Pending Refunds</a></li>
                @endif
                <li class="nav-item"><a class="nav-link" href="{{ route('staff.refund.logs' )}}">Logs</a></li>
              </ul>
            </div>
        </li>
        @endif
        
        @if($staff->manages('investors'))
        <li class="nav-item">
            <a class="nav-link" href="{{ route('staff.loan-requests.active') }}">
                <i class="menu-icon icon-list"></i>
                <span class="menu-title">Active LRs</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link" href="{{ route('staff.loan-transfers.index') }}">
                <i class="menu-icon icon-energy"></i>
                <span class="menu-title">Loans on Transfer</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link" href="{{ route('staff.funds.index') }}">
                <i class="menu-icon icon-arrow-right-circle"></i>
                <span class="menu-title">Funded Loans</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link" href="{{ route('staff.funds.acquired') }}">
                <i class="menu-icon icon-basket-loaded"></i>
                <span class="menu-title">Acquired Loans</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link" href="{{ route('staff.bids.index') }}">
                <i class="menu-icon icon-tag"></i>
                <span class="menu-title">Bids</span>
            </a>
        </li>
        @endif


        @if ($staff->manages('withdrawal_approval'))
            <li class="nav-item nav-dropdown">
                <a class="nav-link" data-toggle="collapse" href="#withdrawals" aria-expanded="false" aria-controls="withdrawals" >
                <i class="menu-icon icon-briefcase"></i>
                    <span class="menu-title">Withdrawals</span>
                </a>
                <div class="collapse" id="withdrawals">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('staff.withdrawals.pending')}}">Pending</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('staff.withdrawals.approved')}}">Approved</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('staff.withdrawals.declined')}}">Declined</a>
                            </li>
                        </ul>
                </div>
            </li>
        @endif

        

        @if ($staff->manages('promissory_notes'))
            <li class="nav-item nav-dropdown">
                <a class="nav-link" data-toggle="collapse" href="#paydaynotes" aria-expanded="false" aria-controls="paydaynotes" >
                <i class="menu-icon icon-briefcase"></i>
                    <span class="menu-title">Payday Notes</span>
                </a>
                <div class="collapse" id="paydaynotes">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('staff.promissory-notes.create')}}">New</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('staff.promissory-notes.index')}}">Active Notes</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('staff.promissory-notes.bank')}}">Investors Bank Details</a>
                            </li>
                        </ul>
                </div>
            </li>
        @endif

        @if ($staff->manages('borrowers_group'))
            <li class="nav-item nav-dropdown">
                <a class="nav-link" data-toggle="collapse" href="#borrowersgroup" aria-expanded="false" aria-controls="borrowersgroup" >
                <i class="menu-icon icon-briefcase"></i>
                    <span class="menu-title">Borrowers Group</span>
                </a>
                <div class="collapse" id="borrowersgroup">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('staff.group.create')}}">Create New</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('staff.group.view')}}">View Groups</a>
                            </li>                           
                        </ul>
                </div>
            </li>
        @endif

        @if($staff->manages('loan_restructuring')) 
            <li class="nav-item">
                <a class="nav-link" href="{{ route('staff.loan-managed.index') }}">
                    <i class="menu-icon icon-energy"></i>
                    <span class="menu-title">Managed Loans</span>
                </a>
            </li>
        @endif

        @if($staff->manages('salary_payment')) 
            <li class="nav-item">
                <a class="nav-link" href="{{ route('staff.salary-payment') }}">
                    <i class="menu-icon icon-energy"></i>
                    <span class="menu-title">Salary Management</span>
                </a>
            </li>
        @endif

        @if($staff->manages('repayments'))
        <li class="nav-item">
            <a class="nav-link"  data-toggle="collapse" href="#repayments" aria-expanded="false" aria-controls="repayments">
                <i class="menu-icon icon-diamond"></i>
                <span class="menu-title">Repayments</span>
            </a>
             <div class="collapse" id="repayments">
                     <ul class="nav flex-column sub-menu">
                          <li class="nav-item">
                            <a class="nav-link" href="{{route('staff.show.repayments')}}">View Repayments</a>
                         </li>
                         @if($staff->manages('approve_repayment'))
                         <li class="nav-item">
                            <a class="nav-link" href="{{route('staff.approve.repayments')}}">Approve Repayments</a>
                         </li>
                         @endif
                         <li class="nav-item">
                            <a class="nav-link" href="{{route('staff.pending.repayments')}}">Pending Repayments</a>
                         </li>
                         <li class="nav-item">
                             <a class="nav-link" href="{{route('staff.bulk.repayments')}}">
                                 Add Bulk Repayments
                             </a>
                         </li>
                     </ul>
              </div>
        </li>
       @if($staff->manages('settlements'))
        <li class="nav-item">
            <a class="nav-link"  data-toggle="collapse" href="#settlements" aria-expanded="false" aria-controls="settlements">
                <i class="menu-icon icon-diamond"></i>
                <span class="menu-title">Settlement</span>
            </a>
             <div class="collapse" id="settlements">
                     <ul class="nav flex-column sub-menu">
                          <li class="nav-item">
                            <a class="nav-link" href="{{route('staff.show.settlements')}}">Settlement</a>
                         </li>
                         <li class="nav-item">
                             <a class="nav-link" href="{{route('staff.add.settlements')}}">
                                 Add Settlement
                             </a>
                         </li>
                     </ul>
              </div>
        </li>
        @endif
        
        @endif
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#sidebar-accounts" aria-expanded="false" aria-controls="page-layouts">
                <i class="menu-icon icon-people"></i>
                <span class="menu-title">Accounts</span>
            </a>
            <div class="collapse" id="sidebar-accounts">
                <ul class="nav flex-column sub-menu">
                    @if($staff->manages('investors'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('staff.accounts.investors') }}">Investors</a>
                    </li>
                    @endif
                    
                    @if($staff->manages('borrowers'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('staff.accounts.borrowers') }}">Borrowers</a>
                    </li>
                    @endif
                </ul>
            </div>
        </li>

        @if($staff->manages('bills') || $staff->manages('approve_bills'))
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#staff-bill" aria-expanded="false" aria-controls="page-layouts">
                        <i class="menu-icon icon-user"></i>
                        <span class="menu-title">Manage Bills</span>
                </a>
                <div class="collapse" id="staff-bill">
                    <ul class="nav flex-column sub-menu">
                        @if($staff->manages('bills'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('staff.bills.manage') }}">Manage Bills</a>
                        </li>
                        @endif
                        
                        @if($staff->manages('approve_bills'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('staff.bills.pending') }}">Pending Bills</a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
        @endif
        
        @if($staff->manages('view_report'))
        <li class="nav-item">
            <a class="nav-link" href="{{ route('staff.report.view') }}">
                <i class="menu-icon icon-chart"></i>
                <span class="menu-title">Reports</span>
            </a>
        </li>
        @endif
        
        <li class="nav-item">
            <a class="nav-link" href="{{ route('staff.profile.index') }}">
                <i class="menu-icon icon-user"></i>
                <span class="menu-title">My Profile</span>
            </a>
        </li>
      
    </ul>
</nav>
<!-- partial -->