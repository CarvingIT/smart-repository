@extends('layouts.app',['class' => 'off-canvas-sidebar','title'=>'Smart Repository','activePage'=>'contact','titlePage'=>'Contact Us'])


@section('content')
<main id="main">
@php
	$search_query = Request::get('isa_search_parameter');
function getTree($children, $parent_id = null){
         if(empty($children['parent_'.$parent_id])) return;
         foreach($children['parent_'.$parent_id] as $t){
         $checked = '';
	 if(!empty(Request::get('taxonomy_id')) && in_array($t->id,Request::get('taxonomy_id'))){
		$checked = 'checked';
	 }
             if(!empty($children['parent_'.$t->id]) && count($children['parent_'.$t->id]) > 0){
                  echo '<input type="checkbox" value="'.$t->id.'" name="taxonomy_id[]" onChange="this.form.submit();"'.$checked.' ><label class="form-check-label" for="flexCheckDefault">'.$t->label.'</label><br />';
                  getTree($children, $t->id);
             }
             else{
                  echo '<input type="checkbox" value="'.$t->id.'" name="taxonomy_id[]" onChange="this.form.submit();"'.$checked.'><label class="form-check-label" for="flexCheckDefault">&nbsp; &nbsp;&nbsp;'.$t->label.'</label><br />';
             }
         }
}
@endphp

<!-- ======= Breadcrumbs ======= -->
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
		<div class="card-header card-header-primary">
                <h4 class="card-title ">
            	{{ __('Database') }}
				</h4>
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
			<label for="collection_search">{{ __('Enter search keyword') }}</label>
		    <input type="text" class="search-field" id="collection_search" name="isa_search_parameter" value="{{ $search_query }}" />
		    <input type="hidden" class="search-field" id="collection_id" name="collection_id" value="{{ $collection->id }}"/>
			<style>
			.dataTables_filter {
			display: none;
			}
			</style>
		   </div>
		   </div>
		   <div class="col-12 text-center">
           		<!--<i class="material-icons">search</i>-->
			<input type="submit" value="Search" name="isa_search" class="btn btn-sm btn-primary">
		   </div>
		</div>
		<!--/form-->

		</div>

<!-- End Breadcrumbs -->

<!-- ======= Service Details Section ======= -->
<section id="service-details" class="service-details">
  <div class="container">

	<div class="row gy-4">

	  <div class="col-lg-3">
		<div class="services-list">
		  <a href="#" class="active">By Country</a>
<div class="form-check">
<!--
<input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
<label class="form-check-label" for="flexCheckDefault">
Default checkbox
</label>
-->
@php
$tags = App\Taxonomy::all();

$children = [];
foreach($tags as $t){
  $children['parent_'.$t->parent_id][] = $t;
}
getTree($children);
@endphp
</div>
		  <a href="#">By Theme</a>
<div class="form-check">
		
</div>
		  <a href="#">Filter 1</a>
		  <a href="#">Filter 2</a>
		  <a href="#">Filter 3</a>
		  <a href="#">Filter 4</a>
		</div>

	  
	  </div>

		</form><!-- isa_search form ends -->

	  <div class="col-lg-9">
<div class="row gy-4 pricing-item" data-aos-delay="100">
	@if(!empty($results))
	@foreach($results as $result)
<p><b><a href="/collection/{{ $collection->id }}/document/{{ $result->id }}"><i class="fa fa-file-text" aria-hidden="true"></i>&nbsp; {{ $result->title }}</a></b><br>
		{{-- $result->text_content --}}
		{{ \Illuminate\Support\Str::limit($result->text_content, 250, $end='...') }}
		</p>
	@endforeach
	@else
		{{ __('No results found') }}
	@endif
	</div>
	  </div>

<nav aria-label="Page navigation">
<ul class="pagination justify-content-center">
@php
$total_results_count=0;
$length=2;
$start = empty(Request::get('start'))? 0 : Request::get('start');
if(empty(Request::get('taxonomy_id'))){
$taxonomies = '';
}
$collection_id = $collection->id;
@endphp
@if($start != 0)
<li class="page-item disabled">
  <a class="services-pagination" href="/documents/isa_document_search?isa_search_parameter={{ $search_query }}&collection_id={{ $collection_id }}&taxonomy_id={{ $taxonomies }}&start={{ $start }}&length={{ $length }}" tabindex="-1" aria-disabled="true">&laquo;</a>
</li>
@endif
@for($i=0;$i<=($total_results_count/2);$i++)
<li class="page-item"><a class="services-pagination" href="/documents/isa_document_search?isa_search_parameter={{ $search_query }}&collection_id={{ $collection_id }}&taxonomy_id={{ $taxonomies }}&start={{ $start }}&length={{ $length }}">1</a></li>
@endfor
<!--
<li class="page-item"><a class="services-pagination" href="#">1</a></li>
<li class="page-item"><a class="services-pagination" href="#">2</a></li>
<li class="page-item"><a class="services-pagination" href="#">3</a></li>
-->
@if($start < ($total_results_count - 2))
<li class="page-item">
  <a class="services-pagination" href="/documents/isa_document_search?isa_search_parameter={{ $search_query }}&collection_id={{ $collection_id }}&taxonomy_id={{ $taxonomies }}&start={{ $start }}&length={{ $length }}">&raquo;</a>
</li>
@endif
</ul>
</nav>

	</div>

  </div>
</section><!-- End Service Details Section -->

</main><!-- End #main -->
@endsection
