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
	if($column_config->type != 1) $hide_type = true;
	if($column_config->title != 1) $hide_title = true;
	if($column_config->size != 1) $hide_size = true;
	if($column_config->creation_time != 1) $hide_creation_time = true;
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
		if(!empty($column_config->meta_fields)){
		foreach($collection->meta_fields as $m){
			$visible = 'false';
			if(in_array($m->id, $column_config->meta_fields)){
				$visible = 'true';
			}
			echo '{ "targets":['.$i.'], "className":"text-right", "sortable":false, "visible":'.$visible.' },';
			$i++;
		}
		}
		@endphp	
		{ "targets":[{{ $i }}], "visible":true, "sortable":false, "className":'td-actions text-right dt-nowrap'},
     ],
    "processing":true,
    "order": [[ 3, "desc" ]],
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
		@foreach($collection->meta_fields as $m)
		{data:"meta_{{$m->id}}"},
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
            	<a href="/collections">{{ __('Collections') }}</a> :: {{ $collection->name }}
		</h4>
            </div>
        <div class="card-body">
		<div class="row">
                  <div class="col-12 text-right">
                  @if(Auth::user() && Auth::user()->hasPermission($collection->id, 'MAINTAINER'))
                    <a title="Manage Users of this collection" href="/collection/{{ $collection->id }}/users" class="btn btn-sm btn-primary"><i class="material-icons">people</i></a>
		    @if($collection->content_type == 'Uploaded documents')	
                    <a title="Manage meta information fields of this collection" href="/collection/{{ $collection->id }}/meta" class="btn btn-sm btn-primary"><i class="material-icons">label</i></a>
                    <a title="Show/hide columns in list view" href="/collection/{{ $collection->id }}/column-config" class="btn btn-sm btn-primary"><i class="material-icons">settings</i></a>
		    @elseif($collection->content_type == 'Web resources')	
                    <a title="Manage Sites for this collection" href="/collection/{{ $collection->id }}/save_exclude_sites" class="btn btn-sm btn-primary"><i class="material-icons">insert_link</i></a>
		    @endif
		  @endif
                  @if(Auth::user() && Auth::user()->hasPermission($collection->id, 'CREATE') && $collection->content_type == 'Uploaded documents')
                    <a title="New Document" href="/collection/{{ $collection->id }}/upload" class="btn btn-sm btn-primary"><i class="material-icons">add</i></a>
		  @endif
                  @if(count($collection->meta_fields)>0)
                    <a href="/collection/{{ $collection->id }}/metafilters" title="Set Filters" class="btn btn-sm btn-primary"><i class="material-icons">filter_list</i></a>
                  @endif
                  </div>
        </div>
            <p>{{ $collection->description }}</p>
        <p>
        @php
            $meta_fields = $collection->meta_fields;
            $meta_labels = array();
            foreach($meta_fields as $m){
                $meta_labels[$m->id] = $m->label;
            }
            $all_meta_filters = Session::get('meta_filters');
        @endphp
        @if(count($meta_fields)>0 && !empty($all_meta_filters[$collection->id]))
            <strong>Current Filters:</strong>
        @foreach( $all_meta_filters[$collection->id] as $m)
            <span class="filtertag">
            {{ $meta_labels[$m['field_id']] }} {{ $m['operator'] }} <i>{{ $m['value'] }}</i>
                <a class="removefiltertag" title="remove" href="/collection/{{ $collection->id }}/removefilter/{{ $m['filter_id'] }}">
                <i class="tinyicon material-icons">delete</i>
                </a>
                </span>
        @endforeach
        @endif
        </p>

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
		@if(!empty(env('TRANSLITERATION')))
		   <div class="col-12 text-right">
		   <input type="text" id="collection_search" placeholder="Search" />
			<style>
			.dataTables_filter {
			display: none;
			}
			</style>
		   </div>
		<script>
			let searchbox = document.getElementById("collection_search");
			enableTransliteration(searchbox, '{{ env('TRANSLITERATION') }}');
			$('#collection_search').keyup(function(){
      			oTable.search($(this).val()).draw() ;
			})
		</script>
		@endif
		   <div class="table-responsive">
                    <table id="documents" class="table">
                        <thead class="text-primary">
                            <tr>
                            <th>Type</th>
                            <th>Title</th>
                            <th>Size</th>
                            <th>Created</th>
							<!-- meta fields -->
							@foreach($collection->meta_fields as $m)
							<th>{{ $m->label }}</th>
							@endforeach
                            <th>Actions</th>
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
@endsection
