@extends('layouts.app', ['class' => 'off-canvas-sidebar','activePage' => 'home', 'title' => __('DEMO SITE'), 'titlePage' => __('Collections')])

@section('content')
@php
	$conf = \App\Sysconfig::all();
	$settings = array();
	foreach($conf as $c){
		$settings[$c->param] = $c->value;
	}

@endphp
@push('js')
<style>
.search-container {
    text-align: center;
    margin: 5px;
    margin-left:10px;
    width: 100%;
}
.search-buttons {
    display: flex;
    gap: 10px;
    padding: 0 5px;
    justify-content: start;
    margin-bottom: 4px;
}
.search-buttons button {
  display: grid;
  place-items: center;
  background: #e3edf7;
  color: #555;
  padding: 7px 10px;
  font-weight: 500;
  font-size: 14px;
  border-radius: 10px 10px 0 0;
  box-shadow: rgba(50, 50, 93, 0.25) 0px 0px 12px -2px, rgba(0, 0, 0, 0.3) 0px 3px 7px -3px;
  border: 1px solid rgba(0,0,0,0);
  cursor: pointer;
  transition: transform 0.5s;
  opacity: .6;
}

.search-buttons button:hover {
  box-shadow: inset 4px 4px 6px -1px rgba(0,0,0,0.2),
	      inset -4px -4px 6px -1px rgba(255,255,255,0.7),
	      -0.5px -0.5px 0px rgba(255,255,255,1),
	      0.5px 0.5px 0px rgba(0,0,0,0.15),
	      0px 12px 10px -10px rgba(0,0,0,0.05);
  opacity: 1;
  /* transform: translateY(0.5em); */
}
.search-buttons button.active {
    background: #ffffff;
    color: #ff5619;
    font-weight:700;
    transform: scale(1.1);
    opacity: 1;
box-shadow: rgba(0, 0, 0, 0.05) 0px 6px 24px 0px, rgba(0, 0, 0, 0.08) 0px 0px 0px 1px;
}
.search-box {
    display: flex;
    justify-content:space-between;
    align-items: center;
    width: 100%;
    /* border: 2px solid #ff5619; */
    border-radius:0 0 10px 10px;
    background: #fff;
    box-shadow: rgba(0, 0, 0, 0.05) 0px 6px 24px 0px, rgba(0, 0, 0, 0.08) 0px 0px 0px 1px;
    padding-right: 5px;
}
.buttonSide{
gap:10px;
display:flex;
align-items: center;
padding: 0 5px;
}

.search-box input{
    border: none;
    border-radius:0 0 10px 10px;
    background: #fff;
    width: 80% !important;

}
.search-box .search{
    padding:0.40625rem 1.25rem !important;
}
.closeIcon{
color:black;
cursor:pointer;
font-size: 18px !important;
}
.closeIcon:hover{
color:#ff5619;
}
.line{
width:0.5px;
height:30px;
background-color:#adb5bd; 
}
.line p{
opacity:0;
}
.tooltip {
  position: relative;
margin-bottom:7px;
z-index:0;
  display: inline-block;
  cursor: pointer;
  border-bottom: none;
}

.tooltip .tooltiptext {
  visibility: hidden;
  width: 70px;
  font-size: 13px;
  background-color: black;
  border: 2px solid white;
  color: #fff;
  text-align: center;
  border-radius: 6px;
  padding: 5px 0;
  position: absolute;
  top: 154%;
  left: 50%;
  margin-left: -35px;
  box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
}

.tooltip .tooltiptext::after {
  content: "";
  position: absolute;
  bottom: 100%;
  border: 2px solid white;
  left: 50%;
  margin-left: -5px;
  border-width: 5px;
  border-style: solid;
  border-color: transparent transparent black transparent;
}

.tooltip:hover .tooltiptext {
  visibility: visible;
}
</style>
<script>
function clearSearchBar(){
     const searchText = document.getElementById('collection_search');
     searchText.value = "";
   }

</script>
@endpush

<div class="container">
<div class="container-fluid">
<div class="row justify-content-center">
      <div class="col-md-12">
		<div class="card">
			<div class="card-header card-header-primary"><h4 class="card-title">{{ __(env('APP_NAME')) }}</h4></div>
			<div class="card-body">
				<div class="row justify-content-center">
				@if(!empty($settings['banner_image_1']))
				@endif
				</div>
				@if(!empty($settings['home_page']))
				{!! $settings['home_page'] !!}
				@endif
			</div>
			<div class="card-body">
                <h4 class="text-center" data-aos="fade-up">{{ __('Global Document Search') }}</h4>
                <p class="text-center" data-aos="fade-up" data-aos-delay="100">{{ __('Search across collections for any document!') }}</p>
          <form action="/global-search" class="form-search d-flex align-items-stretch mb-4 aos-init aos-animate" data-aos="fade-up" data-aos-delay="200" method="get">
                <div class="search-container">
                  <div class="upperDiv"></div>      
                    <div class="search-box">
                        <input type="text" id="collection_search" class="search-field form-control form-group" name="search[value]" placeholder="{{ __('Search all collections') }}" /> 
                        <input type="hidden" name="full_text_scope" value="title_n_content" />
                        <div class="buttonSide">
                            <div class="tooltip">
                                <i class="fa-solid fa-xmark closeIcon" onclick="clearSearchBar()"></i>
                                <span class="tooltiptext">{{ __('Clear') }}</span>
                            </div>
                            <div class="line">
                                <p>line</p>
                            </div>
                        <button type="submit" value="Search" name="collection_search" class="btn btn-primary search">{{ __('Search') }}</button>
                        </div>
                    </div><!-- search-box -->
                </div><!-- search-container -->
          </form>

			</div>
		</div>
      </div>
</div>
</div>
</div>
@endsection
