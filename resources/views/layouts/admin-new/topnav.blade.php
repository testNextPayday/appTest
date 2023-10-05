<!-- partial:partials/_navbar.html -->
<nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex flex-row">

    <div class="text-center navbar-brand-wrapper d-flex align-items-top justify-content-center">
        <a class="navbar-brand brand-logo" href="{{route('staff.dashboard')}}">
            <img src="{{asset('logo_pack/logo/colored/512.png')}}" alt="logo"
                style="margin-top:5px;margin-bottom:0px;height:50px;" />
        </a>
        <a class="navbar-brand brand-logo-mini" href="{{route('staff.dashboard')}}">
            <img src="{{asset('unicredit-image-suite/icons/32/icons-12.png')}}" alt="logo" />
        </a>
    </div>
    <div class="navbar-menu-wrapper d-flex align-items-center">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
            <span class="mdi mdi-menu"></span>
        </button>


        <ul class="navbar-nav navbar-nav-right" style="margin-left:10%;width:100%;">
            @if(app()->environment() == 'staging')
                <li style="color:red;"><h4>Staging</h4></li>
            @endif
            <li class="nav-item d-xl-inline-block" style="width:80%">
              <search-borrowers></search-borrowers>
            </li>
            <li class="nav-item dropdown d-none d-xl-inline-block user-dropdown">
                <a class="nav-link dropdown-toggle" id="UserDropdown" href="#" data-toggle="dropdown"
                    aria-expanded="false">
                    <div class="dropdown-toggle-wrapper">
                        <div class="inner">
                            <img class="img-xs rounded-circle" src="{{asset('images/admin.png')}}" alt="Profile image">
                        </div>
                        <div class="inner">
                            <div class="inner">
                                <span class="profile-text font-weight-bold">Nextpayday Admin</span>
                                <small class="profile-text small">Admin</small>
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
                    <a class="dropdown-item mt-2" href="{{ route('admin.employers.index') }}">
                        Employers
                    </a>
                    <a class="dropdown-item mt-2" href="{{ route('admin.settings.index') }}">
                        Settings
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                        Sign Out
                    </a>
                </div>
            </li>

        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
            data-toggle="offcanvas">
            <span class="mdi mdi-menu"></span>
        </button>
    </div>
</nav>
<!-- partial -->