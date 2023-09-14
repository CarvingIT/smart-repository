<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>ISA - international solar alliance</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link rel="apple-touch-icon" sizes="180x180" href="css/isa/img/favicon/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="css/isa/img/favicon/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="css/isa/img/favicon/favicon-16x16.png">
  <link rel="manifest" href="css/isa/img/favicon/site.webmanifest">
  <meta name="msapplication-TileColor" content="#da532c">
  <meta name="theme-color" content="#ffffff">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,600;1,700&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="css/isa/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/isa/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="css/isa/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="css/isa/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="css/isa/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link href="css/isa/vendor/aos/aos.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link rel="stylesheet" href="{{url('css/isa/main.css')}}" />
  <link href="css/isa/main.css" rel="stylesheet">
  <link href="css/isa/main.css" rel="stylesheet">

  <!-- Fonts -->
  <link href="css/isa/fonts.css" rel="stylesheet">
  <link href="css/isa/fonts.css" rel="stylesheet">
  @stack('js') 
</head>
<body>
  <!-- ======= Header ======= -->
<div class="top-bar">
    <div class="container-fluid container-xl d-flex justify-content-end align-items-center">
       <div class="top-bar-link">
        Need a Guide? <a href="#">Start Here.</a>
      </div>
<div class="social-media-header">
        <a href="https://www.facebook.com/" class="social-icon" target="_blank"><i class="fa-brands fa-facebook-f"></i></a>
        <a href="https://twitter.com/" class="social-icon" target="_blank"><i class="fa-brands fa-twitter"></i></a>
        <a href="https://www.youtube.com/" class="social-icon" target="_blank"><i class="fa-brands fa-youtube"></i></a>
        <a href="https://in.linkedin.com" class="social-icon" target="_blank"><i class="fa-brands fa-linkedin-in"></i></a>
      </div>
     
    </div>
  </div>
  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl d-flex align-items-center justify-content-between">

      <a href="/" class="logo d-flex align-items-center">
        <!-- Uncomment the line below if you also wish to use an image logo -->
        <!-- <img src="design/assets/img/logo.png" alt=""> -->
        <!-- <img src="{{url('design/design/assets/img/site-logo.png')}}"> -->
        <img src="img/isa/img/site-logo.png" alt="Logo">
        <img class="sticke-logo" src="img/isa/img/site-logo.png" alt="Logo">
        <div class="introduce-text">
          <span class="vertical-line"></span>
          <p>Regulation <br> Repository</p>
        </div>
      </a>
      <i class="mobile-nav-toggle mobile-nav-show bi bi-list"></i>
      <i class="mobile-nav-toggle mobile-nav-hide d-none bi bi-x"></i>
      <nav id="navbar" class="navbar">
        <ul>
          <li><a href="/" class="active">Home</a></li>
          <li><a href="javascript:void(0)">Database</a></li>
          <li class="dropdown">
            <a href="/"><span>Collaborations</span><i class="bi bi-chevron-down dropdown-indicator"></i></a>
            <ul>
              <li><a href="javascript:void(0)">Opinions</a></li>
              <li><a href="javascript:void(0)">Link 2 (tbd)</a></li>
            </ul>
          </li>
          <li class="mobile-only"><a href="/abouts">About Repository</a></li>
          <li class="mobile-only"><a href="javascript:void(0)">FAQs</a></li>
          <li class="mobile-only"><a href="/contact">Contact Us</a></li>
          <!-- <li><a class="get-a-quote" href="get-a-quote.html">Get a Quote</a></li> -->
          <li class="mega-menu">
            <a href="javascript:void(0)">
              <i class="js-mega-menu mega-menu-icon bi-list"></i>
              <i class="js-mega-menu mega-menu-close bi bi-x d-none"></i>
            </a>
            <div class="mega-menu-holder">
              <div class="container">
                <div class="row">
                  <div class="col-lg-12">
                    <ul class="mega-menu-ul">
                      <li><a href="/login">Login | Register</a></li>
                      <li><a href="about.html">About Repository</a></li>
                      <li><a href="javascript:void(0)">FAQs</a></li>
                      <li><a href="feedback.html">Feedback</a></li>
                      <li><a href="contact.html">Contact Us</a></li>
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
@yield('content')
</div>




  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">

    <div class="container">
      <div class="row gy-2">
        <div class="col-lg-5 col-md-12 footer-info">
          <a href="/" class="logo d-flex align-items-center">
            <img src="img/isa/img/site-logo-light.png" alt="Logo">
          </a>
          <!-- <p>Cras fermentum odio eu feugiat lide par naso tierra. Justo eget nada terra videa magna derita valies darta donna mare fermentum iaculis eu non diam phasellus.</p> -->
          <div class="social-links d-flex mt-4">
            <a href="https://www.facebook.com/" class="facebook" target="_blank"><i class="bi bi-facebook"></i></a>
            <a href="https://www.twitter.com/" class="twitter" target="_blank"><i class="bi bi-twitter"></i></a>
            <a href="https://www.instagram.com/" class="instagram" target="_blank"><i class="bi bi-instagram"></i></a>
            <a href="https://www.linkedin.com/" class="linkedin" target="_blank"><i class="bi bi-linkedin"></i></a>
          </div>
        </div>

        <div class="col-lg-3 col-md-6 col-5 footer-links">
          <h4>Quick Links</h4>
          <ul>
            <li><a href="/">Home</a></li>
            <li><a href="#">About Repository</a></li>
            <li><a href="#">Database</a></li>
            <li><a href="#">Terms of Service</a></li>
            <li><a href="#">Privacy policy</a></li>
          </ul>
        </div>

        <!-- <div class="col-lg-2 col-6 footer-links">
          <h4>News & Updates</h4>
          <ul>
            <li><a href="#">Heading</a></li>
            <li><a href="#">Heading</a></li>
            <li><a href="#">Heading</a></li>
            <li><a href="#">Heading</a></li>
            <li><a href="#">Heading</a></li>
          </ul>
        </div> -->

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
      </div>
    </div>

    <div class="container mt-2">
      <div class="copyright">
        &copy; Copyright <strong><span>ISA</span></strong>. All Rights Reserved
      </div>
    </div>

  </footer><!-- End Footer -->
  <!-- End Footer -->
</body>
</html>
