@php
$staff = auth('staff')->user();
@endphp

<div class="sidebar">
    <nav class="sidebar-nav">
        <ul class="nav">
            <li class="nav-item">
                <a class="nav-link" href="{{route('staff.dashboard')}}"><i class="icon-speedometer"></i> Dashboard</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="#emiModal" data-toggle="modal"><i class="icon-calculator"></i> EMI Calculator</a>
            </li>

            <li class="divider"></li>
            <li class="nav-title">
                Business
            </li>
            @if($staff->manages('borrowers'))
            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-briefcase"></i> My Loan Requests</a>
                <ul class="nav-dropdown-items">
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('staff.loan-requests.create')}}"><i class="icon-doc"></i> New</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('staff.loan-requests.index')}}"><i class="icon-basket-loaded"></i> View all</a>
                    </li>
                    @if($staff->manages('loan_request_setup'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('staff.loan-requests.pending-setup')}}"> Awaiting Setup</a>
                        </li>
                    @endif
                </ul>
            </li>
            @endif
           
            @if($staff->manages('settlements'))
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#settlements" aria-expanded="false" aria-controls="settlements">
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
           

            @if($staff->manages('investors'))
            <li class="nav-item">
                <a class="nav-link" href="{{route('staff.loan-requests.active')}}"><i class="icon-notebook"></i>Active LRs</a>
            </li>


            <li class="nav-item">
                <a class="nav-link" href="{{route('staff.loan-transfers.index')}}"><i class="icon-energy"></i>Loans on Transfer</a>
            </li>
            @endif

            @if($staff->manages('borrowers'))
            <li class="nav-item">
                <a class="nav-link" href="{{route('staff.loans.index')}}"><i class="icon-arrow-left-circle"></i> Received Loans</a>
            </li>
            @endif

            @if($staff->manages('investors'))
            <li class="nav-item">
                <a class="nav-link" href="{{route('staff.funds.index')}}"><i class="icon-arrow-right-circle"></i> Funded Loans</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{route('staff.funds.acquired')}}"><i class="icon-arrow-down-circle"></i> Acquired Loans</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{route('staff.bids.index')}}"><i class="icon-tag"></i> Bids</a>
            </li>
            @endif

            @if ($staff->manages('promissory_notes'))
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-people"></i> Promissory Notes</a>
                    <ul class="nav-dropdown-items">
                        
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
                </li>
            @endif
            <!--<li class="nav-item">-->
            <!--    <a class="nav-link" href="#"><i class="icon-calculator"></i> Transactions</a>-->
            <!--</li>-->

            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-people"></i> Accounts</a>
                <ul class="nav-dropdown-items">
                    @if($staff->manages('investors'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('staff.accounts.investors') }}"><i class="icon-user"></i> Investor Accounts</a>
                    </li>
                    @endif
                    @if($staff->manages('borrowers'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('staff.accounts.borrowers')}}"><i class="icon-user"></i> Borrower Accounts</a>
                    </li>
                    @endif
                </ul>
            </li>

            <li class="divider"></li>
            <li class="nav-title">
                Me
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{route('staff.profile.index')}}"><i class="icon-user"></i> My Profile</a>
            </li>

        </ul>
    </nav>
    <button class="sidebar-minimizer brand-minimizer" type="button"></button>
</div>