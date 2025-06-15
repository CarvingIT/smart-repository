@php
use \App\Sysconfig;
$config_details = Sysconfig::all();
$sysconfig = array();
foreach($config_details as $details){
	$sysconfig[$details['param']] = $details['value'];
}
$is_demo = env('IS_DEMO');
$app_name = env('APP_NAME');
$has_collection_list = env('ENABLE_COLLECTION_LIST');
$collections = \App\Collection::all();
@endphp
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top text-white">
  <div class="container">
    <div class="navbar-wrapper">
	@php
		$logo_link = empty(env('SITE_HOME'))?'/':env('SITE_HOME');
	@endphp
      <a class="navbar-brand" href="{{ $logo_link }} ">
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
        <li class="nav-item{{ $activePage == 'home' ? ' active' : '' }}">
          <a href="/" class="nav-link">
            <i class="material-icons">home</i> {{ __('Home') }}
          </a>
        </li>
	@if($has_collection_list == 1)
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
	@if(env('ENABLE_REGISTRATION') == 1)
        <li class="nav-item{{ $activePage == 'register' ? ' active' : '' }}">
          <a href="{{ route('register') }}" class="nav-link">
            <i class="material-icons">person_add</i> {{ __('Register') }}
          </a>
        </li>
	@endif
        <li class="nav-item{{ $activePage == 'login' ? ' active' : '' }}">
		  @if(empty(env('SAML2_LOGIN')))
          <a href="{{ route('login') }}" class="nav-link">
            <i class="material-icons">fingerprint</i> {{ __('Login') }}
          </a>
		  @else
          <a href="{{ env('SAML2_LOGIN') }}" class="nav-link">
            <i class="material-icons">fingerprint</i> {{ __('Login') }}
          </a>
		  @endif
        </li>
      </ul>
    </div>
  </div>
</nav>
<!-- End Navbar -->
