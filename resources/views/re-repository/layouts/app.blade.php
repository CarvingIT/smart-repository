<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>{{ env('APP_NAME') }}</title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <!-- Favicons -->
  <link rel="apple-touch-icon" sizes="180x180" href="/css/classic/img/favicon/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="/css/classic/img/favicon/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/css/classic/img/favicon/favicon-16x16.png">
  <link rel="manifest" href="/css/classic/img/favicon/site.webmanifest">
  <meta name="msapplication-TileColor" content="#da532c">
  <meta name="theme-color" content="#ffffff">
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,600;1,700&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
  <!-- Vendor CSS Files -->
  <link href="/css/classic/material/css/material-dashboard.css" rel="stylesheet">
  <link href="/css/classic/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="/css/classic/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="/css/classic/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="/css/classic/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link href="/css/classic/vendor/aos/aos.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link rel="stylesheet" href="{{url('/css/classic/main.css')}}" />

  <!-- Fonts -->
  <link href="/css/classic/fonts.css" rel="stylesheet">
  <link href="/css/jquery.dataTables.min.css" rel="stylesheet" />
  <link href="{{ asset('material') }}/css/bootstrap-select.min.css" rel="stylesheet" />
  <script src="/js/jquery-3.5.1.js"></script>
  <!--script src="https://code.jquery.com/jquery-3.7.0.min.js" crossorigin="anonymous"></script>-->
  <script src="/js/classic/main.js"></script>

  @stack('js') 
  <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('material') }}/img/apple-icon.png">
	@if(!empty($settings['favicon_url']))
  <link rel="icon" type="image/png" href="/storage/{{ $settings['favicon_url']}}">
	@else
  <link rel="icon" type="image/png" href="{{ asset('material') }}/img/favicon.png">
	@endif
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
  
  <!-- CSS Files -->
  <link href="/css/custom.css" rel="stylesheet" />
  <link href="{{ asset('material') }}/css/bootstrap-select.min.css" rel="stylesheet" />
    
    <style>
        .text-light {

        color: #f05a22 !important;
        }
        .text-light strong:hover {
        
        color:#fff200 !important;
        }
    </style>
@if(!empty(env('OPENAI_API_KEY')))
<script>
	setTimeout(function(){
		window.botmanChatWidget.whisper('q');
		window.botmanChatWidget.close();
	}, 1000);
</script>
@endif
    <!-- overriding css -->
    <style>
@php
    $conf = \App\Sysconfig::all();
    $settings = array();
    foreach($conf as $c){
        $settings[$c->param] = $c->value;
    }
@endphp
                    @if(!empty($settings['overridingcss']))
                    {!! $settings['overridingcss'] !!}
                    @endif
    </style>

</head>
<body>
@php
	$sys_config = App\Sysconfig::all();
	$settings = [];
	foreach($sys_config as $sc){
		$settings[$sc->param] = $sc->value;
	}
@endphp
  <!-- ======= Header ======= -->
