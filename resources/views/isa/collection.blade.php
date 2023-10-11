@extends('layouts.app',['class' => 'off-canvas-sidebar','title'=>'Smart Repository','activePage'=>'contact','titlePage'=>'Contact Us'])
@push('js')
<script src="/js/jquery-ui.js" defer></script>
<link href="/css/jquery-ui.css" rel="stylesheet">
@endpush
@section('content')
<main id="main">
@php
	// get meta fields of this collection
	$meta_fields = $collection->meta_fields;
	$filters = [];
	foreach($meta_fields as $m){
		if($m->type == 'TaxonomyTree'){
			$filters[] = $m;
		}
	}	

	$search_query = Request::get('isa_search_parameter');
	function getTree($children, $parent_id = null, $meta_id=null){
         if(empty($children['parent_'.$parent_id])) return;
         foreach($children['parent_'.$parent_id] as $t){
         $checked = '';
	 	 if(!empty(Request::get('meta')) && in_array($t->id,Request::get('meta'))){
			$checked = 'checked';
	 		}
        if(!empty($children['parent_'.$t->id]) && count($children['parent_'.$t->id]) > 0){
		if(empty($t->parent_id)){
		echo "By ".$t->label."<br /><br />";
		}
		else{
		// get compare with query string parameter to mark as checked
		echo '<div class="form-check">';
                  echo '<input type="checkbox" value="'.$t->id.'" name="meta_'.$meta_id.'[]" onChange="this.form.submit();" '.$checked.' ><label class="form-check-label" for="flexCheckDefault">'.$t->label.'</label><br />';
		echo '</div>';
		}
                  getTree($children, $t->id, $meta_id);
             }
             else{
			$checked = '';
			if(!empty(Request::get('meta_'.$meta_id)) && in_array($t->id, Request::get('meta_'.$meta_id))){
				$checked = "checked";
			}
		echo '<div class="form-check">';
                  echo '<input type="checkbox" value="'.$t->id.'" name="meta_'.$meta_id.'[]" onChange="this.form.submit();" '.$checked.'><label class="form-check-label" for="flexCheckDefault">'.$t->label.'</label><br />';
		echo '</div>';
             }
         }
}
@endphp

<!-- ======= Breadcrumbs ======= -->
    <div class="row justify-content-center">
		<form name="isa_search" action="/documents/isa_document_search" method="get" id="isa_search">
        <div class="col-md-12">
            <div class="card">
				<div class="card-header card-header-primary">
                	<h4 class="card-title ">{{ __('Database') }}</h4>
            	</div>
			<div class="card-body">
			<div class="row">
                  <div class="col-12 text-right">
                  @if(Auth::user() && Auth::user()->hasPermission($collection->id, 'MAINTAINER'))
                    <a title="{{ __('Manage users of this collection') }}" href="/collection/{{ $collection->id }}/users" class="btn btn-sm btn-primary"><i class="material-icons">people</i></a>
		    	@if($collection->content_type == 'Uploaded documents')	
                    <a title="{{ __('Manage cataloging fields of this collection') }}" href="/collection/{{ $collection->id }}/meta" class="btn btn-sm btn-primary"><i class="material-icons">label</i></a>
                     @elseif($collection->content_type == 'Web resources')	
                    <a title="Manage Sites for this collection" href="/collection/{{ $collection->id }}/save_exclude_sites" class="btn btn-sm btn-primary"><i class="material-icons">insert_link</i></a>
		    @endif
		  @endif
                  @if(Auth::user() && Auth::user()->hasPermission($collection->id, 'CREATE') && $collection->content_type == 'Uploaded documents')
                    <a title="New Document" href="/collection/{{ $collection->id }}/upload" class="btn btn-sm btn-primary"><i class="material-icons">file_upload</i></a>
                    
		  @endif
                 
                  </div>
		        </div>
			</div>
	
			<div class="col-10">
            <p>{{-- $collection->description --}}</p>
			</div>
			<div class="col-2 text-right">
			</div>
		<form name="isa_search" action="/documents/isa_document_search" method="get" id="isa_search">
		@csrf
		<div class="row text-center">
		   <div class="col-12">
			<div class="float-container" style="width:100%;">
			<label for="collection_search">{{ __('Enter search keywords') }}</label>
		    <input type="text" class="search-field" id="collection_search" name="isa_search_parameter" value="{{ $search_query }}" />
		    <input type="hidden" class="search-field" id="collection_id" name="collection_id" value="{{ $collection->id }}" />
			<input type="submit" value="Search" name="isa_search" class="btn btn-sm btn-primary search">
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
$tags = App\Taxonomy::all();

