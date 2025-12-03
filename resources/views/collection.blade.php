@extends('layouts.app',['class'=> 'off-canvas-sidebar'])

@section('content')
@push('js')
<script src="/js/jquery.dataTables.min.js"></script>
<script src="/js/jquery-ui.js" defer></script>
<script type="text/javascript" src="/js/transliteration-input.bundle.js"></script>
<link href="/css/jquery-ui.css" rel="stylesheet">
<link href="/css/select2.min.css" rel="stylesheet" />
<link href="/css/select2totree.css" rel="stylesheet" />
<link href="/css/tile-view.css" rel="stylesheet" />
<script src="/js/select2.min.js"></script>
<script src="/js/select2totree.js"></script>
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
            var api = this.api();
            var data = api.rows({page: 'current'}).data().toArray();
            renderTiles(data, false);
            
            // Update tile pagination info
            var info = api.page.info();
            var paginationHtml = '<div class="tile-pagination-info">Showing ' + (info.start + 1) + ' to ' + info.end + ' of ' + info.recordsTotal + ' documents</div>';
            if ($('#tile-pagination').length) {
                $('#tile-pagination').html(paginationHtml);
            } else {
                $('#tile-container').after('<div id="tile-pagination" class="text-center mt-3"></div>');
                $('#tile-pagination').html(paginationHtml);
            }
        }
    },
    "columnDefs": [
		{ "targets":[0], "className":'text-center', "sortable":false, @if($hide_type)"visible":false @endif},
		{ "targets":[1], "className":'text-left',"sortable":false, @if($hide_title) ,"visible":false @endif},
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
    //"order": [[ 3, "desc" ]],
    "order": [], // initial ordering disabled. Good for sorting by relevance in ES.
    "serverSide":true,
    "ajax":'/collection/{{$collection->id}}/search',
    "language": 
	{          
	"processing": "<img src='/i/processing.gif'>",
	},
    "columns":[
       {data:"type",
          render:{
            '_':'display',
            'sort':'filetype'
          }
       },
       {data:"title"},
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

} );


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

function randomString(length) {
   var result           = '';
   var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
   var charactersLength = characters.length;
   for ( var i = 0; i < length; i++ ) {
      result += characters.charAt(Math.floor(Math.random() * charactersLength));
   }
   return result;
}

</script>

<script src="/js/jquery.daterangepicker.min.js"></script>
<link rel="stylesheet" href="/js/daterangepicker.css"/>

