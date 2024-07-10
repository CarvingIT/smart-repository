@php
use \App\Sysconfig;
$config_details = Sysconfig::all();
$sysconfig = array();
foreach($config_details as $details){
	$sysconfig[$details['param']] = $details['value'];
}
$is_demo = env('IS_DEMO');
$app_name = env('APP_NAME');
if(empty($activePage)){
$activePage = 'ISA-RRR';
}
$has_collection_list = env('ENABLE_COLLECTION_LIST');
$collections = \App\Collection::all();
@endphp
<!-- Navbar -->
<!--nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top "-->
<nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top text-white">
  <div class="container">
    <div class="navbar-wrapper">
      <!--a class="navbar-brand" href="/">{{ __('Smart Repository') }}</a-->
	@php
		$logo_link = empty(env('SITE_HOME'))?'/':env('SITE_HOME');
	@endphp
	<a class="navbar-brand" href="{{ $logo_link }}">
	@if(!empty($sysconfig['logo_url']))
	<img class="logo_img" src="/storage/{{ $sysconfig['logo_url'] }}">
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
	@if (env('ENABLE_DASHBOARD_LINK_IN_NAVBAR') == '1')
       <li class="nav-item{{ $activePage == 'dashboard' ? ' active' : '' }}">
          <a href="/dashboard" class="nav-link">
            <i class="material-icons">home</i> {{ __('Dashboard') }}
          </a>
        </li>
	@endif

	@if(env('ENABLE_COLLECTION_LIST') == 1)
        <li class="nav-item{{ $activePage == 'collections' ? ' active' : '' }}">
          <a href="/collections" class="nav-link">
            <i class="material-icons">list</i> {{ __('Collections') }}
          </a>
        </li>
	@else
	@foreach($collections as $c)
        <li class="nav-item">
          <a href="/collection/{{ $c->id }}" class="nav-link">
            <i class="material-icons">list</i>{{ $c->name }}
          </a>
        </li>
	@endforeach
	@endif

	@if(env('ENABLE_BLOG') == 1)
	<li class="nav-item{{ $activePage == 'Blog' ? ' active' : '' }}">
          <a href="/en/blog" class="nav-link">
            <i class="material-icons">rss_feed</i> {{ __('Blog') }}
          </a>
        </li>
	@endif
	@if(env('ENABLE_COMMON_SEARCH') == 1)
        <li class="nav-item{{ $activePage == 'documents' ? ' active' : '' }}">
          <a href="/documents" class="nav-link">
            <i class="material-icons">library_books</i> {{ __('All Documents') }}
          </a>
        </li>
	@endif
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
	@if (env('ENABLE_CONTACT_PAGE', 1) == 1)
        <li class="nav-item{{ $activePage == 'contact' ? ' active' : '' }}">
          <a href="/contact" class="nav-link">
            <i class="material-icons">contacts</i> {{ __('Contact') }}
          </a>
        </li>
	@endif

        <li class="nav-item dropdown">
          <a class="nav-link" title="" href="#" id="navbarDropdownProfile" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			@if (@Gravatar::exists(Auth::user()->email))
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
	  <span class="howdy" style="width:200px;"><a href="/dashboard" style="color:inherit !important;">Welcome @if (empty(Auth::user()->name)) {{ Auth::user()->email }} @else {{ Auth::user()->name }} @endif </a>!</span>
          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownProfile">
            <a class="dropdown-item" href="/profile">{{ __('Profile') }}</a>
            <a class="dropdown-item" href="/dashboard">{{ __('Dashboard') }}</a>
            @if(Auth::user()->hasRole('admin'))
            <a class="dropdown-item" href="/admin/usermanagement">{{ __('Manage Users') }}</a>
            <a class="dropdown-item" href="/admin/collectionmanagement">{{ __('Manage Collections') }}</a>
            <a class="dropdown-item" href="/admin/storagemanagement">{{ __('Manage Storages') }}</a>
	         <a class="dropdown-item" href="/admin/synonymsmanagement">{{ __('Manage Synonyms') }}</a>
           <a class="dropdown-item" href="/admin/taxonomiesmanagement">{{ __('Manage Taxonomies') }}</a>
 	         <a class="dropdown-item" href="/admin/rolesmanagement">{{ __('Manage Roles') }}</a>
 	         <a class="dropdown-item" href="/admin/sysconfig">{{ __('System Configuration') }}</a>
	         <a class="dropdown-item" href="/reports">{{ __('Reports') }}</a>
            @endif
            <div class="dropdown-divider"></div>
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
