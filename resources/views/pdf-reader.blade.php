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
	innerDoc.getElementById('download').style.display="none";
</script>
</body>
</html>
