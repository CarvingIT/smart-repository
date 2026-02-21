@extends('layouts.app',['class' => 'off-canvas-sidebar','title'=>'Smart Repository','activePage'=>'contact','titlePage'=>'Contact Us'])
@push('js')
<script src="/js/node/jquery-ui.min.js" defer></script>
<link href="/css/node/jquery-ui.min.css" rel="stylesheet">
<link href="/css/classic/main.css" rel="stylesheet">
<link href="/css/classic/fonts.css" rel="stylesheet">
<!-- Font Awesome for icons - Load from multiple CDNs for redundancy -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.4.0/css/all.css" />
<style>
	.form-check-label{
		display:inline-block;
		width:80%;
	}
.loader {
  margin:0 auto;
  border: 16px solid #f3f3f3; /* Light grey */
  border-top: 16px solid #9c27b0; /* Theme primary color */
  border-radius: 50%;
  width: 120px;
  height: 120px;
  animation: spin 2s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
.search-clear-btn {
    position: absolute;
    top: 50%;
    right: 170px;
    transform: translateY(-50%);
    cursor: pointer;
    font-weight: bold;
    color: #888;
    font-size: 16px;
  }

/* Ensure filter links are styled properly */
.services-list a {
	display: block;
	line-height: 1;
	padding: 8px 0 8px 15px;
	border-left: 3px solid #c2cbdf;
	margin: 20px 0;
	color: #444;
	transition: 0.3s;
	text-decoration: none;
}

.services-list a.active {
	font-weight: 700;
	border-color: #9c27b0;
}

.services-list a:hover {
	border-color: #9c27b0;
}

/* Fix clear filter icon */
.services-list h5 i.fas {
	font-family: 'Font Awesome 6 Free' !important;
	font-weight: 900 !important;
	display: inline-block !important;
	font-style: normal !important;
}

/* Ensure all Font Awesome icons display properly */
.fa, .fas, .far, .fal, .fad, .fab, .fa-solid, .fa-regular, .fa-light, .fa-duotone, .fa-brands {
	font-family: 'Font Awesome 6 Free', 'Font Awesome 6 Brands' !important;
	font-weight: 900 !important;
	display: inline-block !important;
	font-style: normal !important;
	font-variant: normal !important;
	text-rendering: auto !important;
	line-height: 1 !important;
	-webkit-font-smoothing: antialiased !important;
	-moz-osx-font-smoothing: grayscale !important;
}

.fa-brands, .fab {
	font-family: 'Font Awesome 6 Brands' !important;
	font-weight: 400 !important;
}

/* Clear filter button icon */
.fa-broom:before {
	content: "\f51a" !important;
}

/* Classic Theme: collapsible taxonomy parent accordion */
.taxonomy-parent-accordion {
	margin-bottom: 2px;
}
.taxonomy-parent-label {
	user-select: none;
	transition: background 0.15s;
}
.taxonomy-parent-label:hover {
	background: #ede7f6 !important;
}

/* Search clear icon */
.fa-xmark:before, .fa-times:before {
	content: "\f00d" !important;
}

/* File icons */
.fa-file-alt:before {
	content: "\f15c" !important;
}

.fa-external-link-alt:before {
	content: "\f35d" !important;
}
</style>
<script>
$(document).ready(function() {
  // Allow Enter key to trigger search in the search box
  $('#collection_search').keydown(function(event){
    if(event.keyCode == 13) {
      event.preventDefault();
      reloadSearchResults();
      return false;
    }
  });
  
  // Prevent Enter key on other form elements
  $(window).keydown(function(event){
    if(event.keyCode == 13 && event.target.id !== 'collection_search') {
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


	$url = '/collection/{{ $collection->id }}/search-results?analyzer='.request()->get('analyzer').'&isa_search_parameter='.urlencode(request()->get('isa_search_parameter'));
@endphp
$(document).ready(function() {
	//$("#search-results").load('{{ $url }}');
	reloadSearchResults();
	@php
		$_all_mf = Session::get('meta_filters');
		$_coll_mf = !empty($_all_mf[$collection->id]) ? $_all_mf[$collection->id] : [];
		$_has_created = !empty(array_filter($_coll_mf, function($f){ return $f['field_id'] == 'created_at' && $f['operator'] !== '!='; }));
	@endphp
	@if($_has_created)
	// show Record Created section open if a filter is active
	$('#filter_record_created').show();
	@endif
});

function clearFilters(){
	// Use AJAX to clear all filters without page refresh
	$.ajax({
		url: '/collection/{{ $collection->id }}/ajax-clear-all-filters',
		method: 'POST',
		data: {
			_token: '{{ csrf_token() }}'
		},
		success: function(response) {
			// Clear the file type filter dropdown
			$('#file_type_filter').val('');
			// Uncheck all taxonomy checkboxes
			$('input[type="checkbox"][name^="meta_"]').prop('checked', false);
			// Reset all numeric range sliders
			@foreach ($filters as $f)
			@if ($f->type == 'Numeric')
			@php
				$extra_attributes = empty($f->extra_attributes)? null : json_decode($f->extra_attributes);
				$numeric_min_value = @$extra_attributes->numeric_min_value;
				$numeric_max_value = @$extra_attributes->numeric_max_value;
			@endphp
			$('#meta_{{ $f->id }}_lower_slider').val('{{ $numeric_min_value }}');
			$('#meta_{{ $f->id }}_upper_slider').val('{{ $numeric_max_value }}');
			$('#start_meta_{{ $f->id }}').val('{{ $numeric_min_value }}');
			$('#end_meta_{{ $f->id }}').val('{{ $numeric_max_value }}');
			@endif
			@endforeach
			// Clear record created filter UI
			$('#record_created_value').val('');
			$('#created_filter_tags_container').html('');
			$('#date-facets-container').html('');
			// Reload search results
			reloadSearchResults();
		},
		error: function(xhr, status, error) {
			console.error('Error clearing filters:', error);
			alert('Failed to clear filters. Please try again.');
		}
	});
}

function reloadSearchResults(callback){
	showSpinner();
	// go to the first page
	$('#search-results-start').val(0);
	loadSearchResults(callback);
}

function loadSearchResults(callback){
	var queryString = $('#isa_search').serialize();
	var url = '/collection/{{ $collection->id }}/search-results?'+queryString;
	$("#search-results").load(url, function(){
		updateFilterTagCount();
		loadDateFacets();
		if(typeof callback === 'function') callback();
	});
	return false;
}

function updateFilterTagCount(){
	var count = $('#filtered-results-count').text().trim();
	if(count !== ''){
		$('.filter-tag-count').html('<span style="font-size:11px; font-weight:600; color:#666;">' + count + ' results<\/span>');
	}
}

function renderCreatedFilterTag(operator, value, filterId){
	var opLabel = operator === '>=' ? '{{ __('On or after') }}' : (operator === '<=' ? '{{ __('On or before') }}' : '{{ __('On') }}');
	var parts = value.split('-');
	var displayDate = parts.length === 3 ? parts[2]+'-'+parts[1]+'-'+parts[0] : value;
	return '<div class="created-filter-tag" data-filter-id="' + filterId + '" data-operator="' + operator + '" '
		+ 'style="background:#f0e6f6; border:1px solid #9c27b0; border-radius:6px; padding:6px 8px; margin-top:8px;">'
		+ '<div style="display:flex; justify-content:space-between; align-items:center; gap:6px;">'
		+ '<div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">'
		+ '<span style="font-size:12px; font-weight:700; color:#222;">' + opLabel + ' ' + displayDate + '<\/span>'
		+ '<span class="filter-tag-count" style="font-size:11px; font-weight:600; color:#666;"><\/span>'
		+ '<\/div>'
		+ '<a href="javascript:void(0);" onclick="removeCreatedFilter(this, \'' + filterId + '\'); return false;" '
		+ 'style="display:inline-flex; align-items:center; gap:2px; color:#c62828; text-decoration:none; font-size:11px; font-weight:700; '
		+ 'border:1px solid #c62828; border-radius:4px; padding:2px 6px; white-space:nowrap; flex-shrink:0; background:#fff3f3;" '
		+ 'title="{{ __('Remove') }}"><i class="material-icons" style="font-size:12px;">close<\/i> clear<\/a>'
		+ '<\/div>'
		+ '<\/div>';
}

function removeCreatedFilter(el, filterId){
	$.ajax({
		url: '/collection/{{ $collection->id }}/ajax-remove-filter/' + filterId,
		method: 'POST',
		data: { _token: '{{ csrf_token() }}' },
		success: function(){
			$(el).closest('.created-filter-tag').remove();
			$('#date-facets-container').html('');
			reloadSearchResults();
		},
		error: function(){
			window.location.href = '/collection/{{ $collection->id }}/removefilter/' + filterId;
		}
	});
}

function loadDateFacets(){
	// Only show date facets when a created_at range/date filter is active
	if($('#created_filter_tags_container .created-filter-tag').length === 0){
		$('#date-facets-container').html('');
		return;
	}
	// If the active filter is an exact "On" (=) date, date facets would duplicate the same box — skip them
	if($('#created_filter_tags_container .created-filter-tag[data-operator="="]').length > 0){
		$('#date-facets-container').html('');
		return;
	}
	var queryString = $('#isa_search').serialize();
	$.getJSON('/collection/{{ $collection->id }}/date-facets?' + queryString, function(data){
		var html = '';
		if(!data || data.length === 0){
			$('#date-facets-container').html('');
			return;
		}
		$.each(data, function(i, item){
			html += '<div class="date-facet-chip" data-date="'+item.raw_date+'"'
				+ ' style="background:#f0e6f6; border:1px solid #9c27b0; border-radius:6px;'
				+ ' padding:6px 8px; margin-top:6px;">'
				+ '<div style="display:flex; justify-content:space-between; align-items:center; gap:6px;">'
				+ '<div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">'
				+ '<span style="font-size:12px; font-weight:700; color:#222;">'+ item.formatted +'<\/span>'
				+ '<span style="font-size:11px; font-weight:600; color:#666;">'+ item.count +' documents<\/span>'
				+ '<\/div>'
				+ '<a href="javascript:void(0);" onclick="excludeDate(this, \''+item.raw_date+'\'); return false;"'
				+ ' style="display:inline-flex; align-items:center; gap:2px; color:#c62828; text-decoration:none; font-size:11px; font-weight:700;'
				+ ' border:1px solid #c62828; border-radius:4px; padding:2px 6px; white-space:nowrap; flex-shrink:0; background:#fff3f3;"'
				+ ' title="{{ __('Remove this date') }}">'
				+ '<i class="material-icons" style="font-size:12px;">close<\/i> clear'
				+ '<\/a><\/div><\/div>';
		});
		$('#date-facets-container').html(html);
	});
}

function excludeDate(el, date){
	$.ajax({
		url: '/collection/{{ $collection->id }}/ajax-exclude-date',
		method: 'POST',
		data: { _token: '{{ csrf_token() }}', date: date },
		success: function(){
			$(el).closest('.date-facet-chip').fadeOut(200, function(){ $(this).remove(); });
			reloadSearchResults();
		},
		error: function(){
			alert('{{ __('Failed to exclude date. Please try again.') }}');
		}
	});
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

// Classic Theme: toggle collapsible parent taxonomy accordion
function classicToggleTaxParent(uid) {
	var container = document.getElementById(uid);
	var arrow = document.getElementById(uid + '_arrow');
	if (!container) return;
	var isHidden = (container.style.display === 'none');
	container.style.display = isHidden ? '' : 'none';
	if (arrow) arrow.innerHTML = isHidden ? '&#9660;' : '&#9654;';
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

	function getTree($children, $rmfv_map, $parent_id = null, $meta_id=null, $show_filters=false, $depth=0){
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
				if($depth == 0){
					// --- Classic Theme: render first-level parents as collapsible accordion headers ---
					// Check if any direct child is currently selected (to keep accordion open)
					$anySelected = false;
					$req_meta = Request::get('meta_'.$meta_id);
					if(!empty($req_meta)){
						foreach($children['parent_'.$t->id] as $child){
							if(in_array($child->id, $req_meta)){ $anySelected = true; break; }
						}
					}
					$childContainerDisplay = $anySelected ? '' : 'display:none;';
					$arrowChar = $anySelected ? '&#9660;' : '&#9654;';
					$uid = 'tax_acc_'.$meta_id.'_'.$t->id;
					echo '<div class="taxonomy-parent-accordion" style="margin-top:4px;">';
					echo '<div class="taxonomy-parent-label" onclick="classicToggleTaxParent(\''.$uid.'\')" style="cursor:pointer; display:flex; align-items:center; gap:5px; padding:6px 8px; background:#f5f0fa; border-radius:5px; font-weight:600; font-size:13px; color:#444; margin-bottom:2px;">';
					echo '<span id="'.$uid.'_arrow" style="font-size:10px; min-width:14px; text-align:center; color:#9c27b0;">'.$arrowChar.'</span>';
					echo '<span>'.$t->label.'</span>';
					echo '</div>';
					echo '<div id="'.$uid.'" style="'.$childContainerDisplay.' margin-left:14px; margin-top:2px; padding-bottom:4px; border-left:2px solid #e8d5f5; padding-left:8px;">';
					getTree($children, $rmfv_map, $t->id, $meta_id, true, $depth+1);
					echo '</div>';
					echo '</div>';
				}
				else{
					// Original behavior for deeper levels: checkbox with drillDown
					$tid = $t->id;
					echo '<div class="form-check child-of-'.$parent_id.'" '.$display.'>';
                	echo '<input class="ch-child-of-'.$parent_id.'" type="checkbox" value="'.$t->id.'" name="meta_'.$meta_id.'[]" onChange="drillDown(this);" '.$checked.' ><label class="form-check-label" for="flexCheckDefault">'.$t->label.' ('.(empty($rmfv_map[$meta_id][$t->id])?0:count($rmfv_map[$meta_id][$t->id])).')</label><br />';
					echo '</div>';
					getTree($children, $rmfv_map, $t->id, $meta_id, false, $depth+1);
				}
          }
          else{
				$checked = '';
				if(!$show_filters && $depth > 0){
					$display = ' style="display:none;"';
				}
				if(!empty(Request::get('meta_'.$meta_id)) && in_array($t->id, Request::get('meta_'.$meta_id))){
					$checked = "checked";
					$display = '';
				}
				echo '<div class="form-check child-of-'.$parent_id.'" '.$display.'>';
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
                	<h4 class="card-title" style="color: white; font-weight: 600; margin: 0;">
                		@if(env('ENABLE_COLLECTION_LIST') == 1)<a href="/collections" style="color: white; text-decoration: none;">{{ __('Collections') }}</a> ::@endif {{ $collection->name }}
                	</h4>
            	</div>
			<div class="card-body">
			<div class="row">
                  <div class="col-12">
                  @if(Auth::user() && Auth::user()->hasPermission($collection->id, 'MAINTAINER'))
                    <a title="{{ __('Manage users of this collection') }}" href="/collection/{{ $collection->id }}/users" class="btn btn-sm btn-primary"><i class="material-icons">people</i></a>
		    	@if($collection->content_type == 'Uploaded documents')	
                    <a title="{{ __('Manage meta data fields of this collection') }}" href="/collection/{{ $collection->id }}/meta" class="btn btn-sm btn-primary"><i class="material-icons">label</i></a>
                    <a title="{{ __('Settings') }}" href="/collection/{{ $collection->id }}/settings" class="btn btn-sm btn-primary"><i class="material-icons">settings</i></a>
                    @if(env('ENABLE_CHILD_COLLECTION_LINK') == 1)
                    <a title="{{__('New Child Collection')}}" href="/collection/{{ $collection->id }}/child-collection/new" class="btn btn-sm btn-primary"><i class="material-icons">create_new_folder</i></a>
                    @endif
		    	@elseif($collection->content_type == 'Web resources')	
                    <a title="Manage Sites for this collection" href="/collection/{{ $collection->id }}/save_exclude_sites" class="btn btn-sm btn-primary"><i class="material-icons">insert_link</i></a>
		    	@endif
		  	  @endif
                  @if(Auth::user() && Auth::user()->hasPermission($collection->id, 'CREATE') && $collection->content_type == 'Uploaded documents')
                    <a title="{{ __('New Document') }}" href="/collection/{{ $collection->id }}/upload" class="btn btn-sm btn-primary"><i class="material-icons">file_upload</i></a>
                    @if(env('ENABLE_IMPORT_LINK') == 1)
                    <a title="Import via URL" href="/collection/{{ $collection->id }}/url-import" class="btn btn-sm btn-primary"><i class="material-icons">link</i></a>
                    @endif
		  		  @endif
                  @if(count($collection->meta_fields)>0 && env('ENABLE_FILTER_LINK') == 1)
                    <a href="/collection/{{ $collection->id }}/metafilters" title="{{ __('Set Filters') }}" class="btn btn-sm btn-primary"><i class="material-icons">filter_list</i></a>
                  @endif
                  @if(Auth::user() && Auth::user()->hasPermission($collection->id, 'MAINTAINER'))
                    <a href="/collection/{{ $collection->id }}/exportxlsx" title="{{ __('Export up to 1000 records to XLSX') }}" class="btn btn-sm btn-primary"><i class="material-icons">file_download</i></a>
				  @endif
                  </div>
		        </div>
			<div class="row">
				<div class="col-lg-12">
					<div class="search-container-classic">
						<div class="search-box-classic">
							<input type="text" class="search-field" id="collection_search" name="isa_search_parameter" 
								value="{{ $search_query }}" 
								placeholder="Search Data e.g. Laws, Publications and Technical Standards"/>
							<input type="hidden" class="search-field" id="collection_id" name="collection_id" value="{{ $collection->id }}" />
							<div class="buttonSide-classic">
								<div class="tooltip-classic">
								<span class="closeIcon-classic" onclick="clearSearchInput()" style="font-family: 'Font Awesome 6 Free', FontAwesome; font-weight: 900;">&#xf00d;</span>
									<span class="tooltiptext-classic">Clear</span>
								</div>
								<div class="line-classic">
									<p>line</p>
								</div>
								<button type="button" class="btn btn-primary search-btn-classic" onclick="reloadSearchResults()">Search</button>
							</div>
						</div>
					</div>
				</div>
			</div>

			<style>
			.dataTables_filter {
				display: none;
			}
			
			.search-container-classic {
				text-align: center;
				margin: 15px 0;
				width: 100%;
			}
			
			.search-box-classic {
				display: flex;
				justify-content: space-between;
				align-items: center;
				width: 100%;
				border-radius: 10px;
				background: #fff;
				box-shadow: rgba(0, 0, 0, 0.05) 0px 6px 24px 0px, rgba(0, 0, 0, 0.08) 0px 0px 0px 1px;
				padding-right: 5px;
			}
			
			.search-box-classic input.search-field {
				border: none;
				border-radius: 10px;
				background: #fff;
				width: 80% !important;
				padding: 12px 15px;
				font-size: 14px;
				outline: none;
			}
			
			.search-box-classic input.search-field:focus {
				outline: none;
			}
			
			.buttonSide-classic {
				gap: 10px;
				display: flex;
				align-items: center;
				padding: 0 5px;
			}
			
			.closeIcon-classic {
				color: black;
				cursor: pointer;
				font-size: 18px !important;
			}
			
			.closeIcon-classic:hover {
				color: #9c27b0;
			}
			
			.line-classic {
				width: 0.5px;
				height: 30px;
				background-color: #adb5bd;
			}
			
			.line-classic p {
				opacity: 0;
			}
			
			.tooltip-classic {
				position: relative;
				margin-bottom: 7px;
				z-index: 0;
				display: inline-block;
				cursor: pointer;
				border-bottom: none;
			}
			
			.tooltip-classic .tooltiptext-classic {
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
			
			.tooltip-classic .tooltiptext-classic::after {
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
			
			.tooltip-classic:hover .tooltiptext-classic {
				visibility: visible;
			}
			
			.search-btn-classic {
				padding: 0.40625rem 1.25rem !important;
				background-color: #9c27b0;
				border: none;
				color: white;
				cursor: pointer;
				transition: background-color 0.3s ease;
				white-space: nowrap;
			}
			
			.search-btn-classic:hover {
				background-color: #7b1fa2;
			}
			</style>
			
			<script>
			function clearSearchInput() {
				const searchInput = document.getElementById('collection_search');
				searchInput.value = '';
				reloadSearchResults(); // Clear the results and show all documents
			}
			</script>
		
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
		<div class="services-list" style="padding: 10px 5px; border: 1px solid #d3dff3; margin-bottom: 20px; background-color: #fff;">
			<h5 style="margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #e8e8e8;">Filter By <div style="float:right; cursor:pointer; border:1px solid #9c27b0; padding:4px 8px;border-radius:5px; background-color:#eee; font-size: 14px;" onclick="clearFilters();" title="Clear all filters"><span style="font-family: 'Font Awesome 6 Free', FontAwesome; font-weight: 900;">&#xf51a;</span> Clear</div></h5>
				<!-- File Type Filter (shown first) -->
				<a href="javascript:return false;" onclick="$('#filter_file_type').toggle()">{{ __('File Type') }}</a>
				<div id="filter_file_type" style="display:none; margin-left: 15px; margin-top: 5px;">
					<select name="extension_filter" id="file_type_filter" class="form-control" onchange="applyFileTypeFilter()" style="border: 2px solid #9c27b0; padding: 5px 10px; font-size: 13px; border-radius: 5px; width: auto; max-width: 200px;">
						<option value="">{{ __('All File Types') }}</option>
					</select>
				</div>
				@php
				// Taxonomy filters (shown below File Type)
				$taxonomy_filters = array_filter($filters, function($f){ return $f->type == 'TaxonomyTree'; });
				// Also include any TaxonomyTree meta fields that don't have is_filter set
				$shown_ids = array_map(function($f){ return $f->id; }, $taxonomy_filters);
				foreach($collection->meta_fields as $mf){
					if($mf->type == 'TaxonomyTree' && !in_array($mf->id, $shown_ids)){
						$taxonomy_filters[] = $mf;
					}
				}
				foreach($taxonomy_filters as $f){
					// Keep outer section open if any child is already checked (active filter)
					$_taxOpenByDefault = !empty(Request::get('meta_'.$f->id));
					$_taxOuterDisplay = $_taxOpenByDefault ? '' : 'display:none;';
					echo '<a href="javascript:return false;" onclick="$(\'#filter_'.$f->id.'\').toggle()" style="margin-top:8px; display:block;">'.$f->label.'</a>';
					echo '<div id="filter_'.$f->id.'" style="'.$_taxOuterDisplay.'">';
					getTree($children, $rmfv_map, $f->options, $f->id, true);
					echo "</div>\n";
				}
				// Other filters (Numeric, Select)
				foreach($filters as $f){
					if($f->type == 'TaxonomyTree') continue; // already shown above
					else if($f->type == 'Numeric'){
              				$extra_attributes = empty($f->extra_attributes)? null : json_decode($f->extra_attributes);
              				$numeric_min_value = @$extra_attributes->numeric_min_value;
              				$numeric_max_value = @$extra_attributes->numeric_max_value;

						$meta_values = Request::get('meta_'.$f->id);
						echo '<a href="javascript:return false;" onclick="$(\'#filter_'.$f->id.'\').toggle()">'.$f->label.'</a>';
						echo '<div id="filter_'.$f->id.'">';
						echo '<fieldset class="filter-range">';
						echo '<div class="range-field">';
						echo '<input type="range" id="meta_'.$f->id.'_lower_slider" name="meta_'.$f->id.'[]" min="'.$numeric_min_value.'" max="'.$numeric_max_value.'" step="1" 
							value="'.(!empty($meta_values[0])?$meta_values[0]:$numeric_min_value).'">';
						echo '<input type="range" id="meta_'.$f->id.'_upper_slider" name="meta_'.$f->id.'[]" min="'.$numeric_min_value.'" max="'.$numeric_max_value.'" step="1" 
							value="'.(!empty($meta_values[1])?$meta_values[1]:$numeric_max_value).'">';
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

				<!-- Record Created Filter (shown below Taxonomy) -->
				<a href="javascript:return false;" onclick="$('#filter_record_created').toggle()" style="margin-top:8px; display:block;">{{ __('Record Created') }}</a>
				<div id="filter_record_created" style="display:none; margin-left: 5px; margin-top: 5px;">
					<div style="margin-bottom:5px;">
						<select id="record_created_operator" class="form-control" style="font-size:13px; padding:4px 6px; margin-bottom:5px;">
							<option value=">=">{{ __('On or after') }}</option>
							<option value="<=">{{ __('On or before') }}</option>
							<option value="=">{{ __('On') }}</option>
						</select>
						<input type="date" id="record_created_value" class="form-control" style="font-size:13px; padding:4px 6px; margin-bottom:5px;" />
						<button type="button" class="btn btn-sm btn-primary" onclick="applyRecordCreatedFilter()" style="width:100%; font-size:13px;">{{ __('Apply') }}</button>
					</div>
					@php
						$all_session_filters = Session::get('meta_filters');
						$session_filters_for_coll = !empty($all_session_filters[$collection->id]) ? $all_session_filters[$collection->id] : [];
					// Only show the main date range/exact filters (not the != date exclusions)
					$created_at_filters = array_filter($session_filters_for_coll, function($f){
						return $f['field_id'] == 'created_at' && $f['operator'] !== '!=';
					});
					@endphp
				<div id="created_filter_tags_container">
				@foreach($created_at_filters as $cf)
				<div class="created-filter-tag" data-filter-id="{{ $cf['filter_id'] }}" data-operator="{{ $cf['operator'] }}"
						style="background:#f0e6f6; border:1px solid #9c27b0; border-radius:6px; padding:6px 8px; margin-top:8px;">
						<div style="display:flex; justify-content:space-between; align-items:center; gap:6px;">
							<div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
								<span style="font-size:12px; font-weight:700; color:#222;">
									{{ $cf['operator'] == '>=' ? __('On or after') : ($cf['operator'] == '<=' ? __('On or before') : __('On')) }}
									{{ \Carbon\Carbon::parse($cf['value'])->format('d-m-Y') }}
								</span>
								<span class="filter-tag-count" style="font-size:11px; font-weight:600; color:#666;"></span>
							</div>
							<a href="javascript:void(0);"
								onclick="removeCreatedFilter(this, '{{ $cf['filter_id'] }}'); return false;"
								style="display:inline-flex; align-items:center; gap:2px; color:#c62828; text-decoration:none; font-size:11px; font-weight:700; border:1px solid #c62828; border-radius:4px; padding:2px 6px; white-space:nowrap; flex-shrink:0; background:#fff3f3;"
								title="{{ __('Remove') }}">
								<i class="material-icons" style="font-size:12px;">close</i> clear
							</a>
						</div>
					</div>
				@endforeach
				</div>			<div id="date-facets-container" style="margin-top:4px;"></div>				</div>

<script>
function applyRecordCreatedFilter(){
	var operator = $('#record_created_operator').val();
	var value = $('#record_created_value').val();
	if(!value){ alert('{{ __("Please select a date") }}'); return; }
	$.ajax({
		url: '/collection/{{ $collection->id }}/ajax-add-created-filter',
		method: 'POST',
		data: {
			_token: '{{ csrf_token() }}',
			collection_id: '{{ $collection->id }}',
			meta_field: 'created_at',
			operator: operator,
			meta_value: value
		},
		success: function(response){
			if(response && response.success){
				var filterId = response.filter_id;
				// Replace existing tag and add new one without page reload
				$('#created_filter_tags_container').html(renderCreatedFilterTag(operator, value, filterId));
				// Reload results without page reload, then update count in tag
				reloadSearchResults();
			}
		},
		error: function(xhr){
			console.error('Failed to apply date filter', xhr);
			alert('{{ __("Failed to apply filter. Please try again.") }}');
		}
	});
}
</script>

 @foreach ($filters as $f)
 @if ($f->type == 'Numeric')
    var lowerSlider_meta_{{ $f->id }} = document.getElementById('meta_{{ $f->id }}_lower_slider');
    var upperSlider_meta_{{ $f->id }} = document.getElementById('meta_{{ $f->id }}_upper_slider');

 if(lowerSlider_meta_{{ $f->id }} && upperSlider_meta_{{ $f->id }}){
    //document.querySelector('#end_meta_{{ $f->id }}').value = upperSlider.value;
    //document.querySelector('#start_meta_{{ $f->id }}').value = lowerSlider.value;
    document.querySelector('#end_meta_{{ $f->id }}').value = upperSlider_meta_{{ $f->id }}.value;
    document.querySelector('#start_meta_{{ $f->id }}').value = lowerSlider_meta_{{ $f->id }}.value;

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
<div class="col-lg-9" id="search-results" style="padding-left:3%;">
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
	// Load file types for the dropdown
	$(document).ready(function() {
		$.ajax({
			url: '/collection/{{$collection->id}}/extensions',
			method: 'GET',
			success: function(response) {
				var fileTypeSelect = $('#file_type_filter');
				response.extensions.forEach(function(type) {
					// Create user-friendly label
					var label = getFriendlyLabel(type);
					fileTypeSelect.append('<option value="' + type + '">' + label + '</option>');
				});
				
				// Pre-select if filter is active
				@php
					$extension_filter = Session::get('extension_filter');
					$selected_type = !empty($extension_filter[$collection->id]) ? $extension_filter[$collection->id] : '';
				@endphp
				@if(!empty($selected_type))
					fileTypeSelect.val('{{ $selected_type }}');
					$('#filter_file_type').show(); // Show the filter if a type is selected
				@endif
			}
		});
	});
	
	// Helper function to create friendly labels
	function getFriendlyLabel(mimeType) {
		var friendlyNames = {
			'application/pdf': 'PDF',
			'application/msword': 'DOC',
			'application/vnd.openxmlformats-officedocument.wordprocessingml.document': 'DOCX',
			'application/vnd.ms-excel': 'XLS',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': 'XLSX',
			'application/vnd.ms-powerpoint': 'PPT',
			'application/vnd.openxmlformats-officedocument.presentationml.presentation': 'PPTX',
			'image/jpeg': 'JPEG',
			'image/png': 'PNG',
			'image/gif': 'GIF',
			'text/plain': 'TXT',
			'text/csv': 'CSV',
			'application/zip': 'ZIP',
			'video/mp4': 'MP4',
			'audio/mpeg': 'MP3'
		};
		
		var shortName = friendlyNames[mimeType] || mimeType.split('/')[1].toUpperCase();
		return shortName + ' (' + mimeType + ')';
	}
	
	// Apply file type filter
	function applyFileTypeFilter() {
		var selectedType = $('#file_type_filter').val();
		$.ajax({
			url: '/collection/{{$collection->id}}/ajax-set-extension-filter',
			method: 'POST',
			data: {
				_token: '{{ csrf_token() }}',
				collection_id: '{{$collection->id}}',
				extension_filter: selectedType
			},
			success: function(response) {
				// Reload search results without page refresh
				reloadSearchResults();
			},
			error: function(xhr, status, error) {
				console.error('Error applying filter:', error);
				alert('Failed to apply filter. Please try again.');
			}
		});
	}

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

<style>
/* Remove orange footer line */
footer {
	border-top: none !important;
	background-color: transparent !important;
}
.wrapper {
	border-bottom: none !important;
}
main#main {
	margin-bottom: 0 !important;
	padding-bottom: 20px;
}
</style>

@endsection