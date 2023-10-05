<!-- partial:partials/_navbar.html -->
@php
  $staff = auth()->guard('staff')->user();
@endphp
<nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
  <div class="text-center navbar-brand-wrapper d-flex align-items-top justify-content-center">
    <a class="navbar-brand brand-logo" href="{{route('staff.dashboard')}}">
      <img src="{{asset('logo_pack/logo/colored/128.png')}}" alt="logo" />
    </a>
    <a class="navbar-brand brand-logo-mini" href="{{route('staff.dashboard')}}">
      <img src="{{asset('logo_pack/icons/colored/icon_colored_32.png')}}" alt="logo" />
    </a>
  </div>
  <div class="navbar-menu-wrapper d-flex align-items-center">
    <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
      <span class="mdi mdi-menu"></span>
    </button>
    <ul class="navbar-nav navbar-nav-right">
      <li class="nav-item dropdown d-none d-xl-inline-block user-dropdown">
        <a class="nav-link dropdown-toggle" id="UserDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
          <div class="dropdown-toggle-wrapper">
            <div class="inner">
              <img class="img-xs rounded-circle" src="{{$staff->avatar}}" alt="Profile image">
            </div>
            <div class="inner">
              <div class="inner">
                <span class="profile-text font-weight-bold">{{$staff->name}}</span>
                <small class="profile-text small">Staff</small>
              </div>
              <div class="inner">
                <div class="icon-wrapper">
                  <i class="mdi mdi-chevron-down"></i>
                </div>
              </div>
            </div>
          </div>
        </a>
        <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
          <a class="dropdown-item mt-2" href="{{ route('staff.profile.index') }}">
            My Profile
          </a>
          <a class="dropdown-item" href="{{ route('staff.logout') }}">
            Sign Out
          </a>
        </div>
      </li>
    </ul>
    <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
      <span class="mdi mdi-menu"></span>
    </button>
  </div>
</nav>
<!-- partial -->