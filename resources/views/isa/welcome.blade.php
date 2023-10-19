@extends('layouts.app',['class' => 'off-canvas-sidebar','title'=>'Smart Repository','activePage'=>'contact','titlePage'=>'Contact Us'])

@section('content')

  <!-- ======= Hero Section ======= -->
  <section id="hero" class="hero d-flex align-items-center">
    <div class="container"  style="background-color: #eee;">
      <div class="row gy-4 d-flex justify-content-between">
        <div class="col-lg-12 order-2 order-lg-1 d-flex flex-column justify-content-center content-center">

          <h4 class="text-center" data-aos="fade-up">Regulation Resource Repository for Solar Energy</h4>
          <p class="text-center" data-aos="fade-up" data-aos-delay="100">A comprehensive data repository for all regulations in ISA member countries relating to Solar Energy</p>

          <form action="/collection/1" class="form-search d-flex align-items-stretch mb-4" data-aos="fade-up" data-aos-delay="200" method="get" id="isa_search" name="isa_search">
			<input type="hidden" name="collection_id" value="1" />
            <input type="text" class="form-control form-group" name="isa_search_parameter" placeholder="What are you looking for abc?">
            <button type="submit" value="Search" name="isa_search" class="btn btn-primary">Search</button>
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
                <a href="/countries" class="by-country">Country</a> Or 
              &nbsp;  <a href="/themes" class="by-theme">Theme</a>
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

				@php
					$model_families = [];
					$model_ids = [];
					$document_ids = [];
					$document_counts = [];
					
					$major_themes = explode("|",env('MAJOR_THEMES','A1|A2|A3'));
					foreach($major_themes as $mt){
						// get model of each
						$theme_model = \App\Taxonomy::where('label', $mt)->first();
						$model_families[$mt] = empty($theme_model)?[]: $theme_model->createFamily();
					}
					foreach($model_families as $mt=>$mf){
						foreach($mf as $m){
							$model_ids[$mt][] = $m->id;
						}
					}
					//print_r($model_ids);
					$theme_field_label = env('THEME_FIELD_LABEL','Theme');
					$meta_field = \App\MetaField::where('collection_id',1)->where('label',$theme_field_label)->first();
					if($meta_field){
						foreach($major_themes as $mt){
							$rmfv_models = \App\ReverseMetaFieldValue::where('meta_field_id', $meta_field->id)
							->whereIn('meta_value', isset($model_ids[$mt])? $model_ids[$mt]: [])->get();
							foreach($rmfv_models as $m){
								$document_ids[$mt][] = $m->document_id;
							}
							if(isset($document_ids[$mt])){
								$document_ids[$mt] = array_unique($document_ids[$mt]);
								$document_counts[$mt] = count($document_ids[$mt]);
							}
						}
					}
				@endphp

		<!--
          <div class="row" data-aos="fade-up" data-aos-delay="400"> 
            <div class="col-lg-5 col-6">
              <div class="stats-item text-center w-100 h-100">
                <span data-purecounter-start="0" data-purecounter-end="232" data-purecounter-duration="1" class="purecounter"></span>
                <a href="javascript:void(0)">LAWS, REGULATIONS AND POLICIES</a>
              </div>
            </div>

            <div class="col-lg-3 col-6">
              <div class="stats-item text-center w-100 h-100">
                <span data-purecounter-start="0" data-purecounter-end="521" data-purecounter-duration="1" class="purecounter"></span>
                <a class="justify-content-center" href="javascript:void(0)">PUBLICATIONS</a>
              </div>
            </div>

            <div class="col-lg-4 col-6">
              <div class="stats-item text-center w-100 h-100">
                <span data-purecounter-start="0" data-purecounter-end="1453" data-purecounter-duration="1" class="purecounter"></span>
                <a class="justify-content-center" href="javascript:void(0)">TECHNICAL STANDARDS</a>
              </div>
            </div>
          </div>
		-->

          <div class="row" data-aos="fade-up" data-aos-delay="400"> 
			@foreach($major_themes as $mt)
            <div class="col-lg-4 col-4">
              <div class="stats-item text-center w-100 h-100">
                <span data-purecounter-start="0" data-purecounter-end="{{ isset($document_counts[$mt])?$document_counts[$mt]:0 }}" data-purecounter-duration="1" class="purecounter"></span>
                <a class="justify-content-center" href="javascript:void(0)">{{ $mt }}</a>
              </div>
            </div><!-- End Stats Item -->
			@endforeach
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
                <img src="img/isa/img/news-updates/news-updates1.jpg" class="news-updates-img" alt="">
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
                <img src="img/isa/img/news-updates/news-updates2.jpg" class="news-updates-img" alt="">
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
                <img src="img/isa/img/news-updates/news-updates3.jpg" class="news-updates-img" alt="">
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
                <img src="img/isa/img/news-updates/news-updates4.jpg" class="news-updates-img" alt="">
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
                <img src="img/isa/img/news-updates/news-updates5.jpeg" class="news-updates-img" alt="">
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

@endsection

