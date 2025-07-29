<html>
<head>
<title>{{ env('APP_NAME', 'Smart Repository') }}::Document Viewer</title>
<link rel="icon" type="image/png" href="/material/img/favicon.png">
<style>
.row {
  display:flex;
  height:100%;
  overflow:clip;
}
#meta-view {
padding-left:1em;
flex:40%;
}
#doc-view {
  flex:60%;
}
body{
margin:0;
}
</style>
</head>
<body>
@php
    $doc = \App\Document::find($document_id);
@endphp
<div class="row">
<div id="meta-view">
	<h4>Associated Information</h4>
	@foreach($doc->meta as $m)
	<p><strong>{{ $m->meta_field->label }}</strong><br />{!! $m->value !!}</p>
	@endforeach
</div>
<div id="doc-view">
@php
if(!is_null($path_count)){
    $path = json_decode($doc->path);
    $file_url = $path[$path_count];
    $file_path  = $path[$path_count];
@endphp
<iframe id="pdfreAder" class="pdf" src="/js/ViewerJS/?title={{ $doc->title }}#../../collection/{{ $collection_id }}/document/{{ $document_id }}/details/{{ $path_count }}" width="100%" height="100%"></iframe> 
@php
}
else{
@endphp
<iframe id="pdfreader" class="pdf" src="/js/ViewerJS/?title={{ $doc->title }}#../../collection/{{ $collection_id }}/document/{{ $document_id }}" width="100%" height="100%"></iframe>
@php
}
@endphp
</div>
</div>
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
</body>
</html>
