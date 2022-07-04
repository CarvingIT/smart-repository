@php
use \App\Sysconfig;
$config_details = Sysconfig::all();
$sysconfig = array();
foreach($config_details as $details){
	$sysconfig[$details['param']] = $details['value'];
}
$is_demo = env('IS_DEMO');
$app_name = env('APP_NAME');
@endphp
<!-- Navbar -->
<!--nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top "-->
<nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top text-white">
  <div class="container">
    <div class="navbar-wrapper">
      <!--a class="navbar-brand" href="/">{{ __('Smart Repository') }}</a-->
	<a class="navbar-brand" href="/">
	@if(!empty($sysconfig['logo_url']))
	<img class="logo_img" src="{{ $sysconfig['logo_url'] }}">
	@else
	<img class="logo_img" src="/i/your-logo.png" />
	@endif
	</a>
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
            <i class="material-icons">list</i> {{ __('Collections') }}
          </a>
        </li>
        <li class="nav-item{{ $activePage == 'documents' ? ' active' : '' }}">
          <a href="/documents" class="nav-link">
            <i class="material-icons">library_books</i> {{ __('All Documents') }}
          </a>
        </li>
	@if(isset($is_demo) && $is_demo == 1)
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
	@endif
        <li class="nav-item{{ $activePage == 'contact' ? ' active' : '' }}">
          <a href="/contact" class="nav-link">
            <i class="material-icons">contacts</i> {{ __('Contact') }}
          </a>
        </li>

        <li class="nav-item dropdown">
          <a class="nav-link" title="{{ 'Welcome '.Auth::user()->name }}" href="#" id="navbarDropdownProfile" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			@if (Gravatar::exists(Auth::user()->email))
			<img src="{{ Gravatar::get(Auth::user()->email) }}" class="icon" />
			@else
            <i class="material-icons">person</i>
			@endif
			<!--
            <p class="d-lg-none d-md-block">
              {{ __('Account') }}
            </p>
			-->
          </a>
          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownProfile">
            <a class="dropdown-item" href="/profile">{{ __('Profile') }}</a>
            @if(Auth::user()->hasRole('admin'))
            <a class="dropdown-item" href="/admin/usermanagement">{{ __('Manage Users') }}</a>
            <a class="dropdown-item" href="/admin/collectionmanagement">{{ __('Manage Collections') }}</a>
            <a class="dropdown-item" href="/admin/storagemanagement">{{ __('Manage Storages') }}</a>
            <a class="dropdown-item" href="/admin/sysconfig">{{ __('System Configuration') }}</a>
            <a class="dropdown-item" href="/reports">{{ __('Reports') }}</a>
            @endif
	    <!--
            <div class="dropdown-divider"></div>
	   -->
			@if(empty(env('SAML2_SLS')))
            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">{{ __('Log out') }}</a>
			@else
            <a class="dropdown-item" href="{{ env('SAML2_SLS') }}">{{ __('Log out') }}</a>
			@endif
          </div>
        </li>
      </ul>
    </div>
  </div>
</nav>
