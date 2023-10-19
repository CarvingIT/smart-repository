<div class="row gy-4 pricing-item" data-aos-delay="100">
	@if(!empty($results))
	@foreach($results as $result)
		@php 
			$document = \App\Document::find($result->id);
			$meta_fields = $document->collection->meta_fields;
			$abstract_field_id = null;
			$country_field_id = null;
			$year_field_id = null;
			foreach($meta_fields as $m){
				if($m->label == env('ABSTRACT_FIELD_LABEL','Abstract')){
					$abstract_field_id = $m->id;
				}
				else if($m->label == env('COUNTRY_FIELD_LABEL','Country')){
					$country_field_id = $m->id;
				}
				else if($m->label == env('YEAR_FIELD_LABEL','Year')){
					$year_field_id = $m->id;
				}
			}
		@endphp
		<div class="row">
		<a href="/collection/{{ $collection->id }}/document/{{ $result->id }}/details"><i class="fa fa-file-text" aria-hidden="true"></i>&nbsp; {!! $result->title !!}</a>
		</div>
		<div class="row">
		<div class="col-lg-9">
		@if (!empty($document->meta_value($country_field_id)))
			<span class="search-result-meta">
			{{ env('COUNTRY_FIELD_LABEL','Country').': '.$document->meta_value($country_field_id) }}
			</span>
		@endif
		</div>
		<div class="col-lg-3">
		@if (!empty($document->meta_value($year_field_id)))
			<span class="search-result-meta">
			{{ env('YEAR_FIELD_LABEL','Year').': '.$document->meta_value($year_field_id) }}
			</span>
		@endif
		</div>
		</div>
		<div class="row">
		<div class="col-lg-12">
			@if (empty($search_query))
			<p>
			@if (!empty($abstract_field_id) && !empty($document->meta_value($abstract_field_id)))
			{!! \Illuminate\Support\Str::limit(ltrim(rtrim(strip_tags(html_entity_decode($document->meta_value($abstract_field_id))))),
				250, $end='...') 
			!!}
			@else
			{{ \Illuminate\Support\Str::limit($document->text_content, 250, $end='...') }}
			@endif
			</p>
			@else
			<p>
			{!! implode('', App\Util::highlightKeywords($document->text_content, $search_query)) !!}		
			</p>
			@endif
		</div>
		</div>
		<div class="row">&nbsp;</div>
	@endforeach
	@else
		{{ __('No results found') }}
	@endif

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
  <a class="services-pagination" href="#" onclick="previousPage()" tabindex="-1" aria-disabled="true">&laquo;</a>
</li>
@endif
@if($start < ($filtered_results_count - 10) && count($results) >= 10 )
<li class="page-item">
  <a class="services-pagination" href="#" onclick="nextPage()">&raquo;</a>
</li>
@endif
</ul>
</nav>

	</div>

