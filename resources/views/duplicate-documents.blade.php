@extends('layouts.app',['class'=> 'off-canvas-sidebar','activePage'=>'documents','titlePage'=>'Deleted Documents', 'title'=>'Smart Repository'])

@section('content')
@push('js')
<script src="/js/jquery.dataTables.min.js"></script>
<script src="/js/jquery-ui.js" defer></script>
<link href="/css/jquery-ui.css" rel="stylesheet">
<script>
$(document).ready(function() {
    oTable = $('#documents').DataTable();
} );
</script>
<style>
table.dataTable tbody td {
  vertical-align: top;
}
</style>
@endpush

<div class="container">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
		<div class="card-header card-header-primary">
                <h4 class="card-title ">{{__('Duplicate Documents')}}</h4>
        	</div>
	        <div class="card-body">
		<div class="card search-filters-card">
		<div class="table-responsive">
        <p>This report was run on {{ $last_run }}. Following list gets updated when the report runs. You can run the report manually or through crontab.</p>
                    <table id="documents" class="table">
                        <thead class="text-primary">
                            <tr>
                            <th>{{__('Document')}}</th>
                            <th>{{__('Duplicates')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($duplicates as $ori => $dupes)
                            <tr>
                                <td valign="top">
                                    @php
                                    $doc = $dupes->shift();
                                    @endphp
                                    <a href="/collection/{{ $doc->collection->id }}/document/{{ $doc->id }}" target="_new">{{ $doc->title }}</a>
                                </td>
                                <td>
                                    @foreach($dupes as $dup)
                                    <a href="/collection/{{ $dup->collection->id }}/document/{{ $dup->id }}" target="_new">{{ $dup->title }}</a><br />
                                    @endforeach
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
        	</div>
            </div>
        </div>
    </div>
</div>
@endsection
