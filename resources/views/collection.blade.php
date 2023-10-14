@extends('layouts.app',['class'=> 'off-canvas-sidebar'])

@section('content')
@push('js')
<script src="/js/jquery.dataTables.min.js"></script>
<script src="/js/jquery-ui.js" defer></script>
<script type="text/javascript" src="/js/transliteration-input.bundle.js"></script>
<link href="/css/jquery-ui.css" rel="stylesheet">
@php
$column_config = json_decode($collection->column_config);
list($hide_type, $hide_title, $hide_size, $hide_creation_time) = array(false, false, false, false);
if(!empty($collection->column_config)){
	if(@$column_config->type != 1) $hide_type = true;
	if(@$column_config->title != 1) $hide_title = true;
	if(@$column_config->size != 1) $hide_size = true;
	if(@$column_config->creation_time != 1) $hide_creation_time = true;
}
@endphp
<script>
var deldialog;
$(document).ready(function() {
    oTable = $('#documents').DataTable({
    "columnDefs": [
		{ "targets":[0], "className":'text-center', @if($hide_type)"visible":false @endif},
		{ "targets":[1], "className":'text-left' @if($hide_title) ,"visible":false @endif},
		{ "targets":[2], "className":'text-right dt-nowrap' @if($hide_size) ,"visible":false @endif},
		{ "targets":[3], "className":'text-right dt-nowrap' @if($hide_creation_time) ,"visible":false @endif},
		@php
			$i = 4;
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
		@foreach($column_config_meta_fields as $m_id)
			@php
			$m = \App\MetaField::find($m_id);
			@endphp
		{data:"meta_{{@$m->id}}"},
		@endforeach
        {data:"actions"},
    ],
    });

} );

