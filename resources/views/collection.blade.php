@extends('layouts.app',['class'=> 'off-canvas-sidebar'])

@section('content')
@push('js')
<style>
a.toggle-highlights{
    cursor:pointer;
}
td.highlights p{
    white-space:normal;
}
.rotate-90{
    transform: rotate(90deg);
}
@media (min-width: 2000px) {
    .container.wide-screen-container {
        max-width: 95% !important;
        width: 95% !important;
    }
}

</style>
<script src="/js/node/jquery.dataTables.min.js"></script>
<script src="/js/node/jquery-ui.min.js" defer></script>
<script type="text/javascript" src="/js/transliteration-input.bundle.js"></script>
<link href="/css/node/jquery-ui.min.css" rel="stylesheet">
<link href="/css/node/select2.min.css" rel="stylesheet" />
<link href="/css/select2totree.css" rel="stylesheet" />
<link href="/css/tile-view.css" rel="stylesheet" />
<link href="/css/node/fixedColumns.dataTables.min.css" rel="stylesheet" />
<style>
    .dataTables_paginate {
        float: right !important;
        text-align: right;
    }
    .dataTables_wrapper .row {
        align-items: center;
    }
    
    .dataTables_bottom_controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        margin-top: 10px;
    }
</style>
<script src="/js/node/select2.min.js"></script>
<script src="/js/select2totree.js"></script>
<script src="/js/node/dataTables.fixedColumns.min.js"></script>
@php
$column_config = json_decode($collection->column_config);
list($hide_type, $hide_title, $hide_approval_status, $hide_size, $hide_creation_time) = array(false, false, true, false, false);
if(!empty($collection->column_config)){
	if(@$column_config->type != 1) $hide_type = true;
	if(@$column_config->title != 1) $hide_title = true;
	if(@$column_config->display_approval_status === 1) $hide_approval_status = false;
	if(@$column_config->size != 1) $hide_size = true;
	if(@$column_config->creation_time != 1) $hide_creation_time = true;
}
// search scope and fuzzy search
$fuzzy = Session::get('fuzzy');
$full_text_scope = Session::get('full_text_scope');
$old_search = Session::get('search_query');
@endphp
<script>
var deldialog;
$(document).ready(function() {
    oTable = $('#documents').DataTable({
    "drawCallback": function(settings) {
        // Update tiles when DataTable redraws (pagination, search, etc.)
        if (currentViewMode === 'tile') {
            console.log('DataTable drawCallback - Tile view active, page:', settings._iDisplayStart / settings._iDisplayLength + 1);
            var api = this.api();
            var data = api.rows({page: 'current'}).data().toArray();
            renderTiles(data, false);
            
            // Move DataTable controls to tile view layout
            // Use longer timeout to ensure DataTables has finished rendering controls
            setTimeout(function() {
                moveTileControls();
            }, 50);
        }
    },
    "columnDefs": [
		{ "targets":[0], "className":'text-center', "sortable":false, @if($hide_type)"visible":false @endif},
		{ "targets":[1], "className":'text-left',"sortable":false, @if($hide_title)"visible":false @endif},
		@php
			$i = 2;
			$column_config_meta_fields = empty($column_config->meta_fields)?[]:$column_config->meta_fields;
			foreach($column_config_meta_fields as $m_id){
				$m = \App\MetaField::find($m_id);
				$visible = 'false';
				if(in_array(@$m->id, $column_config_meta_fields)){
				$visible = 'true';
			    }
			    echo '{ "targets":['.$i.'], "className":"text-right", "sortable":false, "visible":'.$visible.' },';
			    $i++;
		    }
		echo '{ "targets":['.$i++.'], "sortable":false, "className":"text-left"'. (($hide_approval_status)?',"visible":false':'').'},';
		echo '{ "targets":['.$i++.'], "sortable":false, "className":"text-left"'.(($hide_size)?',"visible":false':"").'},';
		echo '{ "targets":['.$i++.'], "sortable":false, "className":"text-left"'.(($hide_creation_time)?',"visible":false':"").'},';
		@endphp	
		{ "targets":[{{ $i }}], "visible":true, "sortable":false, "className":'td-actions text-right dt-nowrap'},
     ],
    "processing":true,
    "processing":true,
    "dom":'lrt<"dataTables_bottom_controls"ip>',
    @if(!empty($column_config->fixed_columns_left) || !empty($column_config->fixed_columns_right))
    "scrollX": true,
    "fixedColumns": {
        "start": {{ !empty($column_config->fixed_columns_left) ? $column_config->fixed_columns_left : 0 }},
        "end": {{ !empty($column_config->fixed_columns_right) ? $column_config->fixed_columns_right : 0 }}
    },
    @endif
    "order": [], // initial ordering disabled. Good for sorting by relevance in ES.
    "serverSide":true,
    "ajax":'/collection/{{$collection->id}}/search',
    //"lengthMenu":[10, 25, 50, 60, 100, 120],
    "language": 
	{          
	"processing": "<img src='/i/processing.gif'>",
	"lengthMenu": "{{ __('Show _MENU_ entries') }}",
	"zeroRecords": "{{ __('No matching records found') }}",
	"info": "{{ __('Showing _START_ to _END_ of _TOTAL_ entries') }}",
	"infoEmpty": "{{ __('Showing 0 to 0 of 0 entries') }}",
	"infoFiltered": "{{ __('(filtered from _MAX_ total entries)') }}",
	"search": "{{ __('Search:') }}",
	"paginate": {
		"first": "{{ __('First') }}",
		"last": "{{ __('Last') }}",
		"next": "{{ __('Next') }}",
		"previous": "{{ __('Previous') }}"
	}
	},
    "columns":[
       {data:"type",
          render:{
            '_':'display',
            'sort':'filetype'
          }
       },
       {data:"title", render: function(data, type, row){
            return data;
       }},
		@foreach($column_config_meta_fields as $m_id)
			@php
			$m = \App\MetaField::find($m_id);
			@endphp
		{data:"meta_{{@$m->id}}"},
		@endforeach
       {data:"approval_status"},
       {data:"size",
           render:{
             '_': 'display',
             'sort': 'bytes'
            }
        },
        {data:"updated_at",
            render:{
               '_':'display',
              'sort': 'updated_date'
            }
        },
        {data:"actions"},
    ],
    });

$('#doc-sort').on('click', function() {
        // Check if the native showPicker method is available and call it
        const selectElement = $('#doc-sort-select')[0];
        if (selectElement && typeof selectElement.showPicker === 'function') {
            selectElement.showPicker();
        } else {
            console.error('showPicker() is not supported in this browser or environment.');
        }
    })

oTable.on('click', 'tbody td .toggle-highlights', function(e){
    let tr = e.target.closest('tr');
    let row = oTable.row(tr);
 
    if (row.child.isShown()) {
        // This row is already open - close it
        row.child.hide();
    }
    else {
        // Open this row
        row.child(formatHighlights(row.data()), 'highlights').show();
    }
});
    
// Initialize view mode
initializeViewMode();

} );

