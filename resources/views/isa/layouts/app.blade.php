<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>ISA - international solar alliance</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link rel="apple-touch-icon" sizes="180x180" href="design/assets/img/favicon/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="design/assets/img/favicon/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="design/assets/img/favicon/favicon-16x16.png">
  <link rel="manifest" href="design/assets/img/favicon/site.webmanifest">
  <meta name="msapplication-TileColor" content="#da532c">
  <meta name="theme-color" content="#ffffff">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,600;1,700&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="design/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="design/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="design/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="design/assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="design/assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link href="design/assets/vendor/aos/aos.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link rel="stylesheet" href="{{url('design/assets/css/main.css')}}" />
  <link href="design/assets/css/main.css" rel="stylesheet">
  <link href="design/assets/css/main.css" rel="stylesheet">

  <!-- Fonts -->
  <link href="design/assets/css/fonts.css" rel="stylesheet">
  <link href="design/assets/css/fonts.css" rel="stylesheet">


</head>

<body>

  <!-- ======= Header ======= -->
  <div class="top-bar">
    <div class="container-fluid container-xl d-flex justify-content-end align-items-center">
       <div class="top-bar-link">
        Need a Guide? <a href="#">Start Here.</a>
      </div>
<div class="social-media-header">
        <a href="https://www.facebook.com/" class="social-icon"><i class="fa-brands fa-facebook-f"></i></a>
        <a href="https://twitter.com/" class="social-icon"><i class="fa-brands fa-twitter"></i></a>
        <a href="https://www.youtube.com/" class="social-icon"><i class="fa-brands fa-youtube"></i></a>
        <a href="https://in.linkedin.com" class="social-icon"><i class="fa-brands fa-linkedin-in"></i></a>
      </div>
     
    </div>
  </div>
  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl d-flex align-items-center justify-content-between">

      <a href="index.html" class="logo d-flex align-items-center">
        <!-- Uncomment the line below if you also wish to use an image logo -->
        <!-- <img src="design/assets/img/logo.png" alt=""> -->
        <!-- <img src="{{url('design/design/assets/img/site-logo.png')}}"> -->
        <img src="design/assets/img/site-logo.png" alt="Logo">
        <img class="sticke-logo" src="design/assets/img/site-logo.png" alt="Logo">
        <div class="introduce-text">
          <span class="vertical-line"></span>
          <p>Regulation <br> Repository</p>
        </div>
      </a>
      

      <i class="mobile-nav-toggle mobile-nav-show bi bi-list"></i>
      <i class="mobile-nav-toggle mobile-nav-hide d-none bi bi-x"></i>
      <nav id="navbar" class="navbar">
        <ul>
          <li><a href="index.html" class="active">Home</a></li>
          <li><a href="javascript:void(0)">Database</a></li>
          <li class="dropdown">
            <a href="javascript:void(0)"><span>Collaborations</span><i class="bi bi-chevron-down dropdown-indicator"></i></a>
            <ul>
              <li><a href="javascript:void(0)">Opinions</a></li>
              <li><a href="javascript:void(0)">Link 2 (tbd)</a></li>
            </ul>
          </li>
          <li class="mobile-only"><a href="about.html">About Repository</a></li>
          <li class="mobile-only"><a href="javascript:void(0)">FAQs</a></li>
          <li class="mobile-only"><a href="contact.html">Contact Us</a></li>
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
                      <li><a href="{{ route('login') }}">Login | Register</a></li>
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

  <!-- ======= Hero Section ======= -->
  <section id="hero" class="hero d-flex align-items-center">
    <div class="container"  style="background-color: #eee;">
      <div class="row gy-4 d-flex justify-content-between">
        <div class="col-lg-12 order-2 order-lg-1 d-flex flex-column justify-content-center content-center">

          <h4 class="text-center" data-aos="fade-up">Regulation Resource Repository for Solar Energy</h4>
          <p class="text-center" data-aos="fade-up" data-aos-delay="100">A comprehensive data repository for all regulations in ISA member countries relating to Solar Energy</p>

          <form action="#" class="form-search d-flex align-items-stretch mb-4" data-aos="fade-up" data-aos-delay="200">
            <input type="text" class="form-control" placeholder="What are you looking for?">
            <button type="submit" class="btn btn-primary">Search</button>
          </form>

          <div class="row gy-4 mb-3" data-aos="fade-up" data-aos-delay="400">
            <!--<div class="col-lg-6 col-6 search-by">-->
            <!-- <div class="stats-item text-center w-100 h-100">-->
            <!--    <a href="javascript:void(0)" class="justify-content-end">Browse By</a>-->
            <!-- </div>-->
            <!--</div>-->
          <div class="col-lg-12 col-12 search-by">
              <div class="stats-item text-center w-100 h-100 browse-by">
                <p>Browse By:</p>
                <a href="javascript:void(0)" class="by-country">Country</a> Or 
              &nbsp;  <a href="javascript:void(0)" class="by-theme">Theme</a>
              </div>
            </div>
            <!-- End Stats Item -->
            <!--<div class="col-lg-6 col-6 search-by">-->
            <!--  <div class="stats-item text-center w-100 h-100">-->
            <!--    <a href="javascript:void(0)">By Theme</a>-->
            <!--  </div>-->
            <!--</div>-->
            <!-- End Stats Item -->
          </div>

          <div class="row" data-aos="fade-up" data-aos-delay="400"> 

            <div class="col-lg-5 col-6">
              <div class="stats-item text-center w-100 h-100">
                <span data-purecounter-start="0" data-purecounter-end="232" data-purecounter-duration="1" class="purecounter"></span>
                <a href="javascript:void(0)">LAWS, REGULATIONS AND POLICIES</a>
              </div>
            </div><!-- End Stats Item -->

            <div class="col-lg-3 col-6">
              <div class="stats-item text-center w-100 h-100">
                <span data-purecounter-start="0" data-purecounter-end="521" data-purecounter-duration="1" class="purecounter"></span>
                <a class="justify-content-center" href="javascript:void(0)">PUBLICATIONS</a>
              </div>
            </div><!-- End Stats Item -->

            <div class="col-lg-4 col-6">
              <div class="stats-item text-center w-100 h-100">
                <span data-purecounter-start="0" data-purecounter-end="1453" data-purecounter-duration="1" class="purecounter"></span>
                <a class="justify-content-center" href="javascript:void(0)">TECHNICAL STANDARDS</a>
              </div>
            </div><!-- End Stats Item -->

          </div>

        </div>
      </div>

    </div>
  </section><!-- End Hero Section -->


  <main id="main">
    <!-- ======= Services Section ======= -->
    <section id="service" class="services">
      <div class="container" data-aos="fade-up">
        <div class="section-header">
          <span>Major Themes</span>
          <h2>Major Themes</h2>
        </div>
        <div class="row gy-4">
          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
            <div class="card">
              <div class="card-img">
                <img src="design/assets/img/storage-service.jpg" alt="" class="img-fluid">
              </div>
              <h3><a href="javascript:void(0)" class="stretched-link">Laws, Regulations & Policies</a></h3>
              <p>Cumque eos in qui numquam. Aut aspernatur perferendis sed atque quia voluptas quisquam repellendus temporibus itaqueofficiis odit</p>
            </div>
          </div><!-- End Card Item -->
          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
            <div class="card">
              <div class="card-img">
                <img src="design/assets/img/logistics-service.jpg" alt="" class="img-fluid">
              </div>
              <h3><a href="javascript:void(0)" class="stretched-link">Publications</a></h3>
              <p>Asperiores provident dolor accusamus pariatur dolore nam id audantium ut et iure incidunt molestiae dolor ipsam ducimus occaecati nisi</p>
            </div>
          </div><!-- End Card Item -->
          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
            <div class="card">
              <div class="card-img">
                <img src="design/assets/img/cargo-service.jpg" alt="" class="img-fluid">
              </div>
              <h3><a href="javascript:void(0)" class="stretched-link">Technical Standards</a></h3>
              <p>Dicta quam similique quia architecto eos nisi aut ratione aut ipsum reiciendis sit doloremque oluptatem aut et molestiae ut et nihil</p>
            </div>
          </div><!-- End Card Item -->
        </div>
      </div>
    </section>
    <!-- End Services Section -->

    <!-- ======= News & Updates Section ======= -->
    <section id="news-updates" class="news-updates-section pt-0">
      <div class="container" data-aos="fade-up">
        <div class="section-header">
          <span> News & Updates</span>
          <h2> News & Updates</h2>
        </div>
        <div class="slides-3 swiper" data-aos="fade-up">
          <div class="swiper-wrapper" >
            <div class="swiper-slide">
              <div class="news-updates-item">
                <img src="design/assets/img/news-updates/news-updates1.jpg" class="news-updates-img" alt="">
                <div class="news-updates-content">
                  <h3>heading</h3>
                  <p>
                    Proin iaculis purus consequat sem cure digni ssim donec porttitora entum suscipit rhoncus. Accusantium quam, ultricies eget id, aliquam eget nibh et. Maecen aliquam, risus at semper.
                  </p>
                </div>
              </div>
            </div><!-- End news-updates item -->
            <div class="swiper-slide">
              <div class="news-updates-item">
                <img src="design/assets/img/news-updates/news-updates2.jpg" class="news-updates-img" alt="">
                <div class="news-updates-content">
                  <h3>heading</h3>
                  <p>
                    Export tempor illum tamen malis malis eram quae irure esse labore quem cillum quid cillum eram malis quorum velit fore eram velit sunt aliqua noster fugiat irure amet legam anim culpa.
                  </p>
                </div>
              </div>
            </div><!-- End news-updates item -->
            <div class="swiper-slide">
              <div class="news-updates-item">
                <img src="design/assets/img/news-updates/news-updates3.jpg" class="news-updates-img" alt="">
                <div class="news-updates-content">
                  <h3>heading</h3>
                  <p>
                    Enim nisi quem export duis labore cillum quae magna enim sint quorum nulla quem veniam duis minim tempor labore quem eram duis noster aute amet eram fore quis sint minim.
                  </p>
                </div>
              </div>
            </div><!-- End news-updates item -->
            <div class="swiper-slide">
              <div class="news-updates-item">
                <img src="design/assets/img/news-updates/news-updates4.jpg" class="news-updates-img" alt="">
                <div class="news-updates-content">
                  <h3>heading</h3>
                  <p>
                    Fugiat enim eram quae cillum dolore dolor amet nulla culpa multos export minim fugiat minim velit minim dolor enim duis veniam ipsum anim magna sunt elit fore quem dolore labore illum veniam.
                  </p>
                </div>
              </div>
            </div><!-- End news-updates item -->
            <div class="swiper-slide">
              <div class="news-updates-item">
                <img src="design/assets/img/news-updates/news-updates5.jpeg" class="news-updates-img" alt="">
                <div class="news-updates-content">
                  <h3>heading</h3>
                  <p>
                    Quis quorum aliqua sint quem legam fore sunt eram irure aliqua veniam tempor noster veniam enim culpa labore duis sunt culpa nulla illum cillum fugiat legam esse veniam culpa fore nisi cillum quid.
                  </p>
                </div>
              </div>
            </div><!-- End news-updates item -->
          </div>
          <!-- <div class="swiper-pagination"></div> -->
        </div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
      </div>
    </section> 
    <!-- End News & Updates Section -->

    <!-- ======= Frequently Asked Questions Section ======= -->
    <!-- <section id="faq" class="faq">
      <div class="container" data-aos="fade-up">
        <div class="section-header">
          <span>Frequently Asked Questions</span>
          <h2>Frequently Asked Questions</h2>
        </div>
        <div class="row justify-content-center" data-aos="fade-up" data-aos-delay="200">
          <div class="col-lg-10">
            <div class="accordion accordion-flush" id="faqlist">
              <div class="accordion-item">
                <h3 class="accordion-header">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-content-1">
                    <i class="bi bi-question-circle question-icon"></i>
                    Non consectetur a erat nam at lectus urna duis?
                  </button>
                </h3>
                <div id="faq-content-1" class="accordion-collapse collapse" data-bs-parent="#faqlist">
                  <div class="accordion-body">
                    Feugiat pretium nibh ipsum consequat. Tempus iaculis urna id volutpat lacus laoreet non curabitur gravida. Venenatis lectus magna fringilla urna porttitor rhoncus dolor purus non.
                  </div>
                </div>
              </div>
              <div class="accordion-item">
                <h3 class="accordion-header">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-content-2">
                    <i class="bi bi-question-circle question-icon"></i>
                    Feugiat scelerisque varius morbi enim nunc faucibus a pellentesque?
                  </button>
                </h3>
                <div id="faq-content-2" class="accordion-collapse collapse" data-bs-parent="#faqlist">
                  <div class="accordion-body">
                    Dolor sit amet consectetur adipiscing elit pellentesque habitant morbi. Id interdum velit laoreet id donec ultrices. Fringilla phasellus faucibus scelerisque eleifend donec pretium. Est pellentesque elit ullamcorper dignissim. Mauris ultrices eros in cursus turpis massa tincidunt dui.
                  </div>
                </div>
              </div>
              <div class="accordion-item">
                <h3 class="accordion-header">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-content-3">
                    <i class="bi bi-question-circle question-icon"></i>
                    Dolor sit amet consectetur adipiscing elit pellentesque habitant morbi?
                  </button>
                </h3>
                <div id="faq-content-3" class="accordion-collapse collapse" data-bs-parent="#faqlist">
                  <div class="accordion-body">
                    Eleifend mi in nulla posuere sollicitudin aliquam ultrices sagittis orci. Faucibus pulvinar elementum integer enim. Sem nulla pharetra diam sit amet nisl suscipit. Rutrum tellus pellentesque eu tincidunt. Lectus urna duis convallis convallis tellus. Urna molestie at elementum eu facilisis sed odio morbi quis
                  </div>
                </div>
              </div>
            </div>
            <div class="button-hoder">
              <a class="btn btn-primary" href="javascript:void(0)">Read More</a>
            </div>
          </div>
        </div>
      </div>
    </section> -->
    <!-- End Frequently Asked Questions Section -->

    <!-- ======= Call To Action Section ======= -->
  <!--  <section id="call-to-action" class="call-to-action">
      <div class="container" data-aos="zoom-out">

        <div class="row justify-content-center">
          <div class="col-lg-8 text-center">
            <h3>FEEDBACK</h3>
            <p> Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
            <a class="cta-btn" href="#">FEEDBACK</a>
          </div>
        </div>

      </div>
    </section>-->
    <!-- End Call To Action Section -->

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">

    <div class="container">
      <div class="row gy-2">
        <div class="col-lg-5 col-md-12 footer-info">
          <a href="index.html" class="logo d-flex align-items-center">
            <img src="design/assets/img/site-logo-light.png" alt="Logo">
          </a>
          <!-- <p>Cras fermentum odio eu feugiat lide par naso tierra. Justo eget nada terra videa magna derita valies darta donna mare fermentum iaculis eu non diam phasellus.</p> -->
          <div class="social-links d-flex mt-4">
            <a href="#" class="twitter"><i class="bi bi-twitter"></i></a>
            <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
            <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
            <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
          </div>
        </div>

        <div class="col-lg-3 col-md-6 col-5 footer-links">
          <h4>Quick Links</h4>
          <ul>
            <li><a href="#">Home</a></li>
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

  <a href="#" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="design/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="design/assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="design/assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="design/assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="design/assets/vendor/aos/aos.js"></script>
  <script src="design/assets/vendor/php-email-form/validate.js"></script>
  <script src="https://code.jquery.com/jquery-3.7.0.min.js" crossorigin="anonymous"></script>
  <!-- Template Main JS File -->
  <script src="design/assets/js/main.js"></script>

</body>

</html>