$children = [];
foreach($tags as $t){
  $children['parent_'.$t->parent_id][] = $t;
}
@endphp

<section id="service-details" class="service-details">
  <div class="container">
	<div class="row gy-4">
	  <div class="col-lg-3">
		<div class="services-list">
			<h5>Filter</h5>
		  <!--a href="#" class="active">By Location</a-->
			<!--div class="form-check"-->
				<!--
				<input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
				<label class="form-check-label" for="flexCheckDefault">
				Default checkbox
				</label>
				-->
				@php
				foreach($filters as $f){
					echo '<a href="#" onclick="$(\'#checkboxes_'.$f->id.'\').toggle()">By '.$f->label.'</a>';
					echo '<div id="checkboxes_'.$f->id.'" style="display:none;">';
					getTree($children, $f->options, $f->id);
					echo '</div>';
				}
				@endphp
			<!--/div-->
		  <!--a href="#">By Theme</a-->
		<div class="form-check">
		</div>
		</div>

	  
	  </div>

		</form><!-- isa_search form ends -->

	  <div class="col-lg-9">
<div class="row gy-4 pricing-item" data-aos-delay="100">
	@if(!empty($results))
	@foreach($results as $result)
		@php 
			$document = \App\Document::find($result->id);
			$meta_fields = $document->collection->meta_fields;
			$abstract_field_id = null;
			foreach($meta_fields as $m){
				if(strtolower($m->label) == 'abstract'){
					$abstract_field_id = $m->id;
					break;
				}
			}
		@endphp
<p><b><a href="/collection/{{ $collection->id }}/document/{{ $result->id }}"><i class="fa fa-file-text" aria-hidden="true"></i>&nbsp; {{ $result->title }}</a></b><br>
		@if (!empty($abstract_field_id) && !empty($document->meta_value($abstract_field_id)))
		{{ $document->meta_value($abstract_field_id) }}
		@else
		{{ \Illuminate\Support\Str::limit($document->text_content, 250, $end='...') }}
		@endif
		</p>
	@endforeach
	@else
		{{ __('No results found') }}
	@endif
	</div>
	  </div>

		</div> <!-- card -->
<nav aria-label="Page navigation">
<ul class="pagination justify-content-center">
@php
//$total_results_count=0;
$length=10;
$start = empty(Request::get('start'))? 0 : Request::get('start');
if(empty(Request::get('meta'))){
$taxonomies = '';
}
$collection_id = $collection->id;
@endphp
@if($start > 0)
<li class="page-item disabled">
  <a class="services-pagination" href="/documents/isa_document_search?isa_search_parameter={{ $search_query }}&collection_id={{ $collection_id }}{{ @$meta_query }}&start={{ $start-10 }}&length={{ $length }}" tabindex="-1" aria-disabled="true">&laquo;</a>
</li>
@endif
@if($start < ($filtered_results_count - 10) && count($results) >= 10 )
<li class="page-item">
  <a class="services-pagination" href="/documents/isa_document_search?isa_search_parameter={{ $search_query }}&collection_id={{ $collection_id }}{{ @$meta_query }}&start={{ $start+10 }}&length={{ $length }}">&raquo;</a>
</li>
@endif
</ul>
</nav>

</form>
	</div>

  </div>
</section><!-- End Service Details Section -->

</main><!-- End #main -->
    <script>
	    var botmanWidget = {
			frameEndpoint: '/chatbot-frame.html',
	        aboutText: 'ISA Repository',
			aboutLink: "/",
	        introMessage: "✋ Hello from ISA! <br />I understand these instructions <br/><strong>h</strong> - for the menu listing commands <br /><strong>q</strong> - Ask a question.",
			title: "ISA RRR Chatbot",
			mainColor:"#f05a22",
			bubbleBackground:"#f05a22",
			bubbleAvatarUrl: "/i/chatbot.png",
	    };
    </script>
    <script src='https://cdn.jsdelivr.net/npm/botman-web-widget@0/build/js/widget.js'></script>

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