function formatHighlights(d){
 return (
        '<p>' +
        d.highlights.text_content +
        '</p>'
    );
}

function showDeleteDialog(document_id){
	str = randomString(6);
	$('#text_captcha').text(str);
	$('#hidden_captcha').val(str);
	$('#delete_doc_id').val(document_id);
        deldialog = $( "#deletedialog" ).dialog({
		title: 'Are you sure ?',
		resizable: true
        });
}

function showSubCollectionDeleteDialog(collection_id){
        str = randomString(6);
        $('#text_subcollection_captcha').text(str);
        $('#hidden_subcollection_captcha').val(str);
        $('#delete_subcollection_id').val(collection_id);
        deldialog = $( "#deletesubcollectiondialog" ).dialog({
                title: 'Are you sure ?',
                resizable: true
        });
}

function randomString(length) {
   var result           = '';
   var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
   var charactersLength = characters.length;
   for ( var i = 0; i < length; i++ ) {
      result += characters.charAt(Math.floor(Math.random() * charactersLength));
   }
   return result;
}

function setDocSort(sort_by){
    $.ajax({
       url: '/collection/{{ $collection->id }}/set-doc-sort?sort_by='+sort_by,
       method: 'GET',
       dataType: "json",
       success: function(data) {
            search_val = $('#collection_search').val();
            oTable.search(search_val).draw();
       }
   });
}
</script>

<script src="/js/node/jquery.daterangepicker.min.js"></script>
<link rel="stylesheet" href="/css/node/jquery.daterangepicker.min.css"/>
<script src="{{ asset("js/favorites.js") }}"></script>
<style>
#doc-sort-wrapper {
  position: relative;
  display: inline-block;
}

#doc-sort {
  color: white;
  border: none;
  cursor: pointer;
  border-radius: 4px;
}

#doc-sort-select {
  /* Hide the native select menu's default appearance while keeping it interactive */
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  position: absolute;
  top: 0;
  right: 1rem;
  height: 100%;
  opacity: 0;
}
</style>
@endpush
        <div id="deletesubcollectiondialog" style="display:none;">
                <form name="deletesubcollection" method="post" action="/collection/subcollection/delete">
                @csrf
                <p>Enter <span id="text_subcollection_captcha"></span> to delete</p>
                <input type="text" name="delete_subcollection_captcha" value="" />
                <input type="hidden" id="hidden_subcollection_captcha" name="hidden_subcollection_captcha" value="" />
                <input type="hidden" id="delete_subcollection_id" name="collection_id" value="" />
                <button class="btn btn-danger" type="submit" value="delete">Delete</button>
                </form>
        </div>

	    <div id="deletedialog" style="display:none;">
		<form name="deletedoc" method="post" action="/document/delete">
		@csrf
		<p>Enter <span id="text_captcha"></span> to delete</p>
		<input type="text" name="delete_captcha" value="" />
		<input type="hidden" id="hidden_captcha" name="hidden_captcha" value="" />
		<input type="hidden" id="delete_doc_id" name="document_id" value="" />
		<button class="btn btn-danger" type="submit" value="delete">Delete</button>
		</form>
	    </div>

		<!-- Save Search Modal -->
		@if(Auth::check())
		<div id="save-search-dialog" style="display:none;">
			<form id="save-search-form">
				@csrf
				<input type="hidden" name="collection_id" value="{{ $collection->id }}" />
				<div class="form-group">
					<label for="search_name">{{ __('Name for this saved search') }}</label>
					<input type="text" class="form-control" id="search_name" name="name" required placeholder="{{ __('e.g., Recent PDFs, 2024 Reports') }}" />
				</div>
				<div id="save-search-summary" class="mb-3">
					<!-- Summary will be populated by JavaScript -->
				</div>
				<button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
				<button type="button" class="btn btn-secondary" id="cancel-save-search">{{ __('Cancel') }}</button>
			</form>
		</div>
		@endif
		<!-- End Save Search Modal -->
