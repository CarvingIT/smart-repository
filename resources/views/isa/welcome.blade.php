@extends('layouts.app',['class' => 'off-canvas-sidebar','title'=>'Smart Repository','activePage'=>'contact','titlePage'=>'Contact Us'])

@section('content')
@push('js')
<script src="/js/jquery-ui.js" defer></script>
<link href="/css/jquery-ui.css" rel="stylesheet">
<script>
	@if(env('SEARCH_MODE') == 'elastic')
	$(document).ready(function() {
        //alert("js is working");
        src = "{{ route('autosuggest') }}";
        $( "#collection_search" ).autocomplete({
            source: function( request, response ) {
                $.ajax({
                    url: src,
                    method: 'GET',
                    dataType: "json",
                    data: {
                        term : request.term
                    },
                    success: function(data) {
						if(data.length > 0)
                        response(data);
                    },
                });
            },
			select: function (event, ui){
				$("#collection_search").val(ui.item.value);
				return false;
			},
            minLength: 1,
        });
    });
	@endif
</script>
@endpush
  <!-- ======= Hero Section ======= -->
  <section id="hero" class="hero d-flex align-items-center">
    <div class="container"  style="background-color: #eee;">
      <div class="row gy-4 d-flex justify-content-between">
        <div class="col-lg-12 order-2 order-lg-1 d-flex flex-column justify-content-center content-center">

          <h4 class="text-center" data-aos="fade-up">Regulation Resource Repository for Solar Energy</h4>
          <p class="text-center" data-aos="fade-up" data-aos-delay="100">A comprehensive data repository for all regulations in ISA member countries relating to Solar Energy</p>

          <form action="/collection/1" class="form-search d-flex align-items-stretch mb-4" data-aos="fade-up" data-aos-delay="200" method="get" id="isa_search" name="isa_search">
		@csrf
			<input type="hidden" name="collection_id" value="1" />
            <input type="text" id="collection_search" class="form-control form-group" name="isa_search_parameter" placeholder="Search Data e.g. Laws, Publication and Technical Standards.">
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
					$major_theme_models = [];
					foreach($major_themes as $mt){
						// get model of each
						$theme_model = \App\Taxonomy::where('label', $mt)->first();
						$major_theme_models[$mt] = $theme_model;
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
							->whereHas('document',function($q){
								$q->whereNotNull('approved_on');
							})
							->whereIn('meta_value', isset($model_ids[$mt])? $model_ids[$mt]: [])
							->get();
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

          <div class="row" data-aos="fade-up" data-aos-delay="400"> 
			@foreach($major_themes as $mt)
            <div class="col-lg-4 col-4">
              <div class="stats-item text-center w-100 h-100">
                <span data-purecounter-start="0" data-purecounter-end="{{ isset($document_counts[$mt])?$document_counts[$mt]:0 }}" data-purecounter-duration="1" class="purecounter"></span>
                <a class="justify-content-center" href="/collection/1?meta_{{ $meta_field->id }}[]={{ $major_theme_models[$mt]->id }}">{{ $mt }}</a>
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
              <h3><a href="javascript:void(0)">Laws, Regulations & Policies</a></h3>
              <p class ="beautify">This section provides a comprehensive repository of laws, rules, and regulations governing clean and renewable energy, with a specific emphasis on solar energy, in ISA member countries. Explore this resource to gain insights into the legal framework shaping the sustainable energy transition across our diverse member nations.</p>
              <p><a href="/laws" class="stretched-link">Read More >> </a></p>
            </div>
          </div><!-- End Card Item -->
          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
            <div class="card">
              <div class="card-img">
                <img src="design/assets/img/logistics-service.jpg" alt="" class="img-fluid">
              </div>
              <h3><a href="javascript:void(0)" class="stretched-link">Publications</a></h3>
              <p class ="beautify">This section offers a comprehensive collection of publications from ISA member countries. This diverse resource includes research papers, books, reports, case studies, theses, and more, spanning a broad spectrum of topics related to solar energy.</p>
              <p><a href="/publications" class="stretched-link">Read More >> </a></p>
            </div>
          </div><!-- End Card Item -->
          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
            <div class="card">
              <div class="card-img">
                <img src="design/assets/img/cargo-service.jpg" alt="" class="img-fluid">
              </div>
              <h3><a href="javascript:void(0)" class="stretched-link">Technical Standards</a></h3>
              <p class ="beautify">This section enlists various standards developed by governmental and non-governmental standardization organizations relating to technical specifications crucial for maintaining consistency, quality, and safety in solar energy fields. These standards foster uniformity and best practices across solar technology design, production, processes, and services, promoting industry excellence.</p>
              <p> <a href="/technical" class="stretched-link">Read More >> </a></p>
            </div>
          </div><!-- End Card Item -->
        </div>
      </div>
    </section>
    <!-- End Services Section -->

    <!-- ======= News & Updates Section ======= -->
@php
$feeds = ['https://www.solarpowerworldonline.com/feed/', 'https://cleantechnica.com/feed/','https://www.pveurope.eu/rss_feed/pve-rss-feed-news'];
$items = [];
foreach($feeds as $feed){
	$f = FeedReader::read($feed);
	$items_new = $f->get_items();
	$items = array_merge($items, array_slice($items_new,0,4));
}
@endphp

    <section id="news-updates" class="news-updates-section pt-0">
      <div class="container" data-aos="fade-up">
        <div class="section-header">
          <span> News & Updates</span>
          <h2> News & Updates</h2>
        </div>
        <div class="slides-3 swiper" data-aos="fade-up">
          <div class="swiper-wrapper" >
	
@foreach ($items as $item)
            <div class="swiper-slide">
              <div class="news-updates-item">
				<!--
                <img src="img/isa/img/news-updates/news-updates1.jpg" class="news-updates-img" alt="">
				-->
                <div class="news-updates-content">
                  <h3><a href="{{ $item->get_link() }}" target="_new">{{ $item->get_title() }}</a></h3>
                
                  <?php
                  $content = $item->get_content();
                  $content = preg_replace('/<a(.*?)href=["\'](https?:\/\/[^\s"\'<>]+)["\'](.*?)>/i', '<a$1href="$2"$3 target="_blank">', $content);
                  echo '<p>' . $content . '</p>';
                  ?>
                </div>
              </div>
            </div><!-- End news-updates item -->
@endforeach
		
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

