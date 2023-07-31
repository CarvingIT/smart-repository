<html>
<head>
</head>
<body>
	<div style="text-align:center;">
	<h3>{{ $document->title }}</h3>
	<video controls width="400">
	<source src="/collection/{{ $collection_id }}/document/{{ $document->id }}" type="video/mp4">
	</video>
	</div>
</body>
</html>
