@extends('layouts.app',['class' => 'off-canvas-sidebar','title'=>'Smart Repository','activePage'=>'contact','titlePage'=>'Contact Us'])
@push('js')
<script src="/js/jquery-ui.js" defer></script>
<link href="/css/jquery-ui.css" rel="stylesheet">
<style>
	.form-check-label{
		display:inline-block;
		width:80%;
	}
.loader {
  margin:0 auto;
  border: 16px solid #f3f3f3; /* Light grey */
  border-top: 16px solid #f05a22; /* Orange */
  border-radius: 50%;
  width: 120px;
  height: 120px;
  animation: spin 2s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
<script>
$(document).ready(function() {
  $(window).keydown(function(event){
    if(event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });
});
@php

	// get meta fields of this collection
	$meta_fields = $collection->meta_fields;
	//$filter_labels = [env('THEME_FIELD_LABEL','Theme'),env('COUNTRY_FIELD_LABEL','Country'), env('YEAR_FIELD_LABEL','Year')];
	//$filter_labels = [env('THEME_FIELD_LABEL','Theme'),env('COUNTRY_FIELD_LABEL','Country')];
	$filters = [];
	foreach($meta_fields as $m){
		//if(in_array($m->label, $filter_labels)){
		if($m->is_filter == 1){//SKK
			$filters[] = $m;
		}
	}	


	$url = '/collection/1/search-results?analyzer='.request()->get('analyzer').'&isa_search_parameter='.urlencode(request()->get('isa_search_parameter'));
@endphp
$(document).ready(function() {
	//$("#search-results").load('{{ $url }}');
	reloadSearchResults();
});

function clearFilters(){
	// clear checkboxes
	$('input[type="checkbox"]').each(function() {
			this.checked = false;
	});
	// reset range filter
   @foreach ($filters as $f)
	   @php
              $extra_attributes = empty($f->extra_attributes)? null : json_decode($f->extra_attributes);
              $numeric_min_value = @$extra_attributes->numeric_min_value;
              $numeric_max_value = @$extra_attributes->numeric_max_value;
           @endphp

   @if ($f->type == 'Numeric')
	//$('#meta_{{ $f->id }}_lower_slider').val(1950);
	//$('#meta_{{ $f->id }}_upper_slider').val(2023);
	$('#meta_{{ $f->id }}_lower_slider').val({{ $numeric_min_value }});
	$('#meta_{{ $f->id }}_upper_slider').val({{ $numeric_max_value }});
   @endif
   @endforeach
	
	reloadSearchResults();
}

function reloadSearchResults(){
	showSpinner();
	// go to the first page
	$('#search-results-start').val(0);
	loadSearchResults();
}

function loadSearchResults(){
	var queryString = $('#isa_search').serialize();
	//alert(queryString);
	var url = '/collection/1/search-results?'+queryString;
	$("#search-results").load(url);
	return false;
}

function drillDown(checkbox_filter){
	if($(checkbox_filter).is(':checked')){
        //alert(checkbox_filter.value);
		$('.child-of-'+checkbox_filter.value).show();
	}
    else{
        //alert('unchecked');
		$('.child-of-'+checkbox_filter.value).hide();
		$('.ch-child-of-'+checkbox_filter.value).prop('checked', false);
	}
	reloadSearchResults();
}

function showSpinner(){
	$("#search-results").html('<div class="loader"></div>');
	$('html, body').animate({
            scrollTop: $(".loader").offset().top - 200
        }, 500);	
}

function nextPage(){
	showSpinner();
	var start = $('#search-results-start').val();
	start = parseInt(start) + 10;
	$('#search-results-start').val(start);
	loadSearchResults();
	return false;
}

function previousPage(){
	showSpinner();
	var start = $('#search-results-start').val();
	start = parseInt(start) - 10;
	$('#search-results-start').val(start);
	loadSearchResults();
	return false;
}

function goToPage(page){
	showSpinner();
	var start = (page - 1) * 10;
	$('#search-results-start').val(start);
	loadSearchResults();
	return false;
}

</script>
@endpush
@section('content')
<main id="main">
<a name="search-results"></a>
@php
	// get reverse meta field values
	if($collection->require_approval){
		$rmf_values = App\ReverseMetaFieldValue::whereHas('document', function($q){
			$q->whereNotNull('approved_on');
		})->get();
	}
	else{
		$rmf_values = App\ReverseMetaFieldValue::all();
	}
	$rmfv_map = [];
	foreach($rmf_values as $rmfv){
		$mf = \App\MetaField::where('id', $rmfv->meta_field_id)->first();
		$rmfv_map[$rmfv->meta_field_id][$rmfv->meta_value][]=$rmfv->document_id;
		/*
		$tm_family = [];
		if( $mf && $mf->type == 'TaxonomyTree'){
			$mt = \App\Taxonomy::where('id',$rmfv->meta_value)->first();
			$tm_family[$mf->id][$mt->id] = $mt->createFamily();
		}
		*/
	}
	//print_r($rmfv_map);exit;

	$search_query = Request::get('isa_search_parameter');

	function getTree($children, $rmfv_map, $parent_id = null, $meta_id=null, $show_filters=false){
		 $display = '';
		 if(!$show_filters){
			$display = ' style="display:none;"';
		 } 
         if(empty($children['parent_'.$parent_id])) return;

         foreach($children['parent_'.$parent_id] as $t){
			// ignore label "ALL" 
			if(preg_match('/^ALL$/i',$t->label)) continue;
         	$checked = '';
	 	 	if(!empty(Request::get('meta_'.$meta_id)) && in_array($t->id,Request::get('meta_'.$meta_id))){
				$checked = 'checked';
				$display = '';
	 		}
        	if(!empty($children['parent_'.$t->id]) && count($children['parent_'.$t->id]) > 0){
				if(empty($t->parent_id)){
					echo "By ".$t->label."<br /><br />";
				}
				else{
					$tid = $t->id;
					// get compare with query string parameter to mark as checked
					echo '<div class="form-check child-of-'.$parent_id.'" '.$display.'>';
                	echo '<input class="ch-child-of-'.$parent_id.'" type="checkbox" value="'.$t->id.'" name="meta_'.$meta_id.'[]" onChange="drillDown(this);" '.$checked.' ><label class="form-check-label" for="flexCheckDefault">'.$t->label.' ('.(empty($rmfv_map[$meta_id][$t->id])?0:count($rmfv_map[$meta_id][$t->id])).')</label><br />';
					echo '</div>';
				}
          		getTree($children, $rmfv_map, $t->id, $meta_id);
          }
          else{
				$checked = '';
				if(!$show_filters){
					$display = ' style="display:none;"';
				}
				if(!empty(Request::get('meta_'.$meta_id)) && in_array($t->id, Request::get('meta_'.$meta_id))){
					$checked = "checked";
					$display = '';
				}
				echo '<div class="form-check ct-sub child-of-'.$parent_id.'" '.$display.'>';
				$tid = $t->id;
                echo '<input class="ch-child-of-'.$parent_id.'" type="checkbox" value="'.$t->id.'" name="meta_'.$meta_id.'[]" onChange="drillDown(this);" '.$checked.'><label class="form-check-label" for="flexCheckDefault">'.$t->label.' ('.(empty($rmfv_map[$meta_id][$t->id])?0:count($rmfv_map[$meta_id][$t->id])).')</label><br />';
				echo '</div>';
             }
         } // foreach 
}// function 
@endphp

<!-- ======= Breadcrumbs ======= -->
    <div class="row justify-content-center">
		<form name="isa_search" action="/documents/isa_document_search" method="get" id="isa_search">
		<input type="hidden" name="length" id="search-results-length" value="10" />
		<input type="hidden" name="start" id="search-results-start" value="0" />
		@csrf
        <div class="col-md-12">
            <div class="card">
				<div class="card-header card-header-primary">
                	<h6 class="card-title ">{{ __('Database') }}</h6>
            	</div>
			<div class="card-body">
			<div class="row">
                  <div class="col-12 text-right">
                  @if(Auth::user() && Auth::user()->hasPermission($collection->id, 'MAINTAINER'))
                    <a title="{{ __('Manage users of this collection') }}" href="/collection/{{ $collection->id }}/users" class="btn btn-sm btn-primary"><i class="material-icons">people</i></a>
		    	@if($collection->content_type == 'Uploaded documents')	
                    <a title="{{ __('Manage cataloging fields of this collection') }}" href="/collection/{{ $collection->id }}/meta" class="btn btn-sm btn-primary"><i class="material-icons">label</i></a>
                    <a title="{{__('New Child Collection')}}" href="/collection/{{ $collection->id }}/child-collection/new" class="btn btn-sm btn-primary"><i class="material-icons">create_new_folder</i></a>
                     @elseif($collection->content_type == 'Web resources')	
                    <a title="Manage Sites for this collection" href="/collection/{{ $collection->id }}/save_exclude_sites" class="btn btn-sm btn-primary"><i class="material-icons">insert_link</i></a>
		    @endif
		  @endif
                  @if(Auth::user() && Auth::user()->hasPermission($collection->id, 'CREATE') && $collection->content_type == 'Uploaded documents')
                    <a title="New Document" href="/collection/{{ $collection->id }}/upload" class="btn btn-sm btn-primary"><i class="material-icons">file_upload</i></a>
                    <a title="Import via URL" href="/collection/{{ $collection->id }}/url-import" class="btn btn-sm btn-primary"><i class="material-icons">link</i></a>
		  @endif
                  @if(count($collection->meta_fields)>0)
                    <a href="/collection/{{ $collection->id }}/metafilters" title="Set Filters" class="btn btn-sm btn-primary"><i class="material-icons">filter_list</i></a>
                  @endif
                  @if(Auth::user() && Auth::user()->hasPermission($collection->id, 'MAINTAINER'))
                    <!--a href="/collection/{{ $collection->id }}/export" title="Export collection to CSV" class="btn btn-sm btn-primary"><i class="material-icons">file_download</i></a-->
                    <a href="/collection/{{ $collection->id }}/exportxlsx" title="Export collection to XLSX" class="btn btn-sm btn-primary"><i class="material-icons">file_download</i></a>
				  @endif
                 
                  </div>
		        </div>
			</div>
	
			<div class="col-10">
			</div>
			<div class="col-2 text-right">
			</div>
		<div class="row text-center">
		   <div class="col-lg-12">
			<div class="float-container" style="width:100%;">
			<!--
			<label for="collection_search">{{ __('Search data') }}</label>
			-->
		    <input type="text" class="search-field" id="collection_search" name="isa_search_parameter" value="{{ $search_query }}" placeholder="Enter keywords and press SEARCH."/>
		    <input type="hidden" class="search-field" id="collection_id" name="collection_id" value="{{ $collection->id }}" />
			<input type="button" value="Search" name="isa_search" class="btn btn-sm btn-primary search" onclick="reloadSearchResults()">
			<style>
			.dataTables_filter {
			display: none;
			}
			</style>
		   </div>
		  </div>
		</div>
		
<!-- End Breadcrumbs -->

<!-- ======= Service Details Section ======= -->
@php
$tags = App\Taxonomy::orderBy('label','ASC')->get();

$children = [];
foreach($tags as $t){
  $children['parent_'.$t->parent_id][] = $t;
}
@endphp

<section id="service-details" class="service-details">
  <div class="container">
	<div class="row gy-4">
	  <div class="col-lg-3" style="margin-top:0;">
		<div class="services-list">
			<h5>Filter By <div style="float:right; cursor:pointer; border:1px solid #f05a22; padding:2px;border-radius:5px; background-color:#eee;" href="#" onclick="clearFilters();" title="Clear all filters"><i class="fa-solid fa-broom"></i></div></h5>
				@php
				foreach($filters as $f){
					if($f->type == 'TaxonomyTree'){
						echo '<a href="javascript:return false;" onclick="$(\'#filter_'.$f->id.'\').toggle()">'.$f->label.'</a>';
						echo '<div id="filter_'.$f->id.'">';
						getTree($children, $rmfv_map, $f->options, $f->id, true);
						echo "</div>\n";
					}
					else if($f->type == 'Numeric'){
						$meta_values = Request::get('meta_'.$f->id);
						echo '<a href="javascript:return false;" onclick="$(\'#filter_'.$f->id.'\').toggle()">'.$f->label.'</a>';
						echo '<div id="filter_'.$f->id.'">';
						echo '<fieldset class="filter-range">';
						echo '<div class="range-field">';
						echo '<input type="range" id="meta_'.$f->id.'_lower_slider" name="meta_'.$f->id.'[]" min="1950" max="2023" step="1" 
							value="'.(!empty($meta_values[0])?$meta_values[0]:1950).'">';
						echo '<input type="range" id="meta_'.$f->id.'_upper_slider" name="meta_'.$f->id.'[]" min="1950" max="2023" step="1" 
							value="'.(!empty($meta_values[1])?$meta_values[1]:2023).'">';
						echo '</div>';
						@endphp	
						<div class="range-wrap">
		                  <div class="range-wrap-1">
                    		<input id="start_meta_{{ $f->id }}" class="lower">
                    		<label for="start_meta_{{ $f->id }}"></label>
                  		</div>
                  		<div class="range-wrap_line">-</div>
                  		<div class="range-wrap-2">
                    		<input id="end_meta_{{ $f->id }}" class="upper">
                    		<label for="end_meta_{{ $f->id }}"></label>
                  		</div>
                		</div>
						@php
						echo '</fieldset>';
						echo '</div>';

					}
					else if($f->type == 'Select'){
						$options = explode(",",$f->options); 
						echo '<a href="javascript:return false;" onclick="$(\'#filter_'.$f->id.'\').toggle()">'.$f->label.'</a>';
						echo '<div id="filter_'.$f->id.'">';
						echo '<select name="meta_'.$f->id.'[]" class="form-control">';
						foreach($options as $select_options){
						echo '<option value="'.$select_options.'">'.$select_options.'</option>';
						}
						echo '</select>';
						echo "</div>\n";
					}
				}
				@endphp
<script>
 @foreach ($filters as $f)
 @if ($f->type == 'Numeric')
    var lowerSlider_meta_{{ $f->id }} = document.getElementById('meta_{{ $f->id }}_lower_slider');
    var upperSlider_meta_{{ $f->id }} = document.getElementById('meta_{{ $f->id }}_upper_slider');

 if(lowerSlider_meta_{{ $f->id }} && upperSlider_meta_{{ $f->id }}){
    document.querySelector('#end_meta_{{ $f->id }}').value = upperSlider.value;
    document.querySelector('#start_meta_{{ $f->id }}').value = lowerSlider.value;

    var lowerVal_meta_{{ $f->id }} = parseInt(lowerSlider_meta_{{ $f->id }}.value);
    var upperVal_meta_{{ $f->id }} = parseInt(upperSlider_meta_{{ $f->id }}.value);

    upperSlider_meta_{{ $f->id }}.oninput = function () {
      lowerVal_meta_{{ $f->id }} = parseInt(lowerSlider_meta_{{ $f->id }}.value);
      upperVal_meta_{{ $f->id }} = parseInt(upperSlider_meta_{{ $f->id }}.value);

      if (upperVal_meta_{{ $f->id }} < lowerVal_meta_{{ $f->id }} + 4) {
        lowerSlider_meta_{{ $f->id }}.value = upperVal_meta_{{ $f->id }} - 4;
        if (lowerVal_meta_{{ $f->id }} == lowerSlider_meta_{{ $f->id }}.min) {
          upperSlider_meta_{{ $f->id }}.value = 4;
        }
      }
      document.querySelector('#end_meta_{{ $f->id }}').value = this.value;
    };

    upperSlider_meta_{{ $f->id }}.onmouseup = function () {
	reloadSearchResults();
    };

    upperSlider_meta_{{ $f->id }}.ontouchend = function () {
	reloadSearchResults();
    };

    lowerSlider_meta_{{ $f->id }}.oninput = function () {
      lowerVal_meta_{{ $f->id }} = parseInt(lowerSlider_meta_{{ $f->id }}.value);
      upperVal_meta_{{ $f->id }} = parseInt(upperSlider_meta_{{ $f->id }}.value);
      if (lowerVal_meta_{{ $f->id }} > upperVal_meta_{{ $f->id }} - 4) {
        upperSlider_meta_{{ $f->id }}.value = lowerVal_meta_{{ $f->id }} + 4;
        if (upperVal_meta_{{ $f->id }} == upperSlider_meta_{{ $f->id }}.max) {
          lowerSlider_meta_{{ $f->id }}.value = parseInt(upperSlider_meta_{{ $f->id }}.max) - 4;
        }
      }
      document.querySelector('#start_meta_{{ $f->id }}').value = this.value;
    };

    lowerSlider_meta_{{ $f->id }}.onmouseup = function () {
	reloadSearchResults();
    };
    lowerSlider_meta_{{ $f->id }}.ontouchend = function () {
	reloadSearchResults();
    };
 }//if lowerSlider and upperSlider
 @endif
 @endforeach
  </script>
		<div class="form-check">
		</div>

		</div>
	  </div><!-- col-lg-3 -->
<div class="col-lg-9" id="search-results">
	<!-- search results -->
</div>

	</div><!-- row -->
  </div><!-- container -->
</section><!-- End Service Details Section -->
		</div> <!-- card -->

</div>
</form>

</div>

</main><!-- End #main -->

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
@endsection