<div class="top-bar">
    <div class="container-fluid container-xl d-flex justify-content-end align-items-center">
      <div class="social-media-header">
		@if (!empty($settings['facebook_link']))
        <a href="{{ $settings['facebook_link'] }}" class="social-icon" target="_blank"><i class="fa-brands fa-facebook-f"></i></a>
		@endif
		@if (!empty($settings['twitter_link']))
        <a href="{{ $settings['twitter_link'] }}" class="social-icon" target="_blank"><i class="fa-brands fa-twitter"></i></a>
		@endif
		@if (!empty($settings['youtube_link']))
        <a href="{{ $settings['youtube_link'] }}" class="social-icon" target="_blank"><i class="fa-brands fa-youtube"></i></a>
		@endif
		@if (!empty($settings['linkedin_link']))
        <a href="{{ $settings['linkedin_link'] }}" class="social-icon" target="_blank"><i class="fa-brands fa-linkedin-in"></i></a>
		@endif
      </div>
    </div>
  </div>
  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl d-flex align-items-center justify-content-between">
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
      @csrf
    </form>
    <a href="/" class="logo d-flex align-items-center">

	@if(!empty($sysconfig['logo_url']))
	<img class="logo_img" src="/storage/{{ $sysconfig['logo_url'] }}">
	@else
	<img class="logo_img" src="/i/your-logo.png" />
	@endif
      </a>
      <i class="mobile-nav-toggle mobile-nav-show bi bi-list"></i>
      <i class="mobile-nav-toggle mobile-nav-hide d-none bi bi-x"></i>
      <nav id="navbar" class="navbar">
        <ul>
          <li><a href="/">Home</a></li>
          <li><a href="/collections">Collections</a></li>
          <li>
            <a href="/contact">Contact</a>
          </li>
          @if(Auth::check())
          <li class="mobile-only"><a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">{{ __('Log out') }}</a></li>
              @else
              <li class="mobile-only"><a href="/login">Login</a></li>
              @endif
          @if(Auth::check() && Auth::user()->hasRole('admin'))
          <li class="mobile-only"><a href="/admin/usermanagement">{{ __('Manage Users') }}</a></li>
          <li class="mobile-only"><a href="/admin/rolesmanagement">{{ __('Manage Roles') }}</a></li>
          <li class="mobile-only"><a href="/admin/synonymsmanagement">{{ __('Manage Synonyms') }}</a></li>
          <li class="mobile-only"><a href="/admin/taxonomiesmanagement">{{ __('Manage Taxonomy') }}</a></li>
          <li class="mobile-only"><a href="/reports/search-queries">{{ __('Report of search queries') }}</a></li>
          <li class="mobile-only"><a href="/admin/srtemplatemanagement">{{ __('Manage Templates') }}</a></li>
          @endif          
	        <!-- <li><a class="get-a-quote" href="get-a-quote.html">Get a Quote</a></li> -->
          <li class="mega-menu">
            <a href="javascript:void(0)">
              <i class="js-mega-menu mega-menu-icon bi-list"></i>
              <i class="js-mega-menu mega-menu-close bi bi-x d-none"></i>
            </a>
            <div class="mega-menu-holder">
              <div class="container">
                <div class="row">
                  <div class="col-lg-6">
                    <ul class="mega-menu-ul">
                    @if(Auth::check())
                    <li><a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">{{ __('Log out') }}</a></li>
                    @else
                      <li><a href="/login">Login</a></li>
                    @endif
                    </ul>
                  </div>
                  <div class="col-lg-6">
                    <ul class="mega-menu-ul">
                    @if(Auth::check())
                      <li><a class="dropdown-item" href="/dashboard">{{ __('Dashboard') }}</a></li>
		                @endif		
                    @if(Auth::check() && Auth::user()->hasRole('admin'))
                      <!--li><a class="dropdown-item" href="/dashboard">{{ __('Dashboard') }}</a></li-->
                      <li><a class="dropdown-item" href="/admin/usermanagement">{{ __('Manage Users') }}</a></li>
                      <li><a class="dropdown-item" href="/admin/rolesmanagement">{{ __('Manage Roles') }}</a></li>
                      <li><a class="dropdown-item" href="/admin/synonymsmanagement">{{ __('Manage Synonyms') }}</a></li>
                      <li><a class="dropdown-item" href="/admin/taxonomiesmanagement">{{ __('Manage Taxonomy') }}</a></li>
                      <li><a class="dropdown-item" href="/reports/search-queries">{{ __('Report of search queries') }}</a></li>
			<li><a class="dropdown-item" href="/admin/srtemplatemanagement">{{ __('Manage Templates') }}</a></li>
                      
                      
                      @endif
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </li>
        </ul>
      </nav><!-- .navbar -->
    </div>
  </header><!-- End Header -->
  <!-- End Header -->
<div class="container">
  <div class="container-fluid">
    @yield('content')
  </div>
