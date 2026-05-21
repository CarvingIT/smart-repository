<!-- Font Awesome - served locally via npm (@fortawesome/fontawesome-free) -->
<link rel="stylesheet" href="/vendor/font-awesome/css/all.min.css" />
<div class="search-results-container">
<style>
	.search-result-item {
		padding: 15px 0;
		border-bottom: 1px solid #e8e8e8;
		margin-bottom: 20px;
	}
	.search-result-item:last-child {
		border-bottom: none;
	}
	.result-title {
		font-size: 20px;
		line-height: 1.3;
		margin-bottom: 5px;
		display: flex;
		align-items: center;
		gap: 6px;
	}
	.result-title a {
		color: #f05a22;
		text-decoration: none;
		font-weight: 500;
		margin: 0 !important;
		padding: 0 !important;
	}
	.result-title a:hover {
		text-decoration: underline;
	}
	.result-url {
		color: #006621;
		font-size: 14px;
		margin-bottom: 5px;
	}
	.result-meta-info {
		color: #5f6368;
		font-size: 13px;
		margin-bottom: 8px;
	}
	.result-meta-info span {
		margin-right: 15px;
	}
	.result-meta-info .meta-date {
		display: inline-flex;
		align-items: center;
		gap: 4px;
	}
	.result-meta-info .meta-date-icon {
		font-size: 16px;
		line-height: 1;
		color: #5f6368;
		margin: 0 !important;
		padding: 0 !important;
	}
	.result-description {
		color: #4d5156;
		font-size: 14px;
		line-height: 1.6;
		margin-bottom: 10px;
	}
	.result-description strong {
		font-weight: 600;
		color: #202124;
		background-color: #ffeb3b;
		padding: 2px 0;
	}
	.result-title .highlight {
		background-color: #ffeb3b;
		font-weight: 600;
		padding: 2px 0;
	}
	.result-tags {
		margin-top: 8px;
	}
	.tag {
		display: inline-block;
		background-color: #f0f0f0;
		color: #5f6368;
		padding: 4px 10px;
		margin: 3px 5px 3px 0;
		border-radius: 3px;
		font-size: 12px;
		border: 1px solid #dadce0;
	}
	.tag:hover {
		background-color: #e8e8e8;
	}
	.file-type-icon {
		color: #f05a22 !important;
		margin-right: 8px;
		font-size: 18px !important;
		display: inline-block !important;
		vertical-align: middle !important;
		width: 1em !important;
		height: 1em !important;
		line-height: 1 !important;
	}
	.result-title .title-type-icon {
		color: #f05a22;
		font-size: 18px;
		line-height: 1;
		margin: 0 !important;
		padding: 0 !important;
		flex: 0 0 auto;
	}
	
	/* Ensure all Font Awesome icons display */
	.fa, .fas, .far, .fal, .fad, .fab {
		font-family: 'Font Awesome 6 Free' !important;
		font-weight: 900 !important;
		display: inline-block !important;
		font-style: normal !important;
		font-variant: normal !important;
		text-rendering: auto !important;
		line-height: 1 !important;
		-webkit-font-smoothing: antialiased !important;
		-moz-osx-font-smoothing: grayscale !important;
	}
	.fa-file-alt:before {
		content: "\f15c" !important;
	}
	
	.fa-external-link-alt:before {
		content: "\f35d" !important;
	}
	
	.fa-search:before {
		content: "\f002" !important;
	}
	
	.fa-clock-o:before, .fa-clock:before {
		content: "\f017" !important;
	}
