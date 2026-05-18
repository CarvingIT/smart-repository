<!DOCTYPE html>
<head>
<title>{{ env('APP_NAME', 'Smart Repository') }}::Document Viewer</title>
<link rel="icon" type="image/png" href="/material/img/favicon.png">
<script src="/build/assets/js/jquery.min.js"></script>
@php
    // Document model is passed directly from the controller as $doc
    $collection = \App\Collection::find($collection_id);
    $column_config = json_decode($collection->column_config);
    $pdf_viewer = @$column_config->pdf_viewer ?? 'viewerjs';
@endphp
@if($pdf_viewer === 'dearflip')
<!-- Set DearFlip Location before loading the script -->
<script>
    var dFlipLocation = "/js/dearflip/dflip/";
</script>
<!-- DearFlip Flipbook StyleSheet -->
<link href="/js/dearflip/dflip/css/dflip.min.css" rel="stylesheet" type="text/css">
<!-- DearFlip Icons Stylesheet -->
<link href="/js/dearflip/dflip/css/themify-icons.min.css" rel="stylesheet" type="text/css">
@endif
<style>
html, body {
  height: 100%;
}
.row {
  display:flex;
  overflow:clip;
  height:100%;
}
#meta-view {
  width:30%;
  overflow:auto;
  resize:horizontal;
}
#doc-view {
  width:70%;
  overflow:hidden;
  resize:horizontal;
}
._df_book {
  width: 100%;
  height: 100vh;
}
body{
margin:0;
font-family:"Roboto", "Helvetica", "Arial", sans-serif;
font-size:1em;
font-size:0.8em;
background-color:#eee;
}
label{
color:#666;
}
h4{
    display:block;
    position:sticky;
    top:0;
    background-color:#9124a3;
    color:#fff;
    margin:0;
    padding:9px;
    font-family:sans-serif;
    border-top:2px solid #999;
    line-height:14px;
    font-weight:normal;
    font-size:15px;
    color:#eee;
    box-shadow:2px 2px 3px #666;
}
#meta-data{
    padding:1em;
}
</style>
</head>
<body>
<div class="row">
<div id="doc-view">
@if($pdf_viewer === 'dearflip')
    <div id="df_document_viewer" style="width:100%; height:100%;"></div>
    <!-- DearFlip main Js file -->
    <script src="/js/dearflip/dflip/js/dflip.min.js" type="text/javascript"></script>
    <script>
    // Wait for DOM and dflip to be ready
    jQuery(document).ready(function($) {
        var pdfUrl = "{{ !is_null($path_count) ? '/collection/'.$collection_id.'/document/'.$doc->id.'/details/'.$path_count : '/collection/'.$collection_id.'/document/'.$doc->id }}";
        
        console.log("DearFlip: Initializing with PDF URL:", pdfUrl);
        console.log("DearFlip: DFLIP object available:", typeof DFLIP !== 'undefined');
        
        if (typeof DFLIP !== 'undefined') {
            var options = {
                source: pdfUrl,
                webgl: true,
                height: '100%',
                backgroundColor: '#f5f5f5',
                scrollWheel: true,
                autoEnableOutline: false,
                autoEnableThumbnail: false,
                overwritePDFOutline: false,
                duration: 800,
                // Pass PDF.js options via docParameters to avoid requiring 'unsafe-eval' in CSP
                // Setting isEvalSupported: false makes PDF.js use alternative code paths
                // that don't require eval(), allowing stricter CSP policies
                docParameters: {
                    url: pdfUrl,
                    isEvalSupported: false
                },
                onReady: function(flipbook) {
                    console.log("DearFlip: Flipbook ready!");
                },
                onFlip: function(flipbook) {
                    console.log("DearFlip: Page flipped");
                },
                //enableDownload: {{ empty($column_config->document_viewer_download) ? 'false' : 'true' }}
                @if(auth()->user() && auth()->user()->hasPermission($collection_id,'MAINTAINER'))
                enableDownload: {{ 'true' }}
                @else
                enableDownload: {{ empty($column_config->document_viewer_download) ? 'false' : 'true' }}
                @endif
            };
            
            console.log("DearFlip: Creating flipbook with options:", options);
            var flipbook = $("#df_document_viewer").flipBook(pdfUrl, options);
        } else {
            console.error("DearFlip: DFLIP library not loaded!");
        }
    });
    </script>
@elseif($pdf_viewer === 'pdfjs')
    @php
        $pdfPath = !is_null($path_count) 
            ? '/collection/'.$collection_id.'/document/'.$doc->id.'/details/'.$path_count 
            : '/collection/'.$collection_id.'/document/'.$doc->id;
    @endphp
    <iframe id="pdfreader" class="pdf" src="/pdfjsviewer?file={{ urlencode($pdfPath) }}&collection_id={{ $collection_id }}" width="100%" height="100%"></iframe>
@else
    @if(!is_null($path_count))
    <iframe id="pdfreader" class="pdf" src="/js/ViewerJS/?zoom=page-width&title={{ $doc->title }}#../../collection/{{ $collection_id }}/document/{{ $doc->id }}/details/{{ $path_count }}" width="100%" height="100%"></iframe>
    @else
    <iframe id="pdfreader" class="pdf" src="/js/ViewerJS/?zoom=page-width&title={{ $doc->title }}#../../collection/{{ $collection_id }}/document/{{ $doc->id }}" width="100%" height="100%"></iframe>
    @endif
@endif
</div>
<div id="meta-view">
    <h4>Associated Meta Information<span class="text-right" style="float:right"><a href="/collection/{{ $collection_id }}/document/{{ $doc->id }}/doc-edit-viewer?viewer=viewerjs" style="color:#eee;">Edit</a></span></h4>
    <div id="meta-data">
    @php
        $meta_info = $doc->meta->sortBy(function ($m){
            return @$m->meta_field->display_order;
        });
    @endphp 
	@foreach($meta_info as $m)
        @if(!$m->meta_field || (empty(strip_tags($m->value)) && !@$column_config->document_viewer_empty_fields)) @continue @endif

		@if(@$m->meta_field->type == 'Date')
		<p><label>{{ @$m->meta_field->label }}</label><br />
        @if(date_create($doc->meta_value($m->meta_field_id)))
        {{ date_format(date_create($doc->meta_value($m->meta_field_id)), env('DATE_FORMAT', 'd/m/Y')) }}
        @endif
        </p>
		@else
		<p><label>{{ @$m->meta_field->label }}</label><br />{!! html_entity_decode($doc->meta_value($m->meta_field_id)) !!}</p>
		@endif
	@endforeach
    </div>
</div>
</div>
<script>
	var iframe = document.getElementById('pdfreader');

    if (iframe) {
        iframe.onload = function() {
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            const cssLink = iframeDoc.createElement('link');
            cssLink.href = '/c-{{ $collection_id }}/doc-viewer.css';
            cssLink.rel = 'stylesheet';
            cssLink.type = 'text/css';
            iframeDoc.head.appendChild(cssLink);
        };
    }

</script>
</body>
</html>