<div class="container wide-screen-container">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
		<div class="card-header card-header-primary">
                <h4 class="card-title ">
            	@if(env('ENABLE_COLLECTION_LIST') == 1)<a href="/collections">{{ __('Collections') }}</a> ::@endif {{ $collection->name }}
		</h4>
            </div>
        <div class="card-body">
		<div class="row">
                  <div class="col-9">
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
                  <div class="col-3 text-right" id="doc-sort-wrapper">
                  <!-- View Toggle Button -->
                  <button id="view-toggle-btn" class="btn btn-sm btn-primary" title="Switch to Tile View" style="z-index:100;">
                    <i class="material-icons">view_module</i>
                  </button>
                  @if(env('SEARCH_MODE', 'db') == 'elastic')
                  <button id="doc-sort" class="btn btn-sm btn-primary" title="Sort Documents">
                    <i class="material-icons">sort</i>
                  </button>
                  <select id="doc-sort-select" style="text-align:right;" onchange="setDocSort(this.options[this.options.selectedIndex].value);">
                     <option value="relevance">Relevance &#x2193;</option> 
                     <option value="updated_at:desc">Last updated &#x2193;</option> 
                     <option value="updated_at:asc">Last updated &#x2191;</option> 
                    @foreach($collection->meta_fields as $m)
    				    @if(in_array($m->type, ['Numeric', 'Date']) && in_array($m->id,$column_config_meta_fields))
                        <option value="meta_{{$m->id}}:asc">{{ $m->label }} &#x2191;</option>
                        <option value="meta_{{$m->id}}:desc">{{ $m->label }} &#x2193;</option>
                        @endif
                     @endforeach
                  </select>
                  @endif
                  </div>
        </div>
		<div class="row">
			<div class="col-12">
            <!-- <p>{{ $collection->description }}</p> -->
		<!-- children collections -->		
			@if ($collection->parent_id) 
			<div>
				<a 	title="Back to {{ $collection->parent->name }}" href="/collection/{{ $collection->parent->id }}" />						
						<i class="material-icons">folder</i> . .
				</a>
			</div>
			@endif
			@if ($collection->children->count() > 0)
				@foreach ($collection->children as $child)
				<div>
				<a href="/collection/{{ $child->id }}">
					<i class="material-icons">folder</i>
					{{ $child->name }}
                    @if(count($child->documents) == 0 && count($child->children) == 0 && Auth::user() && Auth::user()->hasPermission($collection->id, 'MAINTAINER'))<a href="#" onClick="showSubCollectionDeleteDialog({{ $child->id }});"><i class="material-icons">delete</i> </a>@endif
				</a>
				</div>
				@endforeach
				<!--
				<ul class="navbar-nav">
			        <li class="nav-item dropdown">
          				<a class="btn btn-primary nav-link" title="{{ __('Sub-collections') }}" href="#" id="childrencollections" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="material-icons">subdirectory_arrow_right</i>
          				</a>
					<div class="dropdown-menu dropdown-menu-right" aria-labelledby="childrencollections">
						@foreach ($collection->children as $child)
						<a class="dropdown-item" href="/collection/{{ $child->id }}">{{ $child->name }}</a>
						@endforeach
					</div>
					</li>
				</ul>
				-->
			@endif
			</div><!-- col12 -->
		</div><!-- row -->
        @php
            $meta_fields = empty($collection->meta_fields)? array() : $collection->meta_fields;
		@endphp

            <div class="flash-message">
               @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                   @if(Session::has('alert-' . $msg))
			        <div class="alert alert-<?php echo $msg; ?>">
			        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      	<i class="material-icons">close</i>
                    	</button>
                        <span>{{ Session::get('alert-' . $msg) }}</span>
			        </div>
                   @endif
               @endforeach
            </div>
		<div class="card search-filters-card">
		<div class="row">
			{{-- @if(!empty($column_config->title_search) && $column_config->title_search == 1)
			<div class="float-container col-md-6">
			<form class="inline-form" method="post" action="/collection/{{$collection->id}}/quicktitlefilter">
			@csrf
		   		<label for="title_search" class="search-label">{{ __('Title') }}</label>
		   		<input type="text" class="search-field" id="title_search" name="title_filter" placeholder="{{ __('Search in title...') }}"/>
			</form>
			</div>
		@endif --}}
            
			@foreach($meta_fields as $m)
                @php
                    $extra_attributes = empty($m->extra_attributes) ? null : json_decode($m->extra_attributes);
                    $w = empty($extra_attributes->filter_width_on_collection_page)? 12 : $extra_attributes->filter_width_on_collection_page;
                @endphp
			@if(!empty($column_config->meta_fields_search) && in_array($m->id, $column_config->meta_fields_search))

			@if($m->type == 'SelectCombo' || $m->type == 'Numeric')
			<div class="float-container col-md-{{ $w }}">
			<form class="inline-form" method="post" action="/collection/{{$collection->id}}/quickmetafilters">
			@csrf
		   	<label for="meta_{{ $m->id }}_search" class="search-label">{{ __($m->label) }}</label>
		   	<input type="text" class="search-field" id="meta_{{ $m->id }}_search" name="meta_value[{{ $m->id }}][]" onblur="this.form.submit();"/>
		   	<input type="hidden" name="meta_field[]" value="{{ $m->id }}" />
		   	<input type="hidden" name="meta_type[]" value="{{ $m->type }}" />
		   	<input type="hidden" name="operator[]" value="=" />
			</form>
			</div>
			@elseif($m->type == 'Text' || $m->type == 'Textarea')
			<div class="float-container col-md-{{ $w }}">
			<form class="inline-form" method="post" action="/collection/{{$collection->id}}/quickmetafilters">
			@csrf
		   	<label for="meta_{{ $m->id }}_search" class="search-label">{{ __($m->label) }}</label>
		   	<input type="text" class="search-field" id="meta_{{ $m->id }}_search" name="meta_value[{{ $m->id }}][]" onblur="this.form.submit();"/>
		   	<input type="hidden" name="meta_field[]" value="{{ $m->id }}" />
		   	<input type="hidden" name="meta_type[]" value="{{ $m->type }}" />
		   	<input type="hidden" name="operator[]" value="contains" />
			</form>
			</div>
            @elseif($m->type == 'Select' || $m->type == 'MultiSelect')
            <div class="float-container col-md-{{ $w }}">
			<form class="inline-form" method="post" action="/collection/{{$collection->id}}/quickmetafilters">
			@csrf
            <!-- label for="meta_{{ $m->id }}_search" class="search-label">{{ $m->label }}</label -->
            <select class="search-field" id="meta_{{ $m->id }}_search" title="{{ $m->label }}" name="meta_value[{{ $m->id }}][]" onchange="this.form.submit();">
                @php
                    $options = explode(",", $m->options);
                @endphp
                    <option value="">{{ $m->label }}</option>
                    @foreach($options as $o)
                    <option>{{ $o }}</option>
                    @endforeach
            </select>
            <input type="hidden" name="meta_field[]" value="{{ $m->id }}" />
            <input type="hidden" name="operator[]" value="=" />
            <input type="hidden" name="meta_type[]" value="{{ $m->type }}" />
			</form>
            </div>
			@elseif($m->type == 'Date')
			<div class="float-container col-md-{{ $w }}">
			<form class="inline-form" method="post" action="/collection/{{$collection->id}}/quickmetafilters">
			@csrf
		   	<label for="meta_{{ $m->id }}_search" class="search-label">{{ $m->label }}</label>
		   	<input type="text" class="search-field" id="meta_{{ $m->id }}_search" name="meta_value[{{ $m->id }}][]" />
		   	<input type="hidden" name="meta_field[]" value="{{ $m->id }}" />
		   	<input type="hidden" name="operator[]" value="between" />
		   	<input type="hidden" name="meta_type[]" value="{{ $m->type }}" />
			<script>
                @php
                    $extra_attributes = @json_decode($m->extra_attributes);
                    $min_year = @$extra_attributes->min_year_setting;
                    $max_year = @$extra_attributes->max_year_setting;
                @endphp
				$('#meta_{{ $m->id }}_search').dateRangePicker({
                  monthSelect: true,
                  yearSelect: [ @if($min_year) {{ $min_year }} @else 1900 @endif, 
                                @if($max_year) {{ $max_year }} @else moment().get('year') @endif ]
                })
                .bind('datepicker-change', function(event, obj){
                    this.form.submit();
                }); 
			</script>
			</form>
			</div>
			@elseif($m->type == 'TaxonomyTree')
			<div class="float-container col-md-{{ $w }}">
			<form class="inline-form" method="post" action="/collection/{{$collection->id}}/quickmetafilters">
			@csrf
		   	<label for="meta_{{ $m->id }}_search" class="search-label">{{ $m->label }}</label>
		   	<select class="selectpickertree" id="meta_{{ $m->id }}_search" title="{{ $m->label }}" name="meta_value[{{ $m->id }}][]" onchange="this.form.submit();">
		        @php
                //$options = explode(",", $m->options);
				$taxonomy_m = App\Taxonomy::find($m->options);
            	@endphp
                <option value="">{{ $taxonomy_m->label }}</option>
				@foreach($taxonomy_m->childs as $t)
                    <x-taxonomy-option :taxonomy="$t" :level="0" />
				@endforeach
			</select>
		   	<input type="hidden" name="meta_field[]" value="{{ $m->id }}" />
		   	<input type="hidden" name="operator[]" value="=" />
		   	<input type="hidden" name="meta_type[]" value="{{ $m->type }}" />
			</form>
			</div>
			@endif
			@endif
			@endforeach
		</div>
		<div class="row text-center">
		   <div class="col-12">
			<div class="float-container" style="width:100%;">
            @php
                $old_search_query = !empty(app('request')->input('search_term'))? app('request')->input('search_term') : ''; 
                // set a new search query
                if(!empty(app('request')->input('search_term'))){
                    Session::put('search_query', $old_search_query);
                }
                $session_search_query = Session::get('search_query');
                $parsed_referer = parse_url(request()->headers->get('referer'));
                if(!empty($parsed_referer['path'])){
                    $referer = $parsed_referer['scheme'] . '://' . $parsed_referer['host'] . (isset($parsed_referer['port']) ? ':' . $parsed_referer['port'] : '') . $parsed_referer['path'];
                    if($referer && $referer === Request::url() 
                        && empty($old_search_query) && !empty($session_search_query)){
                        $old_search_query = $session_search_query;
                    } 
                }
            @endphp
			@if(!empty($column_config->file_type_search) && $column_config->file_type_search == 1)
			<form class="inline-form" method="post" action="/collection/{{$collection->id}}/quickextensionfilter">
			@csrf
	   			<select class="search-field" id="file_type_search" name="extension_filter" onchange="this.form.submit();" style="color:#999;" title="{{ __('File Type') }}">
					<option value="" selected disabled style="color:#999;">{{ __('File Type') }}</option>
				</select>
			</form>
			@endif
			<div class="search-input-wrapper" style="display: inline-block; position: relative;width: 100%;">
				<input type="text" class="search-field" id="collection_search" 
					value="@if(!empty($old_search_query)) {{ $old_search_query }} @endif"
					style="padding-right: 25px;width: 100%;"
					placeholder="{{ __('Type a few characters to initiate full-text search') }}"
				/>
				<button type="button" id="clear-search-btn" class="clear-search-btn" 
					style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; padding: 0; font-size: 16px; color: #999; display: none;"
					title="{{ __('Clear search') }}">&times;</button>
			</div>
			<style>
			.dataTables_filter {
			display: none;
			}
            table.dataTable tbody td {
              vertical-align: top;
            }
			</style>
		   </div>
           </div>
        </div>
        @if(env('ENABLE_SEARCH_OPTIONS') == 1 && env('SEARCH_MODE') == 'elastic')
        <div class="row">
           <div class="col-6">
            {{ __('Scope of full-text search:') }} 
            <input type="radio" class="full_text_scope" name="full_text_scope" value="title_n_content" @if(!$full_text_scope || $full_text_scope == 'title_n_content') checked @endif> {{ __('Title and Content') }}</input>
            <input type="radio" class="full_text_scope" name="full_text_scope" value="title" @if($full_text_scope == 'title') checked @endif> {{ __('Title only') }}</input>
            </div>
            <div class="col-6">
            <input type="checkbox" id="fuzzy-search" name="fuzzy" value="1" /> {{ __('Fuzzy search') }}
            </div>
	   </div>
        @endif
			<!--
		   <div class="col-12 text-center">
           <i class="material-icons">search</i>
		   </div>
			-->
		</div><!-- search-filters-card -->
		<!-- show filters -->
		<div>
        <p>
        @php
            $meta_labels = array();
            foreach($meta_fields as $m){
				if(!empty($meta_labels[$m->id]))
				$meta_labels[$m->id] = $m->id;
				else
                $meta_labels[$m->id] = $m->label;
            }
            $all_meta_filters = Session::get('meta_filters');
		$title_filter = Session::get('title_filter');
		$extension_filter = Session::get('extension_filter');
		$show_meta_filters = count($meta_fields)>0 && !empty($all_meta_filters[$collection->id]);
        @endphp
		@if(!empty($title_filter[$collection->id]))
			<span class="filtertag">{{ __('Title contains')}} <i>{{ $title_filter[$collection->id]}}</i>
                <a class="removefiltertag" title="{{ __('remove') }}" href="/collection/{{ $collection->id }}/removetitlefilter">
                <i class="tinyicon material-icons">close</i>
                </a>
                </span>
		@endif
		@if(!empty($extension_filter[$collection->id]))
			<span class="filtertag">{{ __('File Type')}} <i>{{ $extension_filter[$collection->id]}}</i>
                <a class="removefiltertag" title="{{ __('remove') }}" href="/collection/{{ $collection->id }}/removeextensionfilter">
                <i class="tinyicon material-icons">close</i>
                </a>
                </span>
		@endif
		@if($show_meta_filters)
        @foreach( $all_meta_filters[$collection->id] as $m)
		@php
			$m_field = \App\MetaField::find($m['field_id']);
			if($m_field->type == 'TaxonomyTree'){
				$taxonomy_model = App\Taxonomy::find($m['value']);
				$m['value'] = (!empty($taxonomy_model->label))? $taxonomy_model->label:'';
			}	
		@endphp
            <span class="filtertag">
	@if(empty($meta_labels[$m['field_id']]))
            {{ $m['field_id'] }} {{ $m['operator'] }} <i>{{ $m['value'] }}</i>
                <a class="removefiltertag" title="{{ __('remove') }}" href="/collection/{{ $collection->id }}/removefilter/{{ $m['filter_id'] }}">
                <i class="tinyicon material-icons">close</i>
                </a>
	@else
            {{ $meta_labels[$m['field_id']] }} {{ $m['operator'] }} <i>{{ $m['value'] }}</i>
                <a class="removefiltertag" title="{{ __('remove') }}" href="/collection/{{ $collection->id }}/removefilter/{{ $m['filter_id'] }}">
                <i class="tinyicon material-icons">close</i>
                </a>
	@endif
                </span>
        @endforeach
        @endif
		@if(!empty($title_filter[$collection->id]) || !empty($extension_filter[$collection->id]) || $show_meta_filters)
                <a title="{{ __('Remove all filters') }}" href="/collection/{{ $collection->id }}/removeallfilters">
                <i class="tinyicon material-icons">delete_forever</i>
                </a>
		@endif
				@if(Auth::check())
				<button type="button" class="btn btn-sm btn-primary" id="save-search-btn" title="{{ __('Save this search') }}" style="@if(empty($old_search_query) && empty($title_filter[$collection->id]) && empty($extension_filter[$collection->id]) && !$show_meta_filters) display:none; @endif">
					<i class="material-icons">bookmark_add</i> {{ __('Save Search') }}
				</button>
				@endif
        </p>
		</div>
		<!-- display of applied filters ends -->
		    <div class="table-responsive" @if(!empty($column_config->fixed_columns_left) || !empty($column_config->fixed_columns_right)) style="overflow: visible;" @endif>
                    <table id="documents" class="table" style="width:100%">
                        <thead class="text-primary">
                            <tr>
                            <th>{{ __('Type')}}</th>
                            <th>{{ __('Title')}}</th>
			<!-- meta fields -->
				@foreach($collection->meta_fields as $m)
				@if(in_array($m->id,$column_config_meta_fields))
				<th>{{ __($m->label) }}</th>
				@endif
				@endforeach
                            <th>{{__('Approval Status')}}</th>
                            <th>{{__('Size')}}</th>
                            <th>{{__('Created')}}</th>
                <th>@if(env('SHOW_ACTIONS_TH') == 1) {{ __('Actions') }} @endif</th>
                </tr>
                </thead>
               </table>
		    </div>
		    
		    <!-- Tile View Container -->
		    <div id="tile-container" class="tile-container">
		        <!-- Tiles will be dynamically injected here -->
		    </div>
		    
	    <!-- Bottom controls wrapper for tile view -->
	    <div id="tile-bottom-controls" class="dataTables_wrapper" style="display: none;">
	        <!-- Info and pagination will be moved here -->
	    </div>                 </div>
            </div>
        </div>
    </div>
