@php
use \App\Sysconfig;
$config_details = Sysconfig::all();
foreach($config_details as $details){
        if($details['param'] == 'company_logo'){
                $logo = $details['value'];
        }
}
@endphp
<!-- Navbar -->
<!--nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top "-->
<nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top text-white">
  <div class="container">
    <div class="navbar-wrapper">
      <!--a class="navbar-brand" href="/">{{ __('Smart Repository') }}</a-->
	<a class="navbar-brand" href="/"><img src="{{ $logo }}"></a>
    </div>
    <button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
    <span class="sr-only">Toggle navigation</span>
    <span class="navbar-toggler-icon icon-bar"></span>
    <span class="navbar-toggler-icon icon-bar"></span>
    <span class="navbar-toggler-icon icon-bar"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end">
      <ul class="navbar-nav">
	<li class="nav-item{{ $activePage == 'collections' ? ' active' : '' }}">
          <a href="/collections" class="nav-link">
            <i class="material-icons">library_books</i> {{ __('Collections') }}
          </a>
        </li>
        <li class="nav-item{{ $activePage == 'features' ? ' active' : '' }}">
          <a href="/features" class="nav-link">
            <i class="material-icons">featured_play_list</i> {{ __('Features') }}
          </a>
        </li>
        <li class="nav-item{{ $activePage == 'faq' ? ' active' : '' }}">
          <a href="/faq" class="nav-link">
            <i class="material-icons">question_answer</i> {{ __('FAQ') }}
          </a>
        </li>
        <li class="nav-item{{ $activePage == 'contact' ? ' active' : '' }}">
          <a href="/contact" class="nav-link">
            <i class="material-icons">contacts</i> {{ __('Contact') }}
          </a>
        </li>

        <li class="nav-item dropdown">
          <a class="nav-link" href="#" id="navbarDropdownProfile" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="material-icons">person</i>
            <p class="d-lg-none d-md-block">
              {{ __('Account') }}
            </p>
          </a>
          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownProfile">
            <a class="dropdown-item" href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
            @if(Auth::user()->hasRole('admin'))
            <a class="dropdown-item" href="/admin/usermanagement">{{ __('User Management') }}</a>
            <a class="dropdown-item" href="/admin/collectionmanagement">{{ __('Collection Management') }}</a>
            <a class="dropdown-item" href="/reports">{{ __('Reports') }}</a>
            @endif
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">{{ __('Log out') }}</a>
          </div>
        </li>
      </ul>
    </div>
  </div>
</nav>