</div>
@if(!empty(env('OPENAI_API_KEY')))
    <script>
	    var botmanWidget = {
			frameEndpoint: '/chatbot-frame.html',
	        aboutText: 'ISA Repository',
			aboutLink: "/",
	        introMessage: "✋ Hello! <br />I will try to answer your questions based on the documents that we have on this portal.",
			title: "ISA RRR Chatbot",
			mainColor:"#f05a22",
			bubbleBackground:"#f05a22",
			bubbleAvatarUrl: "/i/chatbot.png",
	    };
    </script>
    <script src='/js/botman_widget.js'></script>
@endif
  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="container">
      <div class="row gy-2">
        <div class="col-lg-5 col-md-12 footer-info">
          <div class="social-links d-flex mt-4">
		@if (!empty($settings['facebook_link']))
            <a href="{{ $settings['facebook_link'] }}" class="facebook" target="_blank"><i class="bi bi-facebook"></i></a>
		@endif
		@if (!empty($settings['twitter_link']))
            <a href="{{ $settings['twitter_link'] }}" class="twitter" target="_blank"><i class="bi bi-twitter"></i></a>
		@endif
		@if (!empty($settings['instagram_link']))
            <a href="{{ $settings['instagram_link'] }}" class="instagram" target="_blank"><i class="bi bi-instagram"></i></a>
		@endif
		@if (!empty($settings['linkedin_link']))
            <a href="{{ $settings['linkedin_link'] }}" class="linkedin" target="_blank"><i class="bi bi-linkedin"></i></a>
		@endif
          </div>
        </div>

        <div class="col-lg-3 col-md-6 col-5 footer-contact text-md-start">
          <!--<h4>Quick Links</h4>-->
        </div>
        
        <div class="col-lg-4 col-md-6 col-7 footer-contact text-md-start">
          <!--<h4>Contact Us</h4>-->
          <p>
          </p>
        </div>
       <div class="copyright col-lg-12 col-md-12 col-12 footer-contact ">
        &copy; Copyright. All Rights Reserved
      </div> 
      </div>
    </div>
  </footer><!-- End Footer -->
  <!-- End Footer -->
  <a href="#" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<div id="preloader"></div>
<!-- Vendor JS Files -->
<script src="/css/classic/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/css/classic/vendor/purecounter/purecounter_vanilla.js"></script>
<script src="/css/classic/vendor/glightbox/js/glightbox.min.js"></script>
<script src="/css/classic/vendor/swiper/swiper-bundle.min.js"></script>
<script src="/css/classic/vendor/aos/aos.js"></script>
<script src="/css/classic/vendor/php-email-form/validate.js"></script>
<!-- Template Main JS File -->
<script src="{{ asset('material') }}/js/core/popper.min.js"></script>
<script src="{{ asset('material') }}/js/core/bootstrap-material-design.min.js"></script>
<script src="{{ asset('material') }}/js/plugins/perfect-scrollbar.jquery.min.js"></script>
<!-- Plugin for the momentJs  -->
<script src="{{ asset('material') }}/js/plugins/moment.min.js"></script>
<!-- Forms Validations Plugin -->
<script src="{{ asset('material') }}/js/plugins/jquery.validate.min.js"></script>
<!-- Plugin for the Wizard, full documentation here: https://github.com/VinceG/twitter-bootstrap-wizard -->
<!--script src="{{ asset('material') }}/js/plugins/jquery.bootstrap-wizard.js"></script-->
<!--    Plugin for Select, full documentation here: http://silviomoreto.github.io/bootstrap-select -->
<script src="{{ asset('material') }}/js/plugins/bootstrap-selectpicker.js"></script>
<!--  Plugin for the DateTimePicker, full documentation here: https://eonasdan.github.io/bootstrap-datetimepicker/ -->
<script src="{{ asset('material') }}/js/plugins/bootstrap-datetimepicker.min.js"></script>
<!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
<script src="{{ asset('material') }}/js/material-dashboard.js?v=2.1.1" type="text/javascript"></script>
<script src="{{ asset('material') }}/js/settings.js"></script>
</body>
</html>