</div>
</div>
		<script>
		@if(!empty(env('TRANSLITERATION')) && $collection->content_type == 'Uploaded documents') 
			// transliteration in the title box is needed only for collection of types "Uploaded documents"
			let searchbox = document.getElementById("collection_search");
			enableTransliteration(searchbox, '{{ env('TRANSLITERATION') }}');
		   {{-- @if(!empty($column_config->title_search) && $column_config->title_search == 1)
			let titlesearchbox = document.getElementById("title_search");
			enableTransliteration(titlesearchbox, '{{ env('TRANSLITERATION') }}');
		   @endif --}}

			@foreach($collection->meta_fields as $m)
					@if($m->type != 'Text') @continue @endif
					@if(!empty($column_config->meta_fields_search) && in_array($m->id, $column_config->meta_fields_search))
					let m_{{$m->id}}_searchbox = document.getElementById("meta_{{$m->id}}_search");
					enableTransliteration(m_{{$m->id}}_searchbox, '{{ env('TRANSLITERATION') }}');
					@endif
				@endforeach
			@endif

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
						else
      					oTable.search(request.term).draw();
                    },
                });
            },
			select: function (event, ui){
   				oTable.search(ui.item.value).draw();
				$("#collection_search").val(ui.item.value);
				return false;
			},
            minLength: 1,
        });
    
    $('.selectpickertree').each(function(index,element){
        $(this).select2ToTree({dropdownCssClass : 'full-width'});
    });

    // Load file types for the dropdown
    @if(!empty($column_config->file_type_search) && $column_config->file_type_search == 1)
    $.ajax({
        url: '/collection/{{$collection->id}}/extensions',
        method: 'GET',
        success: function(response) {
            var fileTypeSelect = $('#file_type_search');
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
                fileTypeSelect.css('color', '#000'); // Change to black when value selected
            @endif
        }
    });
    
    // Change color when user selects an option
    $('#file_type_search').on('change', function() {
        if($(this).val()) {
            $(this).css('color', '#000'); // Black text for selected value
        } else {
            $(this).css('color', '#999'); // Gray for placeholder
        }
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
    @endif

    // Trigger search on page load if search_term is passed via URL (from global search)
    @if(!empty(app('request')->input('search_term')))
    var initialSearchTerm = $('#collection_search').val();
    if (initialSearchTerm && initialSearchTerm.trim().length > 0) {
        oTable.search(initialSearchTerm.trim()).draw();
    }
    @endif
    
    });


	$('#collection_search').keyup(function(){
   		oTable.search($(this).val()).draw();
		toggleClearButton();
		toggleSaveSearchButton();
	});

	// Clear search button functionality
	function toggleClearButton() {
		var searchVal = $('#collection_search').val();
		if (searchVal && searchVal.trim().length > 0) {
			$('#clear-search-btn').show();
		} else {
			$('#clear-search-btn').hide();
		}
	}

	// Toggle Save Search button visibility based on search text or filters
	function toggleSaveSearchButton() {
		var searchVal = $('#collection_search').val();
		var hasFilters = $('.filtertag').length > 0;
		if ((searchVal && searchVal.trim().length > 0) || hasFilters) {
			$('#save-search-btn').show();
		} else {
			$('#save-search-btn').hide();
		}
	}

	$('#clear-search-btn').click(function() {
		$('#collection_search').val('');
		oTable.search('').draw();
		$(this).hide();
		$('#collection_search').focus();
		toggleSaveSearchButton();
	});

	// Initialize clear button visibility on page load
	toggleClearButton();
	// Initialize save search button visibility on page load
	toggleSaveSearchButton();

    $(".full_text_scope").click(function(){
        $.ajax({
            url: '/collection/{{ $collection->id }}/set-search-scope',
            method: 'GET',
            data: {
                scope : $('input[name="full_text_scope"]:checked').val(),
            },
            success: function(){
                search_val = $('#collection_search').val();
	            oTable.search(search_val).draw();
            }
        });
    });
    
    $("#fuzzy-search").click(function(){
        $.ajax({
            url: '/collection/{{ $collection->id }}/set-fuzzy',
            method: 'GET',
            data: {
                fuzzy : $("#fuzzy-search").prop('checked'),
            },
            success: function(){
                search_val = $('#collection_search').val();
	            oTable.search(search_val).draw();
            }
        });
    });

    // ===== TILE VIEW FUNCTIONALITY =====
    
    var currentViewMode = localStorage.getItem('viewMode_{{ $collection->id }}') || 'list';
    var tileData = [];
    var currentPage = 0;
    var recordsPerPage = 50;
    var totalRecords = 0;
    var isLoading = false;

    function calculateOptimalPageLength() {
        var containerWidth = $('#tile-container').width();
        if (!containerWidth || containerWidth === 0) {
            containerWidth = $(window).width() - 40; // Approx padding adjustment
        }
        
        var tilesPerRow = Math.floor((containerWidth + 20) / 200);
        if (tilesPerRow < 1) tilesPerRow = 1;
        
        var rows = 3;
        var optimalLength = tilesPerRow * rows;
        
        if (optimalLength < 10) optimalLength = 10;
        
        return optimalLength;
    }

    // Debounce helper
    function debounce(func, wait) {
        var timeout;
        return function() {
            var context = this, args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                func.apply(context, args);
            }, wait);
        };
    }

    // Add resize listener
    $(window).on('resize', debounce(function() {
        if (typeof currentViewMode !== 'undefined' && currentViewMode === 'tile' && typeof oTable !== 'undefined') {
            var newLength = calculateOptimalPageLength();
            if (oTable.page.len() !== newLength) {
                console.log('Resize: Updating page length to ' + newLength);
                oTable.page.len(newLength).draw();
            }
        }
    }, 250));
    
    // Initialize view mode on page load
    function initializeViewMode() {
        if (currentViewMode === 'tile') {
            activateTileView();
        }
    }
    
    // Toggle between list and tile view
    $('#view-toggle-btn').click(function() {
        if (currentViewMode === 'list') {
            currentViewMode = 'tile';
            activateTileView();
        } else {
            currentViewMode = 'list';
            activateListView();
        }
        localStorage.setItem('viewMode_{{ $collection->id }}', currentViewMode);
    });
    
    // Store original positions on first load
    var originalControlsParent = {
        length: null,
        info: null,
        paginate: null
    };
    
    // Save original positions
    function saveOriginalPositions() {
        if (!originalControlsParent.info || !originalControlsParent.paginate) {
            var $wrapper = $(oTable.table().container());
            var $info = $wrapper.find('.dataTables_info, .dt-info').first();
            var $paginate = $wrapper.find('.dataTables_paginate, .dt-paging').first();
            
            // Only save if not already in tile-bottom-controls
            if ($info.length && $info.parent().attr('id') !== 'tile-bottom-controls') {
                originalControlsParent.info = $info.parent();
                console.log('Saved info parent:', originalControlsParent.info.attr('class'));
            }
            if ($paginate.length && $paginate.parent().attr('id') !== 'tile-bottom-controls') {
                originalControlsParent.paginate = $paginate.parent();
                console.log('Saved paginate parent:', originalControlsParent.paginate.attr('class'));
            }
        }
    }
    
    // Restore controls to original positions
    function restoreOriginalControls() {
        var $wrapper = $(oTable.table().container());
        var $info = $wrapper.find('.dataTables_info, .dt-info').first();
        var $paginate = $wrapper.find('.dataTables_paginate, .dt-paging').first();
        
        if (!$info.length) $info = $('.dataTables_info, .dt-info').first();
        if (!$paginate.length) $paginate = $('.dataTables_paginate, .dt-paging').first();
        
        console.log('Restoring controls - Found:', {
            info: $info.length,
            paginate: $paginate.length,
            infoParent: $info.parent().attr('id'),
            paginateParent: $paginate.parent().attr('id'),
            hasOriginalParents: !!(originalControlsParent.info && originalControlsParent.paginate)
        });
        
        // Move controls back using detach to preserve event handlers
        if (originalControlsParent.info && $info.length) {
            // Move back regardless of current parent to ensure proper positioning
            originalControlsParent.info.append($info.detach());
            $info.removeAttr('style'); // Remove inline styles
            $info.show(); // Ensure visible
            console.log('Restored info control');
        }
        if (originalControlsParent.paginate && $paginate.length) {
            // Move back regardless of current parent to ensure proper positioning
            originalControlsParent.paginate.append($paginate.detach());
            $paginate.removeAttr('style'); // Remove inline styles
            $paginate.show(); // Ensure visible
            console.log('Restored paginate control');
        }
    }
    
    // Move controls for tile view
    function moveTileControls() {
        saveOriginalPositions();
        
        if ($('#tile-container').length && $('#tile-bottom-controls').length) {
            var $wrapper = $(oTable.table().container());
            var $info = $wrapper.find('.dataTables_info, .dt-info').first();
            var $paginate = $wrapper.find('.dataTables_paginate, .dt-paging').first();
             
            if (!$info.length) $info = $('.dataTables_info, .dt-info').first();
            if (!$paginate.length) $paginate = $('.dataTables_paginate, .dt-paging').first();
            
            console.log('moveTileControls - Found controls:', {
                info: $info.length,
                paginate: $paginate.length,
                infoParent: $info.parent().attr('id') || $info.parent().attr('class'),
                paginateParent: $paginate.parent().attr('id') || $paginate.parent().attr('class')
            });
            
            // Only move if controls exist and are not already in bottom wrapper
            var $bottomControls = $('#tile-bottom-controls');
            if ($bottomControls.length) {
                var $originalContainer = $(oTable.table().container());
                if ($originalContainer.length) {
                    var originalClasses = $originalContainer.attr('class');
                    $bottomControls.attr('class', originalClasses);
                }
                $bottomControls.attr('id', 'tile-bottom-controls');

                // MOVE (not clone) the actual controls to preserve event handlers
                if ($info.length && $info.parent().attr('id') !== 'tile-bottom-controls') {
                    $bottomControls.append($info.detach());
                    $info.removeAttr('style'); 
                    $info.show();
                    console.log('Moved info control');
                }
                if ($paginate.length && $paginate.parent().attr('id') !== 'tile-bottom-controls') {
                    $bottomControls.append($paginate.detach());
                    $paginate.removeAttr('style');
                    $paginate.show();
                    console.log('Moved paginate control');
                }
                
                $bottomControls.show();
                console.log('Bottom controls wrapper shown');
            }
        }
    }
    
    // Activate tile view
    function activateTileView() {
        // Store current scroll position
        var scrollPos = $(window).scrollTop();
        
        $('.card-body').addClass('tile-view-active');
        $('#tile-container').addClass('active');
        $('#view-toggle-btn').html('<i class="material-icons">view_list</i>');
        $('#view-toggle-btn').attr('title', 'Switch to List View');
        
        var $scrollWrapper = $('.dataTables_scroll');
        if ($scrollWrapper.length) {
            $scrollWrapper.hide();
        } else {
            $('#documents').hide();
        }
        
        $('.table-responsive').show();

        var newLength = calculateOptimalPageLength();
        
        if (oTable.page.len() !== newLength) {
            console.log('Tile View: Updating page length to ' + newLength);
            oTable.page.len(newLength).draw();
            // multiple draws might update the controls automatically via drawCallback
        } else {
            // Use current DataTable data to render tiles
            var data = oTable.rows({page: 'current'}).data().toArray();
            renderTiles(data, false);
            
            // Move DataTable controls after a short delay to ensure DataTable has rendered
            setTimeout(function() {
                moveTileControls();
            }, 100);
        }
        
        // Restore scroll position to prevent jump
        $(window).scrollTop(scrollPos);
    }
    
    // Activate list view
    function activateListView() {
        // Store current scroll position
        var scrollPos = $(window).scrollTop();
        
        $('.card-body').removeClass('tile-view-active');
        $('#tile-container').removeClass('active');
        $('#tile-container').html('');
        
        // FIRST restore controls to their original positions BEFORE hiding/emptying the wrapper
        if (oTable) {
            console.log('Switching to list view - restoring controls');
            restoreOriginalControls();
            
            // Show the table content
            var $scrollWrapper = $('.dataTables_scroll');
            if ($scrollWrapper.length) {
                $scrollWrapper.show();
            } else {
                $('#documents').show();
            }
            
            // Show the table wrapper
            $('.table-responsive').show();
            
            // Force DataTable to recalculate column widths WITHOUT redrawing
            oTable.columns.adjust();
        }
        
        // NOW hide the bottom controls wrapper (after moving controls out)
        $('#tile-bottom-controls').hide();
        
        $('#view-toggle-btn').html('<i class="material-icons">view_module</i>');
        $('#view-toggle-btn').attr('title', 'Switch to Tile View');
        
        // Restore scroll position to prevent jump
        $(window).scrollTop(scrollPos);
    }
    

    
    // Render tiles from data
    function renderTiles(data, append) {
        console.log('Tile view: Rendering tiles', data.length, 'documents');
        
        if (!data || data.length === 0) {
            $('#tile-container').html('<div class="tile-empty-state"><i class="material-icons">folder_open</i><p>No documents found</p></div>');
            $('#tile-pagination').remove();
            return;
        }
        
        var tilesHtml = append ? '' : '';
        
        data.forEach(function(doc, index) {
            // Safe access to nested properties
            var filetype = doc.type && doc.type.filetype ? doc.type.filetype : 'unknown';
            var icon = getFileIcon(filetype);
            var iconClass = getFileIconClass(filetype);
            
            // Extract document ID - try multiple sources
            var docId = '';
            if (doc.DT_RowId) {
                // DT_RowId format is usually "row_123"
                docId = doc.DT_RowId.toString().replace('row_', '');
            } else if (doc.id) {
                docId = doc.id;
            } else if (doc.document_id) {
                docId = doc.document_id;
            }
            
            var docUrl = (filetype === 'pdf')
                ? '/collection/{{ $collection->id }}/document/' + docId + '/doc-viewer'
                : '/collection/{{ $collection->id }}/document/' + docId;
            
            if (index === 0) {
                console.log('Tile view: Document data', doc);
                console.log('Tile view: Document ID extracted', docId);
                console.log('Tile view: Document URL', docUrl);
            }
            
            // Escape HTML to prevent XSS
            function escapeHtml(text) {
                if (!text) return '';
                var div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
            
            // Function to strip HTML tags
            function stripHtml(html) {
                if (!html) return '';
                var tmp = document.createElement('div');
                tmp.innerHTML = html;
                return tmp.textContent || tmp.innerText || '';
            }
            
            // Build metadata HTML
            var metadataHtml = '<div class="tile-metadata"><div class="tile-metadata-content">';
            //metadataHtml += '<div class="metadata-row"><span class="metadata-label">Title:</span><span class="metadata-value">' + escapeHtml(stripHtml(doc.title)) + '</span></div>';
            
            @foreach($collection->meta_fields as $m)
                @if(in_array($m->id,$column_config_meta_fields))
                if (doc.meta_{{ $m->id }}) {
                    metadataHtml += '<div class="metadata-row"><span class="metadata-label">{{ __($m->label) }}:</span><span class="metadata-value">' + escapeHtml(stripHtml(doc.meta_{{ $m->id }})) + '</span></div>';
                }
                @endif
            @endforeach
            
            @if(!$hide_approval_status)
            if (doc.approval_status) {
                metadataHtml += '<div class="metadata-row"><span class="metadata-label">Approval:</span><span class="metadata-value">' + doc.approval_status + '</span></div>';
            }
            @endif
            
            @if(!$hide_size)
            if (doc.size && doc.size.display) {
                metadataHtml += '<div class="metadata-row"><span class="metadata-label">Size:</span><span class="metadata-value">' + doc.size.display + '</span></div>';
            }
            @endif
            
            @if(!$hide_creation_time)
            if (doc.updated_at && doc.updated_at.display) {
                metadataHtml += '<div class="metadata-row"><span class="metadata-label">Updated:</span><span class="metadata-value">' + doc.updated_at.display + '</span></div>';
            }
            @endif
            
            metadataHtml += '</div></div>';
            
            // Get plain text title
            var tempTitleDiv = document.createElement('div');
            tempTitleDiv.innerHTML = doc.title;
            var plainTitle = tempTitleDiv.textContent || tempTitleDiv.innerText || '';
            
            tilesHtml += '<div class="document-tile" data-doc-url="' + docUrl + '">';
            
            // Add info icon first (outside thumbnail so it can be sibling of metadata)
            tilesHtml += '  <div class="tile-info-icon" data-tile-id="tile_' + docId + '">i</div>';
            
            // Show thumbnail if available, otherwise show icon
            if (doc.type && doc.type.display && doc.type.display.includes('<img')) {
                // Extract thumbnail URL from img tag
                var tempDiv = document.createElement('div');
                tempDiv.innerHTML = doc.type.display;
                var imgElement = tempDiv.querySelector('img');
                if (imgElement) {
                    tilesHtml += '  <div class="tile-thumbnail">';
                    tilesHtml += '    <img src="' + imgElement.src + '" alt="Document preview" />';
                    tilesHtml += '  </div>';
                } else {
                    tilesHtml += '  <div class="tile-thumbnail">';
                    tilesHtml += '    <div class="tile-icon ' + iconClass + '"><i class="material-icons">' + icon + '</i></div>';
                    tilesHtml += '  </div>';
                }
            } else {
                tilesHtml += '  <div class="tile-thumbnail">';
                tilesHtml += '    <div class="tile-icon ' + iconClass + '"><i class="material-icons">' + icon + '</i></div>';
                tilesHtml += '  </div>';
            }
            
            tilesHtml += '  <div class="tile-title" data-full-title="' + escapeHtml(plainTitle) + '" title="' + escapeHtml(plainTitle) + '">' + escapeHtml(plainTitle) + '</div>';
            
            @if(!$hide_size || !$hide_creation_time)
            tilesHtml += '  <div class="tile-info">';
            @if(!$hide_size)
            if (doc.size && doc.size.display) {
                tilesHtml += '<div>' + doc.size.display + '</div>';
            }
            @endif
            @if(!$hide_creation_time)
            if (doc.updated_at && doc.updated_at.display) {
                tilesHtml += '<div>' + doc.updated_at.display + '</div>';
            }
            @endif
            tilesHtml += '  </div>';
            @endif
            
            tilesHtml += metadataHtml;
            tilesHtml += '</div>';
        });
        
        if (append) {
            $('#tile-container').append(tilesHtml);
        } else {
            $('#tile-container').html(tilesHtml);
        }
        
        // Add event handlers for tiles
        attachTileEventHandlers();
    }
    
    // Attach event handlers to tiles
    function attachTileEventHandlers() {
        // Click on tile (but not on info icon) to navigate
        $('.document-tile').off('click').on('click', function(e) {
            if (!$(e.target).closest('.tile-info-icon, .tile-metadata').length) {
                var url = $(this).attr('data-doc-url');
                if (url) {
                    window.open(url, '_blank');
                }
            }
        });
        
        // Metadata now shows on hover via CSS - no click handler needed
    }
    
    // Get material icon for file type
    function getFileIcon(filetype) {
        var iconMap = {
            'pdf': 'picture_as_pdf',
            'doc': 'description',
            'docx': 'description',
            'xls': 'table_chart',
            'xlsx': 'table_chart',
            'ppt': 'slideshow',
            'pptx': 'slideshow',
            'txt': 'text_snippet',
            'jpg': 'image',
            'jpeg': 'image',
            'png': 'image',
            'gif': 'image',
            'mp4': 'video_library',
            'avi': 'video_library',
            'mp3': 'audio_file',
            'wav': 'audio_file',
            'zip': 'folder_zip',
            'rar': 'folder_zip',
            'html': 'code',
            'htm': 'code'
        };
        
        return iconMap[filetype] || 'insert_drive_file';
    }
    
    // Get CSS class for file type
    function getFileIconClass(filetype) {
        if (filetype === 'pdf') return 'pdf';
        if (filetype === 'doc' || filetype === 'docx') return 'doc';
        if (filetype === 'xls' || filetype === 'xlsx') return 'xls';
        if (filetype === 'ppt' || filetype === 'pptx') return 'ppt';
        if (filetype === 'txt') return 'txt';
        if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg'].includes(filetype)) return 'image';
        if (['mp4', 'avi', 'mov', 'wmv'].includes(filetype)) return 'video';
        if (['mp3', 'wav', 'ogg'].includes(filetype)) return 'audio';
        if (['zip', 'rar', '7z', 'tar', 'gz'].includes(filetype)) return 'archive';
        return 'default';
    }
    

	// Save Search functionality
	@if(Auth::check())
	var saveSearchDialog;
	
	$('#save-search-btn').click(function(){
		// Build summary of current search/filters
		var summary = '<strong>{{ __("Current search will be saved:") }}</strong><ul>';
		
		var searchText = $('#collection_search').val();
		if (searchText) {
			summary += '<li>{{ __("Search text:") }} "' + $('<div>').text(searchText).html() + '"</li>';
		}
		
		// Get applied filters from the page but keep values (don't remove <i> elements)
		var filters = [];
		$('.filtertag').each(function(){
			// clone and remove only the remove-link (anchor) so value in <i> stays
			var $clone = $(this).clone();
			$clone.find('a').remove();
			var filterText = $clone.text().trim();
			if (filterText) {
				filters.push(filterText);
			}
		});

		// Include file type if present and not already in filters
		var fileTypeVal = '';
		var fileTypeText = '';
		var fileTypeEl = $('#file_type_search');
		if (fileTypeEl.length) {
			fileTypeVal = fileTypeEl.val() || '';
			// get displayed text for selected option
			fileTypeText = fileTypeEl.find('option:selected').text() || '';
			if (fileTypeVal && !filters.join(' ').includes(fileTypeVal) && !filters.join(' ').includes(fileTypeText)) {
				filters.push('{{ __("File Type") }}: ' + $('<div>').text(fileTypeText || fileTypeVal).html());
			}
		}

		if (filters.length > 0) {
			summary += '<li>{{ __("Filters:") }} ' + filters.join(', ') + '</li>';
		}

		if (!searchText && filters.length === 0) {
			summary = '<p class="text-warning">{{ __("No search text or filters are currently applied. The saved search will show all documents in this collection.") }}</p>';
		} else {
			summary += '</ul>';
		}
		
		$('#save-search-summary').html(summary);
		$('#search_name').val('');
		
		saveSearchDialog = $('#save-search-dialog').dialog({
			title: '{{ __("Save Search") }}',
			width: 450,
			modal: true
		});
	});
	
	$('#cancel-save-search').click(function(){
		if (saveSearchDialog) {
			saveSearchDialog.dialog('close');
		}
	});
	
	$('#save-search-form').submit(function(e){
		e.preventDefault();
		
		var searchName = $('#search_name').val().trim();
		if (!searchName) {
			alert('{{ __("Please enter a name for this saved search.") }}');
			return;
		}
		
		// collect values to send: search text and filetype (controller will pick up meta filters from session)
		var postData = {
			_token: '{{ csrf_token() }}',
			name: searchName,
			collection_id: {{ $collection->id }},
			search_text: $('#collection_search').val() || ''
		};

		var fileTypeEl = $('#file_type_search');
		if (fileTypeEl.length) {
			postData.extension_filter = fileTypeEl.val() || '';
		}

		$.ajax({
			url: '{{ route("saved-searches.store") }}',
			method: 'POST',
			data: postData,
			success: function(response) {
				if (response.status === 'success') {
					if (saveSearchDialog) {
						saveSearchDialog.dialog('close');
					}
					alert('{{ __("Search saved successfully!") }}');
				} else {
					alert('{{ __("Error saving search. Please try again.") }}');
				}
			},
			error: function(xhr) {
				console.error('Error saving search:', xhr);
				alert('{{ __("Error saving search. Please try again.") }}');
			}
		});
	});
	@endif

	</script>
@endsection
