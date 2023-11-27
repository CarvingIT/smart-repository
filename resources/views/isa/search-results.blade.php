<div class="row gy-4 pricing-item" data-aos-delay="100">
	<div class="col-lg-12 text-right">
	<a href="#" onclick="clearFilters();">Clear All Filters</a>
	</div>
<style>
	.tag{
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
	@php
		$major_themes = explode("|",env('MAJOR_THEMES', 'A|B|C'));
		$major_theme_ids = [];
		$taxonomy = \App\Taxonomy::orderBy('parent_id','ASC')->orderBy('label', 'ASC')->get()->keyBy('id');
		$children = [];
		$taxonomy_ordered_by_id = [];
		foreach($taxonomy as $t){
			$children[$t->parent_id][] = $t->id; 
			$taxonomy_ordered_by_id[] = $t->id;
			if(in_array($t->label, $major_themes)){
				$major_theme_ids[] = $t->id;
			}
		}
		print_r($major_theme_ids);
		function orderByHierarchy($taxonomy_ordered_by_id, $my_ids){
			$ordered = [];
			foreach($taxonomy_ordered_by_id as $t_id){
				if(in_array($t_id, $my_ids)){
					$ordered[] = $t_id;
				}
			}
			return $ordered;
		}
		function removeContinents($string){
			$continents = ['Asia','Africa','Europe','North America','South America', 'Oceania'];
			$str_values = explode(",", $string);
			$new_str_values = [];
			foreach($str_values as $v){
				if (!in_array(ltrim(rtrim($v)), $continents)) $new_str_values[] = $v;
			}
			return implode(", ",$new_str_values);
		}

		function distinguishMajorThemes($string){
			$major_themes = explode("|",env('MAJOR_THEMES', 'A|B|C'));
			foreach($major_themes as $t){
				$string = str_replace($t, '<em class="mt">'.$t.'</em>', $string);
			}
			return $string;
		}
	@endphp
	@if(!empty($results))
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
				else if($m->label == env('GOVT_AGENCY_FIELD_LABEL','Agency')){
					$govt_agency_field_id = $m->id;
				}
				else if($m->label == env('THEME_FIELD_LABEL','Theme')){
					$theme_field_id = $m->id;
				}
				else if($m->label == env('AUTHOR_FIELD_LABEL','Author')){
					$author_field_id = $m->id;
				}
				else if($m->label == env('SERIAL_NUMBER_FIELD_LABEL','Author')){
					$serial_num_field_id = $m->id;
				}
			}
			$highlight = @$highlights[$document->id];
			$highlight_serialized = serialize($highlight);
			preg_match_all('#<em>(.*?)</em>#',$highlight_serialized, $matches);
			array_shift($matches);
			$highlight_keywords = $matches;	
			$entered_keywords = explode(' ',$search_query);
			//print_r($highlight_keywords);
			//print_r($entered_keywords);
			$highlight_keywords = array_merge($highlight_keywords[0], $entered_keywords);
			//print_r($highlight_keywords); exit;
		@endphp
		<div class="row">
		<h4>
		@if (@$result->type == 'url')
		<a href="/collection/{{ $collection->id }}/document/{{ $result->id }}/details"><i class="fa fa-external-link" aria-hidden="true"></i>&nbsp;{!! strip_tags($document->title) !!}</a>
		@else
		<a href="/collection/{{ $collection->id }}/document/{{ $result->id }}/details"><i class="fa fa-file-text" aria-hidden="true"></i>&nbsp;{!! strip_tags($document->title) !!}</a>
		@endif
		</h4>
		</div>
		<div class="row">
		<div class="col-lg-2">
		@if (!empty($document->meta_value($year_field_id)))
			<span class="search-result-meta">
			<i class="fa fa-calendar" aria-hidden="true"></i>&nbsp;{{ $document->meta_value($year_field_id) }}
			</span>
		@endif
		</div>
		<div class="col-lg-10">
		{{-- for publications --}}
		@if (!empty($document->meta_value($author_field_id)))
			<i class="fa fa-users" aria-hidden="true"></i>
			<span class="search-result-meta">
			{!! $document->meta_value($author_field_id) !!}
			</span>
		@endif
		{{-- for technical standards --}}
		@if (!empty($document->meta_value($serial_num_field_id)))
			<i class="fa fa-bars" aria-hidden="true"></i>
			<span class="search-result-meta">
			{{ $document->meta_value($serial_num_field_id) }}
			</span>
		@endif
		</div>
		<div class="col-lg-12">
		@if (!empty($document->meta_value($govt_agency_field_id)))
			<i class="fa fa-university" aria-hidden="true"></i>
			<span class="search-result-meta">
			{!! $document->meta_value($govt_agency_field_id) !!}
			</span>
		@endif
		</div>
		<div class="col-lg-12">
		@if (!empty($document->meta_value($country_field_id)))
			<i class="fa fa-globe" aria-hidden="true"></i>
			<span class="search-result-meta">
			{{ removeContinents($document->meta_value($country_field_id)) }}
			</span>
		@endif
		</div>
		</div><!-- row -->
		<div class="row">
		<div class="col-lg-12">
			@if (empty($search_query))
			@if (!empty($abstract_field_id) && !empty($document->meta_value($abstract_field_id)))
			{!! \Illuminate\Support\Str::limit(ltrim(rtrim(strip_tags(html_entity_decode($document->meta_value($abstract_field_id))))),
				250, $end='...') 
			!!}
			@else
			{{ \Illuminate\Support\Str::limit($document->text_content, 250, $end='...') }}
			@endif
			@else
			{!! implode('', App\Util::highlightKeywords($document->text_content, implode(' ',$highlight_keywords))) !!}		
			@endif
		</div>
		</div>
		<div class="row">
		<div class="col-lg-12">
		@if (!empty($document->meta_value($theme_field_id)))
			<span class="search-result-meta">
			{{-- distinguishMajorThemes($document->meta_value($theme_field_id)) --}} 
			@php
				$taxonomy_values = json_decode($document->meta_value($theme_field_id, true));
				foreach(orderByHierarchy($taxonomy_ordered_by_id, $taxonomy_values) as $t){
					$parent = in_array($taxonomy[$t]->id, $major_theme_ids) ? 'parent-tag' : '';
					echo '<span class="tag '.$parent.'"><i class="fa fa-tag" aria-hidden="true"></i>
					'.$taxonomy[$t]->label.'</span>';
				}
			@endphp
			</span>
			<p>
			</p>
		@endif
		</div>
		</div>
	@endforeach
		<div class="row">

<nav aria-label="Page navigation" style="text-align:center;">
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

