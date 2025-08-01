@extends('layouts.app', ['class' => 'off-canvas-sidebar','activePage' => 'home', 'title' => __('DEMO SITE'), 'titlePage' => 'Collections'])

@section('content')

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
    document.addEventListener('DOMContentLoaded', () => {
    const allContentButton = document.getElementById('all-content');
    const titleButton = document.getElementById('title');
    const searchText = document.getElementById('collection_search');
    const title_search = document.getElementById('title_search');

    allContentButton.addEventListener('click', () => {
        title_search.value = 0;
        searchText.placeholder = "Search Data e.g. Debates, Ayurved, News etc.."
        allContentButton.classList.add('active');
        titleButton.classList.remove('active');
    });

    titleButton .addEventListener('click', () => {
        title_search.value = 1;
        searchText.placeholder = "Search Title"
        titleButton.classList.add('active');
        allContentButton.classList.remove('active');
    });

    // Function to get URL parameters
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        const regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        const results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }

    // Set the active tab based on the value of title_search URL parameter
    const titleSearchParam = getUrlParameter('title_search');
    if (titleSearchParam === '1') {
        titleButton.click();
    } else {
        allContentButton.click();
    }});


function clearSearchBar(){
     const searchText = document.getElementById('collection_search');
     searchText.value = "";
   }

</script>

<div class="container">
<h4 id="collection_links"></h4>
<div class="container-fluid">
<div class="row justify-content-center">
      <div class="col-md-12">
		<div class="card">
			<div class="card-header card-header-primary"><h4 class="card-title">{{ env('APP_NAME') }}</h4></div>
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
                <h4 class="text-center" data-aos="fade-up">Search across collections</h4>
                <!--<p class="text-center" data-aos="fade-up" data-aos-delay="100">A comprehensive data repository for all document types</p>-->

          <form action="/global-search" class="form-search d-flex align-items-stretch mb-4 aos-init aos-animate" data-aos="fade-up" data-aos-delay="200" method="get">
                <input type="hidden" name="length" id="search-results-length" value="1000" />
                <input type="hidden" name="start" id="search-results-start" value="0" />
                <input type="hidden" name="full_text_scope" value="title_n_content" />

                <div class="search-container">
                  <div class="upperDiv"></div>      
                    <div class="search-box">
                        <input type="text" id="collection_search" class="search-field form-control form-group" name="search[value]" placeholder="Search all collections" value="{{ @$search_term }}" /> 
                        <div class="buttonSide">
                            <div class="tooltip">
                                <i class="fa-solid fa-xmark closeIcon" onclick="clearSearchBar()"></i>
                                <span class="tooltiptext">Clear</span>
                            </div>
                            <div class="line">
                                <p>line</p>
                            </div>
                        <button type="submit" value="Search" name="searchbutton" class="btn btn-primary search">Search</button>
                        </div>
                    </div><!-- search-box -->
                </div><!-- search-container -->
          </form>


<div class="row gy-4">
    <div class="col-md-3">
        @php
            $collection_names = [];
            $i = 0;
            //$highlights = $results->highlights;
        @endphp

        @if(!empty($results->data))
        @foreach($results->data as $d)
        @php //print_r($d); 
            $document = \App\Document::find($d->id);
            $collection_names[$d->collection_id][] = $document->collection->name;
            $highlights = (array) @$results->highlights;
        @endphp
        @endforeach
        <ul>
        @if(count($collection_names) > 0)
        <li><strong>Collections</strong></li>
        @endif
        @foreach($collection_names as $key => $value)
            <li><a href="/collection/{{ $key }}?search_term={{ $search_term }}">{{ $value[0] }}</a> ({{ count($value) }})</li>
        @endforeach
        </ul>
        @endif
    </div>
    <div class="col-md-9" id="search-results">
    @if(!empty($results->data))
    @foreach($results->data as $d)
        @php //print_r($d); 
            $document = \App\Document::find($d->id);
        @endphp
        <div class="row">
            <div class="col-md-11">
                @php
                $record_highlights = array_shift($highlights);
                $title = empty($record_highlights->title)? $d->title: $record_highlights->title[0];
                $record_highlights = (array) $record_highlights;
                $content_matches=[];
                foreach($record_highlights as $k=>$m){
                    $content_matches_temp = array_map(function($item){
                            return strip_tags($item, '<em>');
                        }, $m);
                    foreach($content_matches_temp as $cm){
                        if(!in_array($cm, $content_matches)){
                            $content_matches[] = $cm;
                        }
                    }
                }
                @endphp
                 <h4><i class="material-icons">description</i><a href="/collection/{{ $d->collection_id }}/document/{{ $d->id }}/details" target="_blank">{!! $title !!}</a></h4>

            <p style="text-align:justify;">
                {!! implode('...',$content_matches) !!}
            </p>
           </div>
        </div>
        <div class="row">
            <div class="col-md-4"><i class="material-icons">folder</i> {{ @$document->collection->name }}</div>
            <!--<div class="col-md-8"><i class="material-icons">calendar_today</i>@php $date = strtotime(@$document->updated_at); echo date('F d, Y',$date); @endphp</div>-->
            <br /><br />
        </div>
    @endforeach
<div class="text-right"><h5><a href="#collection_links" title="Return to Top"><i class="material-icons">arrow_upward</i></a></h5></div>
    @endif
@if(!empty($results->data) && count($results->data) > 100)
<p>To narrow down your search, use links on the left.</p>
@endif
    </div>
</div><!-- row gy-4 -->

			</div>
		</div>
      </div>

</div>
</div>
</div>
@endsection
