<html>
<head>
</head>
<body>
	<div style="text-align:center;">
	<h3>{{ $document->title }}</h3>
    @if(!is_null($path_count))
	<video controls width="400">
	<source src="/collection/{{ $collection_id }}/document/{{ $document->id }}/details/{{ $path_count }}" type="video/mp4">
	</video>
    @else
	<video controls width="400">
	<source src="/collection/{{ $collection_id }}/document/{{ $document->id }}" type="video/mp4">
	</video>
    @endif
	</div>
</body>
</html>
