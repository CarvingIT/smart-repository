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
</style>
	@if (!empty($results))
	@php
	$template_code = \App\SRTemplate::
		where('collection_id',$collection->id)
		->where('template_name','Search Result')
		->first();
	if(!empty($template_code->html_code)){
	$html_code = $template_code->html_code;
	}		
	@endphp
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
			@if(!empty($html_code))
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
					//$formatted_data = \App\Util::replacePlaceHolder($display_meta, $html_code, $result_title, $collection->id, $result->id, @$result->type);	
					$formatted_data = \App\Util::renderBlade($html_code, $display_meta, $result_title, $collection->id, $result->id, @$result->type);	
					echo $formatted_data;
					//echo Blade::render($html_code);
				@endphp


			@else
			   @foreach ($meta_fields as $m)
				@if (empty($m->results_display_order)) 
					@continue
				@else
					@php
						$extra_attributes = json_decode($m->extra_attributes);
						$w = @$extra_attributes->width_on_info_page;
						$classname = @$extra_attributes->results_classname;
					@endphp
					@if (!empty($document->meta_value($m->id)))
					{{-- $m->label--}}
					<div class="col-lg-{{ $w }}">
					<div class="{{ $classname }}">&nbsp;</div>
						@if ($m->type == 'Textarea')
						{{ $document->meta_value($m->id) }}
						@else
						<strong>{{ $document->meta_value($m->id) }}</strong>
						@endif
					</div>
					@endif
				@endif
			   @endforeach
			@endif
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

