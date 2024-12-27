<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<div class="row gy-4 pricing-item" data-aos-delay="100">
	<div class="col-lg-12 text-right">
	</div>
<style>
	.tag{
		display:inline-block;
		margin-top:2px;
		background-color:#ccc;
		margin-right:5px;
		padding:3px;
  		border-top-left-radius: 10px;
  		border-bottom-left-radius: 10px;
	}
	.parent-tag{
		background-color:#aaa;
	}

.Draft {
  color: grey;
  padding-right: 7px;
}

.Active {
  color: green;
  padding-right: 7px;
}

.Repealed {
  color: red;
  padding-right: 7px;
}


h4 {
  display: flex;
  justify-content: start;
  align-items: center;
}
h4 a {
  color: #3f819e;
  text-decoration: none; 
}
</style>
	@if (!empty($results))
	@foreach($results as $result)
		@php 
			$document = \App\Document::find($result->id);
			$meta_fields = $document->collection->meta_fields;
			$abstract_field_id = null;
			$country_field_id = null;
			$year_field_id = null;
			$govt_agency_field_id = null;
			$theme_field_id = null;
			$author_field_id = null;
			$serial_num_field_id = null;
			
			$highlight = @$highlights[$document->id];
			$highlight_serialized = serialize($highlight);
			preg_match_all('#<em>(.*?)</em>#',$highlight_serialized, $matches);
			array_shift($matches);
			$highlight_keywords = $matches;	
			$entered_keywords = explode(' ',$search_query);
			$highlight_keywords = array_merge($highlight_keywords[0], $entered_keywords);

			$collection_config = json_decode($document->collection->column_config);
			$result_title = empty($collection_config->replace_title_with_meta) ? $document->title : $document->meta_value($collection_config->replace_title_with_meta);
		@endphp
		<div class="row">
		<h4>
		@if (@$result->type == 'url')
		<!--a href="/collection/{{ $collection->id }}/document/{{ $result->id }}/details"><i class="fa fa-external-link" aria-hidden="true"></i>&nbsp;{!! strip_tags($result_title) !!}</a-->
		@else
		<!--a href="/collection/{{ $collection->id }}/document/{{ $result->id }}/details"><i class="fa fa-file-text" aria-hidden="true"></i>&nbsp;{!! strip_tags($result_title) !!}</a-->
		@endif
		</h4>
		</div>
		<div class="row">
				@php $display_meta = [];@endphp
			   @foreach ($meta_fields as $m)
				@php 
					$placeholder = strtolower($m->placeholder);
					$meta_placeholder = preg_replace("/ /","-",$placeholder);
					$display_meta[$meta_placeholder]=$document->meta_value($m->id);
				@endphp
			   @endforeach		
				@php
					$result_title = strip_tags($result_title);
				@endphp
					<div class="row">
					<div class="col-lg-12">
						<h4 class="d-flex justify-content-start align-items-center">
        					<span class='{{ $display_meta['status'] }}' >
            					<i class="fa fa-file"></i>
        					</span>
        					<a href='/collection/{{ $collection->id }}/document/{{ $result->id }}/details'>{{ $result_title }}</a>
						</h4>
					</div>
					</div>

					@if(!empty($display_meta['state-name']) || !empty($display_meta['issuing-authority']))
					<div class="col-lg-4">
						<i class="fa fa-globe" style="margin-right: 5px;"></i>{{ $display_meta['state-name'] }} ({{ $display_meta['issuing-authority'] }})
					</div>
					@endif
					@if(!empty($display_meta['date-of-issuance']))
					<div class="col-lg-4">
						<i class="fa fa-calendar" style="margin-right: 5px;"></i>{{ $display_meta['date-of-issuance'] }}
					</div>
					@endif
					<div class="row">
					@if(!empty($display_meta['sector']))
					  <div class="col-lg-4">
					    <i class="fa fa-tag" style="margin-right: 5px;"></i> {{ $display_meta['sector'] }}
					  </div>
					@endif
					@if(!empty($display_meta['document-type']))
					  <div class="col-lg-4">
					    <i class="fa fa-tag" style="margin-right: 5px;"></i>{{ $display_meta['document-type'] }}
					  </div>
					</div>
					@endif
					<div class="row">&nbsp;</div>

					<div>&nbsp;</div>
		</div><!-- row -->

		<div class="row">
		<div class="col-lg-12">
			@if (!empty($search_query))
			{!! implode('', App\Util::highlightKeywords($document->text_content, implode(' ',$highlight_keywords))) !!}		
			@endif
		</div>
		</div>
	@endforeach
		<div class="row">

<nav aria-label="Page navigation" style="text-align:center; width:100%;">
<div class="pagination">Filtered {{ $filtered_results_count }} of {{ $total_results_count }}</div>
<ul class="pagination">
@php
//$total_results_count=0;
$length=10;
$start = empty(Request::get('start'))? 0 : Request::get('start');
if(empty(Request::get('meta'))){
$taxonomies = '';
}
$collection_id = $collection->id;

$total_pages = ($filtered_results_count / 10) + (($filtered_results_count%10 === 0) ? 0 : 1);
@endphp
@if($start > 0)
<li class="page-item disabled">
  <a class="services-pagination" href="javascript:void(0);" onclick="previousPage()" tabindex="-1" aria-disabled="true">&laquo;</a>
</li>
@endif
@for ($p=1; $p<=$total_pages; $p++)
<li class="page-item @if (($start+10)/10 == $p) {{ 'current-page' }} @endif">
  <a class="services-pagination" href="javascript:void(0);" onclick="goToPage({{ $p }})">{{ $p }}</a>
</li>
@endfor
@if($start < ($filtered_results_count - 10) && count($results) >= 10 )
<li class="page-item">
  <a class="services-pagination" href="javascript:void(0);" onclick="nextPage()">&raquo;</a>
</li>
@endif
</ul>
</nav>
</div>
	@else
		{{ __('No results found') }}
		
	@endif

	</div>

