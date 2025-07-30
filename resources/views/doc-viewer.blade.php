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
flex:40%;
overflow-y:scroll;
}
#doc-view {
  flex:60%;
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
    box-shadow:6px #666 0 0 1em 0;
}
#meta-data{
    padding:1em;
}
</style>
</head>
<body>
@php
    $doc = \App\Document::find($document_id);
@endphp
<div class="row">
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
<div id="meta-view">
	<h4>Associated Meta Information</h4>
    <div id="meta-data">
	@foreach($doc->meta as $m)
        @if(!$m->meta_field || empty($m->value)) @continue @endif

		@if(@$m->meta_field->type == 'Date')
		<p><label>{{ @$m->meta_field->label }}</label><br />{{ date_format(date_create($doc->meta_value($m->meta_field_id)), env('DATE_FORMAT', 'd/m/Y')) }}</p>
		@else
		<p><label>{{ @$m->meta_field->label }}</label><br />{!! $doc->meta_value($m->meta_field_id) !!}</p>
		@endif
	@endforeach
    </div>
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
