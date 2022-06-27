<header class="main-header">
  <a href="{{ route('home') }}" class="logo">
    <span class="logo-mini"><b>W</b>TL</span>
    <span class="logo-lg"><b>WEB</b>PORTAL</span>
  </a>
  <nav class="navbar navbar-static-top">
    <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
      <span class="sr-only">Toggle navigation</span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </a>

    <div class="navbar-custom-menu">
      <ul class="nav navbar-nav">
        <!-- Tasks: style can be found in dropdown.less -->
        <li class="dropdown tasks-menu">
          <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
            <i class="fa fa-flag-o"></i>
            <span class="label label-danger">{{ count(\Auth::user()->roles) }}</span>
          </a>
          <ul class="dropdown-menu">
            <li class="header">
              Role<small>(s)</small> assigned in your account ({{ count(\Auth::user()->roles) > 0 ? count(\Auth::user()->roles) : '0' }})
            </li>
            <li>
              <ul class="menu">
                @if (count(\Auth::user()->roles) > 0)
                  @forelse (\Auth::user()->roles->sortBy('name') as $role)
                    <li>
                      <a href="javascript:void(0);">{{ $role->name }}</a>
                    </li>
                  @empty
                    <li>
                      <a href="javascript:void(0);">You have no roles set in your account. Please contact the administrator.</a>
                    </li>
                  @endforelse
                @endif
              </ul>
            </li>
          </ul>
        </li>
        <!-- User Account: style can be found in dropdown.less -->
        <li class="dropdown user user-menu">
          <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
            <img src="{{ asset('images/addessa_logo.png') }}" class="user-image" alt="User Image">
            <span class="hidden-xs">{{ \Auth::user()->first_name }} {{ \Auth::user()->last_name }}</span>
          </a>
          <ul class="dropdown-menu">
            <!-- User image -->
            <li class="user-header">
              <img src="{{ asset('images/addessa_logo.png') }}" class="img-circle" alt="User Image">
              <p>
                <strong>
                  {{ \Auth::user()->first_name }}
                  {{ \Auth::user()->last_name }}
                </strong>
              </p>
              <p>
                <small>{{ \Auth::user()->branch ? \Auth::user()->branch->name : 'Not Assigned' }}</small>
              </p>
            </li>
            <!-- Menu Footer-->
            <li class="user-footer">
              <div class="pull-left">
                <a href="{{ route('user.profile') }}" class="btn btn-default btn-flat">Profile</a>
              </div>
              <div class="pull-right">
                <a onclick="event.preventDefault();
                   document.getElementById('logout-form').submit();" href="javascript:void(0);"
                   class="btn btn-default btn-flat">Sign out</a>
                <form id="logout-form" action="{{ url('/logout') }}" method="post">
                  {{ csrf_field() }}
                </form>
              </div>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </nav>
</header>