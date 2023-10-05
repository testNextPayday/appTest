<!-- partial:partials/_sidebar.html -->
<nav class="sidebar sidebar-offcanvas sidebar-dark no-print" id="sidebar" style="margin-left:-15px;">
    <ul class="nav">
      @php
        $investor = auth()->guard('investor')->user();
      @endphp
      <li class="nav-item nav-profile">
        <img src="{{ $investor->avatar }}" alt="profile image">
        <p class="text-center font-weight-medium">{{ $investor->name }}</p>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{ route('investors.dashboard') }}">
          <i class="menu-icon icon-speedometer"></i>
          <span class="menu-title">Dashboard</span>
        </a>
      </li>
      @if($investor->isPremium())
      <li class="nav-item">
        <a class="nav-link" data-toggle="collapse" href="#invest" aria-expanded="false" aria-controls="invest">
          <i class="menu-icon icon-wallet"></i>
          <span class="menu-title">Invest Money</span>
        </a>
        <div class="collapse" id="invest">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item">
              <a class="nav-link" href="{{ route('investors.loan-requests.active') }}">Marketplace</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="{{ route('investors.loan-requests.assigned') }}">Assigned</a>
            </li>
          </ul>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{ route('investors.funds.market') }}">
          <i class="menu-icon icon-layers"></i>
          <span class="menu-title">Buy Loans</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-toggle="collapse" href="#page-layouts" aria-expanded="false" aria-controls="page-layouts">
          <i class="menu-icon icon-briefcase"></i>
          <span class="menu-title">My Loans</span>
        </a>
        <div class="collapse" id="page-layouts">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item">
              <a class="nav-link" href="{{ route('investors.funds') }}">Given</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="{{ route('investors.funds.acquired') }}">Acquired</a>
            </li>
          </ul>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{ route('investors.bids.index') }}">
          <i class="menu-icon icon-chart"></i>
          <span class="menu-title">My Bids</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{ route('investors.collections') }}">
          <i class="menu-icon icon-diamond"></i>
          <span class="menu-title">Collections</span>
        </a>
      </li>


      <li class="nav-item">
        <a class="nav-link" href="{{ route('investors.investment-profile') }}">
          <i class="menu-icon icon-chart"></i>
          <span class="menu-title">Investment Profile</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link" data-toggle="collapse" href="#transaction" aria-expanded="false" aria-controls="tra">
          <i class="menu-icon icon-calculator"></i>
          <span class="menu-title">Transactions</span>
        </a>
        <div class="collapse" id="transaction">
          <ul class="nav flex-column sub-menu">
          
            <li class="nav-item">
              <a class="nav-link" href="{{ route('investors.transactions') }}">Wallet Transactions</a>
            </li>
          </ul>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#" data-toggle="modal" data-target="#fundWallet">
          <i class="menu-icon icon-briefcase"></i>
          <span class="menu-title">Fund Wallet</span>
        </a>
      </li>
       <li class="nav-item">
        <a class="nav-link" href="{{route('investors.certificate.show')}}" >
          <i class="menu-icon icon-shield"></i>
          <span class="menu-title">Certificate</span>
        </a>
      </li> 

      <li class="nav-item">
        <a class="nav-link" href="{{ route('investors.profile') }}">
          <i class="menu-icon icon-user"></i>
          <span class="menu-title">My Profile</span>
        </a>
      </li>
      @endif

      @if($investor->isPromissory())
        <li class="nav-item">
          <a class="nav-link" href="{{ route('investors.promissory-note.fund.account') }}">
            <i class="menu-icon icon-tag"></i>
            <span class="menu-title">Fund Account</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link" data-toggle="collapse" href="#notes" aria-expanded="false" aria-controls="notes">
            <i class="menu-icon icon-wallet"></i>
            <span class="menu-title">My Payday Notes</span>
          </a>
          <div class="collapse" id="notes">
            <ul class="nav flex-column sub-menu">
              <li class="nav-item">
                <a class="nav-link" href="{{ route('investors.promissory-note.index') }}">All</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="{{ route('investors.promissory-note.active') }}">Active</a>
              </li>
            </ul>
          </div>
        </li>
      @endif

      <li class="nav-item">
        <a class="nav-link" href="{{ route('investors.withdrawals') }}">
          <i class="menu-icon icon-tag"></i>
          <span class="menu-title">Withdrawals</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{route('investors.ticket.index')}}" >
          <i class="menu-icon icon-support"></i>
          <span class="menu-title">Support</span>
        </a>
      </li> 
     
      <li class="nav-item">
        <a class="nav-link" href="{{ route('investors.logout') }}">
          <i class="menu-icon icon-logout"></i>
          <span class="menu-title">Logout</span>
        </a>
      </li>
    </ul>
</nav>
<!-- partial -->