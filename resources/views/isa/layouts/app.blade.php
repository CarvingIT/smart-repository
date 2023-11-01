<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>ISA - international solar alliance</title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <!-- Favicons -->
  <link rel="apple-touch-icon" sizes="180x180" href="/css/isa/img/favicon/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="/css/isa/img/favicon/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/css/isa/img/favicon/favicon-16x16.png">
  <link rel="manifest" href="/css/isa/img/favicon/site.webmanifest">
  <meta name="msapplication-TileColor" content="#da532c">
  <meta name="theme-color" content="#ffffff">
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,600;1,700&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
  <!-- Vendor CSS Files -->
  <link href="/css/isa/material/css/material-dashboard.css" rel="stylesheet">
  <link href="/css/isa/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="/css/isa/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="/css/isa/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="/css/isa/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="/css/isa/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link href="/css/isa/vendor/aos/aos.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link rel="stylesheet" href="{{url('/css/isa/main.css')}}" />

  <!-- Fonts -->
  <link href="/css/isa/fonts.css" rel="stylesheet">
  <!--
  <link href="{{ asset('material') }}/css/material-dashboard.css?v=2.1.1" rel="stylesheet" />
  -->
  <link href="/css/custom.css" rel="stylesheet" />
  <link href="/css/jquery.dataTables.min.css" rel="stylesheet" />
  <link href="{{ asset('material') }}/css/bootstrap-select.min.css" rel="stylesheet" />
  <script src="https://code.jquery.com/jquery-3.7.0.min.js" crossorigin="anonymous"></script>
  <script src="/js/isa/main.js"></script>
  <!--script src="/js/jquery-3.5.1.js"></script-->

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

        color: #010a14 !important;
        }
        .text-light strong:hover {

        color: #010a14 !important;
        }
    </style>
<script>
	/*
	$(document).ready(function(){
		window.botmanChatWidget.whisper('q');
		window.botmanChatWidget.close();
	});
	*/
</script>
</head>
<body>
  <!-- ======= Header ======= -->
<div class="top-bar">
    <div class="container-fluid container-xl d-flex justify-content-end align-items-center">
       <div class="top-bar-link">
        Need a Guide? <a href="#">Start Here.</a>
      </div>
      <div class="social-media-header">
        <a href="https://www.facebook.com/InternationalSolarAlliance/" class="social-icon" target="_blank"><i class="fa-brands fa-facebook-f"></i></a>
        <a href="https://twitter.com/isolaralliance" class="social-icon" target="_blank"><i class="fa-brands fa-twitter"></i></a>
        <a href="https://www.youtube.com/@internationalsolaralliance" class="social-icon" target="_blank"><i class="fa-brands fa-youtube"></i></a>
        <a href="https://www.linkedin.com/company/internationalsolaralliance/?originalSubdomain=in" class="social-icon" target="_blank"><i class="fa-brands fa-linkedin-in"></i></a>
      </div>
    </div>
  </div>
  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl d-flex align-items-center justify-content-between">
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
      @csrf
    </form>
    <a href="/" class="logo d-flex align-items-center">
        <img src="/img/isa/img/site-logo.png" alt="Logo">
        <img class="sticke-logo" src="/img/isa/img/site-logo.png" alt="Logo">
        <div class="introduce-text">
          <span class="vertical-line"></span>
          <p>Regulation <br> Repository</p>
        </div>
      </a>
      <i class="mobile-nav-toggle mobile-nav-show bi bi-list"></i>
      <i class="mobile-nav-toggle mobile-nav-hide d-none bi bi-x"></i>
      <nav id="navbar" class="navbar">
        <ul>
          <li><a href="/">Home</a></li>
          <li><a href="/collection/1">Database</a></li>
          <li class="dropdown">
            <a href="/en/blog"><span>Collaborations</span><i class="bi bi-chevron-down dropdown-indicator"></i></a>
            <ul>
              <li><a href="/en/blog">Views</a></li>
            </ul>
          </li>
          @if(Auth::check())
          <li class="mobile-only"><a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">{{ __('Log out') }}</a></li>
              @else
              <li class="mobile-only"><a href="/login">Login</a></li>
              @endif
          <li class="mobile-only"><a href="/about">About Repository</a></li>
          <li class="mobile-only"><a href="/faq">FAQs</a></li>
          <li class="mobile-only"><a href="/feedback">Feedback</a></li>
          <li class="mobile-only"><a href="/contact-us">Contact Us</a></li>
          @if(Auth::check() && Auth::user()->hasRole('admin'))
          <li class="mobile-only"><a href="/admin/usermanagement">{{ __('Manage Users') }}</a></li>
          <li class="mobile-only"><a href="/admin/rolesmanagement">{{ __('Manage Roles') }}</a></li>
          <li class="mobile-only"><a href="/admin/synonymsmanagement">{{ __('Manage Synonyms') }}</a></li>
          <li class="mobile-only"><a href="/admin/taxonomiesmanagement">{{ __('Manage Taxonomy') }}</a></li>
          <li class="mobile-only"><a href="/reports/search-queries">{{ __('Report of search queries') }}</a></li>
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
                      <li><a href="/about">About Repository</a></li>
                      <li><a href="/faq">FAQs</a></li>
                      <li><a href="/feedback">Feedback</a></li>
                      <li><a href="/contact-us">Contact Us</a></li>
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
</div>
<div class="container">
  <div class="container-fluid">
    @yield('content')
  </div>
