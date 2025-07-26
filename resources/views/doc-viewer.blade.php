<html>
<head>
<title>{{ env('APP_NAME', 'Smart Repository') }}::Document Viewer</title>
</head>
<body>
@php
if(!is_null($path_count)){
    $doc = \App\Document::find($document_id);
    $path = json_decode($doc->path);
    $file_url = $path[$path_count];
    $file_path  = $path[$path_count];
@endphp
<iframe id="pdfreader" class="pdf" src="/js/ViewerJS/#../../collection/{{ $collection_id }}/document/{{ $document_id }}/details/{{ $path_count }}" width="100%" height="100%"></iframe> 
@php
}
else{
@endphp
<iframe id="pdfreader" class="pdf" src="/js/ViewerJS/#../../collection/{{ $collection_id }}/document/{{ $document_id }}" width="100%" height="100%"></iframe>
@php
}
@endphp

@endphp
<!--iframe id="pdfreader" class="pdf" src="/js/ViewerJS/#../../collection/{{ $collection_id }}/document/{{ $document_id }}" 
            width="100%" height="100%">
</iframe-->

<script>
	var iframe = document.getElementById('pdfreader');

    iframe.onload = function() {
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        const cssLink = iframeDoc.createElement('link');
        cssLink.href = '/c-{{ $collection_id }}/doc-viewer.css';
        cssLink.rel = 'stylesheet';
        cssLink.type = 'text/css';
        iframeDoc.head.appendChild(cssLink);
    };

	var innerDoc = iframe.contentDocument || iframe.contentWindow.document;
</script>
@php
    $conf = \App\Sysconfig::all();
    $settings = array();
    foreach($conf as $c){
        $settings[$c->param] = $c->value;
    }
@endphp
@if(!empty($settings['footer']))
	{!! $settings['footer'] !!}
@endif
</body>
</html>
