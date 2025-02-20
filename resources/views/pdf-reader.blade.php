<html>
<head>
<title>Smart Repository::Document Viewer</title>
</head>
<body>
<iframe id="pdfreader" class="pdf" src="/js/ViewerJS/#../../collection/{{ $collection_id }}/document/{{ $document_id }}" 
            width="100%" height="100%">
</iframe>

<script>
	var iframe = document.getElementById('pdfreader');
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