@endpush
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
<div class="container">
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
                  <div class="col-12 text-right">
                  <!-- View Toggle Button -->
                  <button id="view-toggle-btn" class="btn btn-sm btn-primary" title="Switch to Tile View">
                    <i class="material-icons">view_module</i>
                  </button>
                  @if(Auth::user() && Auth::user()->hasPermission($collection->id, 'MAINTAINER'))
                    <a title="{{ __('Manage users of this collection') }}" href="/collection/{{ $collection->id }}/users" class="btn btn-sm btn-primary"><i class="material-icons">people</i></a>
		    @if($collection->content_type == 'Uploaded documents')	
                    <a title="{{ __('Manage cataloging fields of this collection') }}" href="/collection/{{ $collection->id }}/meta" class="btn btn-sm btn-primary"><i class="material-icons">label</i></a>
                    <a title="Settings" href="/collection/{{ $collection->id }}/settings" class="btn btn-sm btn-primary"><i class="material-icons">settings</i></a>
                    @if(env('ENABLE_CHILD_COLLECTION_LINK') == 1)
                    <a title="{{__('New Child Collection')}}" href="/collection/{{ $collection->id }}/child-collection/new" class="btn btn-sm btn-primary"><i class="material-icons">create_new_folder</i></a>
                    @endif
		    @elseif($collection->content_type == 'Web resources')	
                    <a title="Manage Sites for this collection" href="/collection/{{ $collection->id }}/save_exclude_sites" class="btn btn-sm btn-primary"><i class="material-icons">insert_link</i></a>
		    @endif
		  @endif
                  @if(Auth::user() && Auth::user()->hasPermission($collection->id, 'CREATE') && $collection->content_type == 'Uploaded documents')
                    <a title="New Document" href="/collection/{{ $collection->id }}/upload" class="btn btn-sm btn-primary"><i class="material-icons">file_upload</i></a>
                    @if(env('ENABLE_IMPORT_LINK') == 1)
                    <a title="Import via URL" href="/collection/{{ $collection->id }}/url-import" class="btn btn-sm btn-primary"><i class="material-icons">link</i></a>
                    @endif
		  @endif
                  @if(count($collection->meta_fields)>0 && env('ENABLE_FILTER_LINK') == 1)
                    <a href="/collection/{{ $collection->id }}/metafilters" title="Set Filters" class="btn btn-sm btn-primary"><i class="material-icons">filter_list</i></a>
                  @endif
                  @if(Auth::user() && Auth::user()->hasPermission($collection->id, 'MAINTAINER'))
                    <!--a href="/collection/{{ $collection->id }}/export" title="Export collection to CSV" class="btn btn-sm btn-primary"><i class="material-icons">file_download</i></a-->
                    <a href="/collection/{{ $collection->id }}/exportxlsx" title="Export up to 1000 records to XLSX" class="btn btn-sm btn-primary"><i class="material-icons">file_download</i></a>
				  @endif
                  </div>
        </div>
		<div class="row">
			<div class="col-12">
            <p>{{ $collection->description }}</p>
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
            {{--
			@if(!empty($column_config->title_search) && $column_config->title_search == 1)
			<div class="float-container col-md-12">
			<form class="inline-form" method="post" action="/collection/{{$collection->id}}/quicktitlefilter">
			@csrf
		   		<label for="title_search" class="search-label">{{ __('Look for a phrase in the title of the documents.') }}</label>
		   		<input type="text" class="search-field" id="title_search" name="title_filter"/>
			</form>
			</div>
			@endif
            --}}
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
				$('#meta_{{ $m->id }}_search').dateRangePicker({
                  monthSelect: true,
                  yearSelect: [1900, moment().get('year')]
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
			<label for="collection_search">{{ __('Type a few characters to initiate full-text search') }}</label>
		    <input type="text" class="search-field" id="collection_search" 
            value="@if(!empty($old_search_query)) {{ $old_search_query }} @endif"
            />
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
            Scope of full-text search: 
            <input type="radio" class="full_text_scope" name="full_text_scope" value="title_n_content" @if(!$full_text_scope || $full_text_scope == 'title_n_content') checked @endif> Title and Content</input>
            <input type="radio" class="full_text_scope" name="full_text_scope" value="title" @if($full_text_scope == 'title') checked @endif> Title only</input>
            </div>
            <div class="col-6">
            <input type="checkbox" id="fuzzy-search" name="fuzzy" value="1" /> Fuzzy search
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
		$show_meta_filters = count($meta_fields)>0 && !empty($all_meta_filters[$collection->id]);
        @endphp
		@if(!empty($title_filter[$collection->id]))
			<span class="filtertag">{{ __('Title contains')}} <i>{{ $title_filter[$collection->id]}}</i>
                <a class="removefiltertag" title="remove" href="/collection/{{ $collection->id }}/removetitlefilter">
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
                <a class="removefiltertag" title="remove" href="/collection/{{ $collection->id }}/removefilter/{{ $m['filter_id'] }}">
                <i class="tinyicon material-icons">close</i>
                </a>
	@else
            {{ $meta_labels[$m['field_id']] }} {{ $m['operator'] }} <i>{{ $m['value'] }}</i>
                <a class="removefiltertag" title="remove" href="/collection/{{ $collection->id }}/removefilter/{{ $m['filter_id'] }}">
                <i class="tinyicon material-icons">close</i>
                </a>
	@endif
                </span>
        @endforeach
        @endif
		@if(!empty($title_filter[$collection->id]) || $show_meta_filters)
                <a title="{{ __('Remove all filters') }}" href="/collection/{{ $collection->id }}/removeallfilters">
                <i class="tinyicon material-icons">delete_forever</i>
                </a>
		@endif
        </p>
		</div>
		<!-- display of applied filters ends -->
		   <div class="table-responsive">
                    <table id="documents" class="table">
                        <thead class="text-primary">
                            <tr>
                            <th>{{ __('Type')}}</th>
                            <th>{{__('Title')}}</th>
			<!-- meta fields -->
				@foreach($collection->meta_fields as $m)
				@if(in_array($m->id,$column_config_meta_fields))
				<th>{{ __($m->label) }}</th>
				@endif
				@endforeach
                            <th>{{__('Approval Status')}}</th>
                            <th>{{__('Size')}}</th>
                            <th>{{__('Created')}}</th>
                <th>@if(env('SHOW_ACTIONS_TH') == 1) Actions @endif</th>
                </tr>
                </thead>
               </table>
		    </div>
		    
		    <!-- Tile View Container -->
		    <div id="tile-container" class="tile-container">
		        <!-- Tiles will be dynamically injected here -->
		    </div>
		    
                 </div>
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
			   @if(!empty($column_config->title_search) && $column_config->title_search == 1)
				let titlesearchbox = document.getElementById("title_search");
				enableTransliteration(titlesearchbox, '{{ env('TRANSLITERATION') }}');
			   @endif

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

    // why is this call needed?
    //oTable.search($('#collection_search').val()).draw();
    
    });


	$('#collection_search').keyup(function(){
   		oTable.search($(this).val()).draw() ;
	});

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
    
    // Activate tile view
    function activateTileView() {
        $('.card-body').addClass('tile-view-active');
        $('#tile-container').addClass('active');
        $('#view-toggle-btn').html('<i class="material-icons">view_list</i>');
        $('#view-toggle-btn').attr('title', 'Switch to List View');
        
        // Use current DataTable data to render tiles
        var data = oTable.rows({page: 'current'}).data().toArray();
        renderTiles(data, false);
        
        // Update pagination info
        var info = oTable.page.info();
        var paginationHtml = '<div class="tile-pagination-info">Showing ' + (info.start + 1) + ' to ' + info.end + ' of ' + info.recordsTotal + ' documents</div>';
        if ($('#tile-pagination').length) {
            $('#tile-pagination').html(paginationHtml);
        } else {
            $('#tile-container').after('<div id="tile-pagination" class="text-center mt-3"></div>');
            $('#tile-pagination').html(paginationHtml);
        }
    }
    
    // Activate list view
    function activateListView() {
        $('.card-body').removeClass('tile-view-active');
        $('#tile-container').removeClass('active');
        $('#tile-container').html('');
        $('#tile-pagination').remove();
        $('#view-toggle-btn').html('<i class="material-icons">view_module</i>');
        $('#view-toggle-btn').attr('title', 'Switch to Tile View');
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
            
            var docUrl = '/collection/{{ $collection->id }}/document/' + docId;
            
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
            
            // Build metadata HTML
            var metadataHtml = '<div class="tile-metadata"><div class="tile-metadata-content">';
            metadataHtml += '<div class="metadata-row"><span class="metadata-label">Title:</span><span class="metadata-value">' + escapeHtml(doc.title) + '</span></div>';
            
            @foreach($collection->meta_fields as $m)
            @if(in_array($m->id,$column_config_meta_fields))
            if (doc.meta_{{ $m->id }}) {
                metadataHtml += '<div class="metadata-row"><span class="metadata-label">{{ __($m->label) }}:</span><span class="metadata-value">' + doc.meta_{{ $m->id }} + '</span></div>';
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
            
            tilesHtml += '<div class="document-tile" onclick="window.location.href=\'' + docUrl + '\'">';
            
            // Show thumbnail if available, otherwise show icon
            if (doc.type && doc.type.display && doc.type.display.includes('<img')) {
                // Extract thumbnail URL from img tag
                var tempDiv = document.createElement('div');
                tempDiv.innerHTML = doc.type.display;
                var imgElement = tempDiv.querySelector('img');
                if (imgElement) {
                    tilesHtml += '  <div class="tile-thumbnail"><img src="' + imgElement.src + '" alt="Document preview" /></div>';
                } else {
                    tilesHtml += '  <div class="tile-icon ' + iconClass + '"><i class="material-icons">' + icon + '</i></div>';
                }
            } else {
                tilesHtml += '  <div class="tile-icon ' + iconClass + '"><i class="material-icons">' + icon + '</i></div>';
            }
            
            tilesHtml += '  <div class="tile-title">' + escapeHtml(doc.title) + '</div>';
            
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
    

    
    // Initialize view mode
    initializeViewMode();

	</script>
@endsection