function showDeleteDialog(document_id){
	str = randomString(6);
	$('#text_captcha').text(str);
	$('#hidden_captcha').text(str);
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
                  @if(Auth::user() && Auth::user()->hasPermission($collection->id, 'MAINTAINER'))
                    <a title="{{ __('Manage users of this collection') }}" href="/collection/{{ $collection->id }}/users" class="btn btn-sm btn-primary"><i class="material-icons">people</i></a>
		    @if($collection->content_type == 'Uploaded documents')	
                    <a title="{{ __('Manage cataloging fields of this collection') }}" href="/collection/{{ $collection->id }}/meta" class="btn btn-sm btn-primary"><i class="material-icons">label</i></a>
                    <a title="Settings" href="/collection/{{ $collection->id }}/settings" class="btn btn-sm btn-primary"><i class="material-icons">settings</i></a>
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
		<div class="row">
			<div class="col-10">
            <p>{{ $collection->description }}</p>
			</div>
			<div class="col-2 text-right">
		<!-- children collections -->		
			@if ($collection->children->count() > 0)
			<div class="navbar-collapse justify-content-end">
				<ul class="navbar-nav">
			        <li class="nav-item dropdown">
          				<a class="btn btn-primary nav-link" title="{{ __('Sub-collections') }}" href="#" id="childrencollections" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="material-icons">folder</i>
						<i class="material-icons">subdirectory_arrow_right</i>
          				</a>
					<div class="dropdown-menu dropdown-menu-right" aria-labelledby="childrencollections">
						@foreach ($collection->children as $child)
						<a class="dropdown-item" href="/collection/{{ $child->id }}">{{ $child->name }}</a>
						@endforeach
					</div>
					</li>
				</ul><!-- navbar-nav -->
			</div>
			@endif
			@if ($collection->parent_id) 
				<a class="btn btn-primary" title="Back to {{ $collection->parent->name }}" href="/collection/{{ $collection->parent->id }}" />						
						. . <i class="material-icons">arrow_upward</i>
				</a>
			@endif
			</div><!-- col2 -->
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
		<div class="row text-center">
		   <div class="col-12">
			@if(!empty($column_config->title_search) && $column_config->title_search == 1)
			<form class="inline-form" method="post" action="/collection/{{$collection->id}}/quicktitlefilter">
			@csrf
			<div class="float-container">
		   		<label for="title_search" class="search-label">{{ __('Title search') }}</label>
		   		<input type="text" class="search-field" id="title_search" name="title_filter" placeholder="Add keywords and press enter"/>
			</div>
			</form>
			@endif
			@foreach($meta_fields as $m)
			@if(!empty($column_config->meta_fields_search) && in_array($m->id, $column_config->meta_fields_search))
			<form class="inline-form" method="post" action="/collection/{{$collection->id}}/quickmetafilters">
			@csrf

			@if($m->type == 'Text' || $m->type == 'SelectCombo' || $m->type == 'Numeric' || $m->type == 'Textarea')
			<div class="float-container">
		   	<label for="meta_{{ $m->id }}_search" class="search-label">{{ __($m->label) }}</label>
		   	<input type="text" class="search-field" id="meta_{{ $m->id }}_search" name="meta_value[{{ $m->id }}][]" placeholder="Add keywords and press enter"/>
		   	<input type="hidden" name="meta_field[]" value="{{ $m->id }}" />
		   	<input type="hidden" name="meta_type[]" value="{{ $m->type }}" />
		   	<input type="hidden" name="operator[]" value="contains" />
			<!--/form-->
			</div>
			@elseif($m->type == 'Date')
			<div class="float-container">
		   	<label for="meta_{{ $m->id }}_search" class="search-label">{{ $m->label }}</label>
		   	<input type="text" class="search-field" id="meta_{{ $m->id }}_search" name="meta_value[{{ $m->id }}][]" placeholder="Selete date range and press enter"/>
		   	<input type="hidden" name="meta_field[]" value="{{ $m->id }}" />
		   	<input type="hidden" name="operator[]" value="between" />
		   	<input type="hidden" name="meta_type[]" value="{{ $m->type }}" />
			<script>
				$('#meta_{{ $m->id }}_search').dateRangePicker();
			</script>
			</div>
			@elseif($m->type == 'Select')
			<div class="float-container">
		   	<label for="meta_{{ $m->id }}_search" class="search-label">{{ $m->label }}</label>
		   	<!--select class="selectpicker" id="meta_{{ $m->id }}_search" name="meta_value" onchange="this.form.submit();"-->
		   	<select class="selectpicker" id="meta_{{ $m->id }}_search" title="{{ $m->label }}" name="meta_value[{{ $m->id }}][]" onchange="this.form.submit();">
		            @php
                		$options = explode(",", $m->options);
            		    @endphp
				<!--option value="">{{ $m->label }}</option-->
				@foreach($options as $o)
				<option>{{ $o }}</option>
				@endforeach
			</select>
		   	<input type="hidden" name="meta_field[]" value="{{ $m->id }}" />
		   	<input type="hidden" name="operator[]" value="contains" />
		   	<input type="hidden" name="meta_type[]" value="{{ $m->type }}" />
			</div>
			@elseif($m->type == 'MultiSelect')
			<div class="float-container">
		   	<label for="meta_{{ $m->id }}_search" class="search-label">{{ $m->label }}</label>
		   	<!--select class="selectpicker" id="meta_{{ $m->id }}_search" name="meta_value" onchange="this.form.submit();"-->
		   	<select class="selectpicker" id="meta_{{ $m->id }}_search" title="{{ $m->label }}" name="meta_value[{{ $m->id }}][]" multiple onchange="this.form.submit();">
		            @php
                		$options = explode(",", $m->options);
            		    @endphp
				<!--option>{{ $m->label }}</option-->
				@foreach($options as $o)
				<option>{{ $o }}</option>
				@endforeach
			</select>
		   	<input type="hidden" name="meta_field[]" value="{{ $m->id }}" />
		   	<input type="hidden" name="operator[]" value="contains" />
		   	<input type="hidden" name="meta_type[]" value="{{ $m->type }}" />
			</div>
			@endif
			</form>
			@endif
			@endforeach
			</div>
		</div>
		<div class="row text-center">
		   <div class="col-12">
			<div class="float-container" style="width:100%;">
			<label for="collection_search">{{ __('Start typing to initiate search within the document content') }}</label>
		    <input type="text" class="search-field" id="collection_search" />
			<style>
			.dataTables_filter {
			display: none;
			}
			</style>
		   </div>
		   </div>
		   <div class="col-12 text-center">
           <!--<i class="material-icons">search</i>-->
		   </div>
		</div>
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
                            <th>{{__('Size')}}</th>
                            <th>{{__('Created')}}</th>
			<!-- meta fields -->
				@foreach($collection->meta_fields as $m)
				@if(in_array($m->id,$column_config_meta_fields))
				<th>{{ __($m->label) }}</th>
				@endif
				@endforeach
                <th>@if(env('SHOW_ACTIONS_TH') == 1) Actions @endif</th>
                </tr>
                </thead>
               </table>
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
    });
			@else
			$('#collection_search').keyup(function(){
      			oTable.search($(this).val()).draw() ;
			})
			@endif
		</script>
@endsection
