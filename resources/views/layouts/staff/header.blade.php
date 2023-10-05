<header class="app-header navbar">
    <button class="navbar-toggler mobile-sidebar-toggler d-lg-none mr-auto" type="button">
      <span class="navbar-toggler-icon"></span>
    </button>
    <a class="navbar-brand" href="#"></a>
    <button class="navbar-toggler sidebar-toggler d-md-down-none" type="button">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <ul class="nav navbar-nav ml-auto">
      <!--<li class="nav-item d-md-down-none">-->
      <!--  <a class="nav-link" href="#"><i class="icon-bell"></i><span class="badge badge-pill badge-danger">5</span></a>-->
      <!--</li>-->
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
          <img src="{{Auth::guard('staff')->user()->avatar}}" class="img-avatar" alt="Display Image">
        </a>
        <div class="dropdown-menu dropdown-menu-right">
          <div class="dropdown-header text-center">
            <strong>Menu</strong>
          </div>
          <a class="dropdown-item" href="{{route('staff.logout')}}">
              <i class="fa fa-lock"></i> Logout
          </a>
        </div>
      </li>
    </ul>
  </header>