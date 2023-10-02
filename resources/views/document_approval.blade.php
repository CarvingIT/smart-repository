@extends('layouts.app',['class'=> 'off-canvas-sidebar'])
@push('js')
<link href="/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
<script src="/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>

<link rel="stylesheet" href="/css/jquery.dataTables.min.css" />
<link rel="stylesheet" href="/css/jquery-ui.css" />
<script src="/js/jquery-3.5.1.js"></script>
<script src="/js/jquery.dataTables.min.js"></script>
<script src="/js/jquery-ui.js"></script>

<script type="text/javascript">
$(document).ready(function() {
        $("#approvals").DataTable(
                {
                stateSave:true,
                "scrollX": true,
                columnDefs: [
                        { width: '20%', targets: 0 },
                        { "orderable": false, targets: 4 }
                ],
                "lengthMenu": [ 100, 500, 1000 ],
                "pageLength": 100,
                fixedColumns: true
                }
        );
// New code to retain search value
// Restore state
});

</script>

@endpush
@section('content')
<div class="container">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header card-header-primary"><h4 class="card-title"><a href="/collections">{{ __('Collections') }}</a> :: <a href="/collection/{{ $collection->id }}">{{ $collection->name }}</a> :: Upload Document</h4></div>
                <div class="col-md-12 text-right">
                <a href="javascript:window.history.back();" class="btn btn-sm btn-primary" title="Back"><i class="material-icons">arrow_back</i></a>
                </div>

                <div class="card-body">
                    <div class="flash-message">
                    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                        @if(Session::has('alert-' . $msg))
				<div class="alert alert-<?php echo $msg; ?>">
                        	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        	<i class="material-icons">close</i>
                        	</button>
                        	<span>{{ Session::get('alert-' . $msg) }}</span>
                        </div>
                        @endif
                    @endforeach
                    </div>

<form name="document_upload_form" action="/document/{{ $document->id }}/save_status" method="post">
@csrf()
<input type="hidden" name="collection_id" value="{{ $collection->id }}" />
@if(!empty($document->id))
<input type="hidden" name="document_id" value="{{ $document->id }}" />
@endif
		<div class="form-group row">
	   	   <div class="col-md-3">
		   <label for="title" class="col-md-12 col-form-label text-md-right">Title</label>
		   </div>
                    <div class="col-md-9">
                    <input class="form-control" type="text" id="title" name="title" size="40" value="@if(!empty($document->id)){{ html_entity_decode($document->title) }}@endif" 
                    placeholder="If left blank, we shall guess!" />
                    </div>
		</div>
                <div class="form-group row">
                   <div class="col-md-3">
                   <label for="approved" class="col-md-12 col-form-label text-md-right">Approval Status</label>
                   </div>
                   <div class="col-md-9">
                   <select id="approval_status" name="approval_status" class="selectpicker" required>
			<option value="">Select Status</option>
			<option value="1">Approved</option>
			<option value="0">Rejected</option>
		   </select>
                   </div>
                </div>
                <div class="form-group row">
                   <div class="col-md-3">
                   <label for="approved" class="col-md-12 col-form-label text-md-right">Comments</label>
                   </div>
                   <div class="col-md-9">
                   <textarea class="form-control" id="approval_comment" name="comments"></textarea> 
                   </div>
                </div>

	<div class="select-data-container" style="position:fixed; top:25%; z-index:1000;"></div>
<div class="form-group row mb-0">
    <div class="col-md-9 offset-md-4">
        <button type="submit" class="btn btn-primary"> Save </button>
    </div>
</div>
</form>

		<div class="table-responsive">
		<table id="approvals" class="display">
                        <thead class="text-primary">
                            <tr>
                            <th>Updated at</th>
                            <th>Approved By</th>
                            <th>Approval Status</th>
                            <th>Comments</th>
                            </tr>
                        </thead>
                        <tbody>
			@php
			$doc_approvals = $document->approvals->sortByDesc('created_at');
			@endphp
            @foreach ($doc_approvals as $d_a)
			<tr>
			<td>{{ $d_a->updated_at }}</td>
			<td>{{ $d_a->approvable->owner->name }}</td>
			<td>@if($d_a->approval_status == 1) {{ __('Approved') }} @else {{ __('Rejected') }} @endif</td>
			<td>{!! $d_a->comments !!}</td>
			</tr>
			@endforeach
			</tbody>
		</table>
		</div>


                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
