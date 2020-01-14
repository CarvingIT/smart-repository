<div class="sidebar" data-color="orange" data-background-color="white" data-image="{{ asset('material') }}/img/sidebar-1.jpg">
  <!--
      Tip 1: You can change the color of the sidebar using: data-color="purple | azure | green | orange | danger"

      Tip 2: you can also add an image using data-image tag
  -->
  <div class="logo">
    <!--a href="{{ route('home') }}" class="simple-text logo-normal"-->
    <a href="/" class="simple-text logo-normal">
      <!--img src="/i/smart-repo-logo.png"-->
      {{ __('Smart Repository') }}
    </a>
  </div>
  <div class="sidebar-wrapper">
    <ul class="nav">
      <!--li class="nav-item{{ $activePage == 'dashboard' ? ' active' : '' }}">
        <a class="nav-link" href="/admin">
          <i class="material-icons">dashboard</i>
            <p>{{ __('Admin Dashboard') }}</p>
        </a>
      </li-->
      <li class="nav-item{{ $activePage == 'table' ? ' active' : '' }}">
        <a class="nav-link" href="/collections">
          <i class="material-icons">library_books</i>
            <p>{{ __('Collections') }}</p>
        </a>
      </li>
@if(Auth::check() && Auth::user()->hasRole('admin'))
      <li class="nav-item {{ ($activePage == 'profile' || $activePage == 'user-management') ? ' active' : '' }}">
        <a class="nav-link" data-toggle="collapse" href="#admindashboard" aria-expanded="true">
          <i class="material-icons">dashboard</i>
          <p>{{ __('Admin Dashboard') }}
            <b class="caret"></b>
          </p>
        </a>
        <div class="collapse hide" id="admindashboard">
          <ul class="nav">
            <li class="nav-item{{ $activePage == 'profile' ? ' active' : '' }}">
              <a class="nav-link" href="{{ route('profile.edit') }}">
          	<i class="material-icons">account_box</i>
                <!--span class="sidebar-mini"> UP </span-->
                <span class="sidebar-normal">{{ __('User profile') }} </span>
              </a>
            </li>
            <li class="nav-item{{ $activePage == 'user-management' ? ' active' : '' }}">
              <a class="nav-link" href="{{ route('user.index') }}">
                <!--span class="sidebar-mini"> UM </span-->
          	<i class="material-icons">view_list</i>
                <span class="sidebar-normal"> {{ __('User Management') }} </span>
              </a>
            </li>
            <li class="nav-item{{ $activePage == 'typography' ? ' active' : '' }}">
              <a class="nav-link" href="/admin/collectionmanagement">
                <!--span class="sidebar-mini"> CM </span-->
          	<i class="material-icons">view_list</i>
                <span class="sidebar-normal"> {{ __('Collection Management') }}</span>
              </a>
            </li>
          </ul>
        </div>
      </li>
      <li class="nav-item {{ ($activePage == 'reports' || $activePage == 'user-management') ? ' active' : '' }}">
        <a class="nav-link" data-toggle="collapse" href="#reports" aria-expanded="true">
          <!--i><img style="width:25px" src="{{ asset('material') }}/img/laravel.svg"></i-->
          <i class="material-icons">show_chart</i>
          <p>{{ __('Reports') }}
            <b class="caret"></b>
          </p>
        </a>
        <div class="collapse hide" id="reports">
          <ul class="nav">
            <li class="nav-item{{ $activePage == 'uploads' ? ' active' : '' }}">
              <a class="nav-link" href="/reports/uploads">
          	<i class="material-icons">show_chart</i>
                <span class="sidebar-normal">{{ __('Uploads Reports') }} </span>
              </a>
            </li>
            <li class="nav-item{{ $activePage == '/reports/downloads' ? ' active' : '' }}">
              <a class="nav-link" href="/reports/downloads">
          	<i class="material-icons">show_chart</i>
                <span class="sidebar-normal"> {{ __('Downloads Reports') }} </span>
              </a>
            </li>
          </ul>
        </div>
      </li>
      <li class="nav-item{{ $activePage == 'logout' ? ' active' : '' }}">
        <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
          <i class="material-icons">library_books</i>
            <p>{{ __('Log out') }}</p>
        </a>
      </li>
@endif
    </ul>
  </div>
</div>
