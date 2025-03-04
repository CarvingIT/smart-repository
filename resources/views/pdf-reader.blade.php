<html>
<head>
<title>Smart Repository::Document Viewer</title>
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
	var innerDoc = iframe.contentDocument || iframe.contentWindow.document;
</script>
</body>
</html>