</style>

	@if (!empty($results))
	@php
	$collection_config = json_decode($collection->column_config);
	$use_custom_template = !empty($collection_config->use_custom_template) && $collection_config->use_custom_template == 1;
	// When custom template is active, load the Search Result template (classic theme only)
	if($use_custom_template){
		$template_code = \App\SRTemplate::
			where('collection_id',$collection->id)
			->where('template_type','search_result')
			->first();
		if(!empty($template_code->html_code)){
			$html_code = $template_code->html_code;
		}
	}
	@endphp
	@foreach($results as $result)
		@php 
			$document = \App\Document::find($result->id);
			$meta_fields = $document->collection->meta_fields;
			
			$highlight = @$highlights[$document->id];
			$highlight_serialized = serialize($highlight);
			preg_match_all('#<em>(.*?)</em>#',$highlight_serialized, $matches);
			array_shift($matches);
			$highlight_keywords = $matches;	
			$entered_keywords = explode(' ',$search_query);
			$highlight_keywords = array_merge($highlight_keywords[0], $entered_keywords);

			$collection_config = json_decode($document->collection->column_config);
			$result_title = empty($collection_config->replace_title_with_meta) ? $document->title : $document->meta_value($collection_config->replace_title_with_meta);
			
			// Extract description snippet from text content
			$description = '';
			if (!empty($document->text_content)) {
				$description = strip_tags($document->text_content);
				// Limit to first 300 characters
				if (strlen($description) > 300) {
					$description = mb_substr($description, 0, 300) . '...';
				}
			}
		@endphp
		
		<div class="search-result-item">
			@if(!empty($html_code))
				{!! \App\Util::renderDocumentTemplate($html_code, $document, $collection) !!}
			@else
				<!-- Title -->
				<div class="result-title">
					@if (@$result->type == 'url')
						<i class="material-icons title-type-icon">link</i>
					@else
						<i class="material-icons title-type-icon">description</i>
					@endif
					@php
						// Highlight keywords in title
						$highlighted_title = strip_tags($result_title);
						if (!empty($search_query)) {
							foreach ($highlight_keywords as $keyword) {
								if (!empty($keyword)) {
									$highlighted_title = preg_replace('/(' . preg_quote($keyword, '/') . ')/i', '<span class="highlight">$1</span>', $highlighted_title);
								}
							}
						}
					@endphp
					<a href="/collection/{{ $collection->id }}/document/{{ $result->id }}/details">{!! $highlighted_title !!}</a>
				</div>
				
				<!-- URL/Path -->
				<div class="result-url">
					@if (@$result->type == 'url')
						{{ $document->path }}
					@else
						{{ env('APP_URL') }}/collection/{{ $collection->id }}/document/{{ $result->id }}/details
					@endif
				</div>
				
				<!-- Meta Information -->
				<div class="result-meta-info">
					@foreach ($meta_fields as $m)
						@if (!empty($m->results_display_order) && !empty($document->meta_value($m->id)))
							@php
								$meta_value = strip_tags($document->meta_value($m->id));
								if (!empty($search_query)) {
									foreach ($highlight_keywords as $keyword) {
										if (!empty($keyword)) {
											$meta_value = preg_replace('/(' . preg_quote($keyword, '/') . ')/i', '<span class="highlight">$1</span>', $meta_value);
										}
									}
								}
							@endphp
							<span><strong>{{ $m->label }}:</strong> {!! $meta_value !!}</span>
						@endif
					@endforeach
					@if (!empty($document->updated_at))
						<span class="meta-date"><i class="material-icons meta-date-icon">schedule</i>{{ date('d M Y', strtotime($document->updated_at)) }}</span>
					@endif
				</div>
				
				<!-- Description/Snippet -->
				@if (!empty($search_query) && !empty($document->text_content))
					<div class="result-description">
						@php
							// Get contextual snippet where keyword appears
							$full_text = strip_tags($document->text_content);
							$content_snippet = '';
							$snippet_found = false;
							
							// Find the first occurrence of any keyword in the text
							$keyword_position = -1;
							$found_keyword = '';
							
							foreach ($highlight_keywords as $keyword) {
								if (!empty($keyword) && strlen($keyword) > 2) {
									$pos = stripos($full_text, $keyword);
									if ($pos !== false && ($keyword_position == -1 || $pos < $keyword_position)) {
										$keyword_position = $pos;
										$found_keyword = $keyword;
										$snippet_found = true;
									}
								}
							}
							
							if ($snippet_found && $keyword_position !== -1) {
								// Extract context around the keyword (150 chars before and after)
								$context_length = 150;
								$start = max(0, $keyword_position - $context_length);
								$length = min(strlen($full_text) - $start, $context_length * 2 + strlen($found_keyword));
								
								$content_snippet = mb_substr($full_text, $start, $length);
								
								// Add ellipsis if we're not at the start/end
								if ($start > 0) {
									$content_snippet = '... ' . $content_snippet;
								}
								if ($start + $length < strlen($full_text)) {
									$content_snippet = $content_snippet . ' ...';
								}
							} else {
								// Fallback: show first 300 characters if keyword not found
								$content_snippet = mb_substr($full_text, 0, 300);
								if (strlen($full_text) > 300) {
									$content_snippet .= ' ...';
								}
							}
							
							// Highlight all keywords in the snippet
							foreach ($highlight_keywords as $keyword) {
								if (!empty($keyword) && strlen($keyword) > 1) {
									$content_snippet = preg_replace('/(' . preg_quote($keyword, '/') . ')/iu', '<strong>$1</strong>', $content_snippet);
								}
							}
						@endphp
						{!! $content_snippet !!}
					</div>
				@elseif (!empty($description))
					<div class="result-description">
						{{ $description }}
					</div>
				@endif
				
				<!-- Tags (if meta fields have specific display) -->
				<div class="result-tags">
					@foreach ($meta_fields as $m)
						@if ($m->type == 'TaxonomyTree' && !empty($document->meta_value($m->id)))
							@php
								$tag_values = explode(',', strip_tags($document->meta_value($m->id)));
								foreach($tag_values as $tag_val) {
									$tag_val = trim($tag_val);
									if(!empty($tag_val)) {
										echo '<span class="tag">' . $tag_val . '</span>';
									}
								}
							@endphp
						@endif
					@endforeach
				</div>
			@endif
		</div>
	@endforeach
	
	<!-- Pagination -->
	<div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e8e8e8;">
		<nav aria-label="Page navigation" style="text-align:center; width:100%;">
			<div style="color: #5f6368; margin-bottom: 15px; font-size: 14px;">
					Showing <span id="filtered-results-count">{{ $filtered_results_count }}</span> results
			</div>
			@php
			$length = 10;
			$start = intval(Request::get('start'));
			$total_pages = ceil($filtered_results_count / $length);
			$current_page = floor($start / $length) + 1;
			// Windowed pagination: show at most 9 pages centred around current page
			$window = 4; // pages on each side of current
			$page_from = max(1, $current_page - $window);
			$page_to   = min($total_pages, $current_page + $window);
			@endphp
			<ul class="pagination" style="justify-content: center; flex-wrap: wrap;">
				{{-- Previous button --}}
				@if($current_page > 1)
				<li class="page-item">
					<a class="services-pagination" href="javascript:void(0);" onclick="previousPage(); return false;" tabindex="-1">&laquo; Prev</a>
				</li>
				@endif

				{{-- First page + ellipsis --}}
				@if($page_from > 1)
					<li class="page-item">
						<a class="services-pagination" href="javascript:void(0);" onclick="goToPage(1); return false;">1</a>
					</li>
					@if($page_from > 2)
						<li class="page-item disabled"><span class="services-pagination" style="cursor:default;">…</span></li>
					@endif
				@endif

				{{-- Windowed page numbers --}}
				@for ($p = $page_from; $p <= $page_to; $p++)
					<li class="page-item @if ($current_page == $p) active @endif">
						<a class="services-pagination" href="javascript:void(0);" onclick="goToPage({{ $p }}); return false;">{{ $p }}</a>
					</li>
				@endfor

				{{-- Ellipsis + Last page --}}
				@if($page_to < $total_pages)
					@if($page_to < $total_pages - 1)
						<li class="page-item disabled"><span class="services-pagination" style="cursor:default;">…</span></li>
					@endif
					<li class="page-item">
						<a class="services-pagination" href="javascript:void(0);" onclick="goToPage({{ $total_pages }}); return false;">{{ $total_pages }}</a>
					</li>
				@endif

				{{-- Next button --}}
				@if($current_page < $total_pages)
					<li class="page-item">
						<a class="services-pagination" href="javascript:void(0);" onclick="nextPage(); return false;">Next &raquo;</a>
					</li>
				@endif
			</ul>
		</nav>
	</div>
	@else
		<div style="text-align: center; padding: 40px 20px; color: #5f6368;">
			<span style="font-size: 48px; color: #dadce0; margin-bottom: 15px; font-family: 'Font Awesome 6 Free', FontAwesome; font-weight: 900;">&#xf002;</span>
			<h3 style="color: #202124; font-size: 20px; margin-bottom: 10px;">{{ __('No results found') }}</h3>
			<p style="font-size: 14px;">Try different keywords or remove search filters</p>
		</div>
	@endif

</div>