</div>
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
	<script>
		setTimeout(function(){
		window.botmanChatWidget.whisper('q');
		window.botmanChatWidget.close();
		}, 1000);
	</script>
  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="container">
      <div class="row gy-2">
        <div class="col-lg-5 col-md-12 footer-info">
          <a href="/" class="logo d-flex align-items-center">
            <img src="/img/isa/img/site-logo-light.png" alt="Logo">
          </a>
          <div class="social-links d-flex mt-4">
            <a href="https://www.facebook.com/InternationalSolarAlliance/" class="facebook" target="_blank"><i class="bi bi-facebook"></i></a>
            <a href="https://www.twitter.com/" class="twitter" target="_blank"><i class="bi bi-twitter"></i></a>
            <a href="https://www.instagram.com/" class="instagram" target="_blank"><i class="bi bi-instagram"></i></a>
            <a href="https://www.linkedin.com/company/internationalsolaralliance/?originalSubdomain=in" class="linkedin" target="_blank"><i class="bi bi-linkedin"></i></a>
          </div>
        </div>

        <div class="col-lg-3 col-md-6 col-5 footer-contact text-md-start">
          <h4>Quick Links</h4>
          <p><a href="/">Home</a><br>
            <a href="/about">About Repository</a><br>
            <a href="/collection/1">Database</a><br>
            <a href="/service">Terms of Service</a><br>
            <a href="/policy">Privacy Policy</a>
          </p>
        </div>
        
        <div class="col-lg-4 col-md-6 col-7 footer-contact text-md-start">
          <h4>Contact Us</h4>
          <p>
            International Solar Alliance Secretariat<br>
            Surya Bhawan, <br>
            National Institute of Solar Energy Campus Gwal Pahari,<br>
            Faridabad-Gurugram Road,<br>
            Gurugram, Haryana – 122003,
            India<br>
            <strong>Tel:</strong> +91 124 362 3090/69<br>
            <strong>Email:</strong> <a href="mailto:info@isolaralliance.org">info@isolaralliance.org</a><br>
          </p>
        </div>
       <div class="copyright col-lg-12 col-md-12 col-12 footer-contact ">
        &copy; Copyright <strong><span>ISA</span></strong>. All Rights Reserved
      </div> 
      </div>
    </div>
  </footer><!-- End Footer -->
  <!-- End Footer -->
  <a href="#" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<div id="preloader"></div>
<!-- Vendor JS Files -->
<script src="/css/isa/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/css/isa/vendor/purecounter/purecounter_vanilla.js"></script>
<script src="/css/isa/vendor/glightbox/js/glightbox.min.js"></script>
<script src="/css/isa/vendor/swiper/swiper-bundle.min.js"></script>
<script src="/css/isa/vendor/aos/aos.js"></script>
<script src="/css/isa/vendor/php-email-form/validate.js"></script>
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
