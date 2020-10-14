@php
use \App\Sysconfig;
$config_details = Sysconfig::all();
$sysconfig = array();
foreach($config_details as $details){
	$sysconfig[$details['param']] = $details['value'];
}
$is_demo = env('IS_DEMO');
@endphp
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top text-white">
  <div class="container">
    <div class="navbar-wrapper">
      <a class="navbar-brand" href="/">
	@if(!empty($sysconfig['logo_url']))
	<img class="logo_img" src="{{ $sysconfig['logo_url'] }}">
	@else
	{{$title}}
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
            <i class="material-icons">library_books</i> {{ __('Collections') }}
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
        <li class="nav-item{{ $activePage == 'register' ? ' active' : '' }}">
          <a href="{{ route('register') }}" class="nav-link">
            <i class="material-icons">person_add</i> {{ __('Register') }}
          </a>
        </li>
        <li class="nav-item{{ $activePage == 'login' ? ' active' : '' }}">
          <a href="{{ route('login') }}" class="nav-link">
            <i class="material-icons">fingerprint</i> {{ __('Login') }}
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
<!-- End Navbar -->
