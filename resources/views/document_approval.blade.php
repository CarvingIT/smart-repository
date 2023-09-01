@extends('layouts.app',['class'=> 'off-canvas-sidebar'])
@push('js')
<link href="/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
<script src="/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
<script src="/js/tinymce/tinymce.min.js" referrerpolicy="origin"></script>


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

tinymce.init({
                                //selector: '#document_description',
                                selector: 'textarea',
                                plugins: [
                                    "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                                    "searchreplace wordcount visualblocks visualchars code fullscreen",
                                    "insertdatetime media table nonbreaking save contextmenu directionality paste"
                                ],
                                toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | code",
                                relative_urls: false,
                                //remove_script_host: false,
                                //convert_urls: true,
                                force_br_newlines: true,
                                force_p_newlines: false,
                                forced_root_block: '', // Needed for 3.x
                              file_picker_callback (callback, value, meta) {
        let x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth
        let y = window.innerHeight|| document.documentElement.clientHeight|| document.getElementsByTagName('body')[0].clientHeight
	
	tinymce.activeEditor.windowManager.openUrl({
          url : '/file-manager/tinymce5',
          title : 'File manager',
          width : x * 0.8,
          height : y * 0.8,
          onMessage: (api, message) => {
			//alert(message.content);
            //callback(message.content, { text: message.text })
            callback('/media/i/'+message.text, { text: message.text })
          }
        })
      },
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

<form name="document_upload_form" action="/document/save_status" method="post">
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
			<option value="0">UnApproved</option>
		   </select>
                   </div>
                </div>
                <div class="form-group row">
                   <div class="col-md-3">
                   <label for="approved" class="col-md-12 col-form-label text-md-right">Comments</label>
                   </div>
                   <div class="col-md-9">
                   <textarea id="approval_comment" name="comments"></textarea> 
                   </div>
                </div>

	<div class="select-data-container" style="position:fixed; top:25%; z-index:1000;"></div>
@php
	$approvals = $document_ids = [];
	foreach($doc_approvals as $da){
		$approvals[] = $da->approved_by;
	}
	$doc_approval_details = auth()->user()->docApprovals;
	foreach($doc_approval_details as $details){
		$document_ids[] = $details->document_id;
		if($document->id == $details->document_id){
		$document_status = $details->approval_status;
		}
	}
	//print_r(json_decode($collection->column_config)->approved_by);
	//echo auth()->user()->userrole(auth()->user()->id);
@endphp
<div class="form-group row mb-0">
    <div class="col-md-9 offset-md-4">
@if((in_array(auth()->user()->userrole(auth()->user()->id),json_decode($collection->column_config)->approved_by)) && (!in_array($document->id,$document_ids) || in_array($document->id,$document_ids) && $document_status == 0))
        <button type="submit" class="btn btn-primary"> Save </button>
@else
        <button type="button" class="btn btn-primary">{{ __('You have Approved this document') }}</button>
@endif
    </div>
</div>
</form>

		<div class="table-responsive">
		<table id="approvals" class="display">
                        <thead class="text-primary">
                            <tr>
                            <th>Document</th>
                            <th>Approved By</th>
                            <th>Approval Status</th>
                            <th>Comments</th>
                            <th>Updated at</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($doc_approvals as $d_a)
			@php
			$document = \App\Document::find($d_a->document_id);
			if(!empty($document)){
			@endphp
			<tr>
			<td>{{ @$d_a->document->title }}</td>
			<td>{{ $d_a->user->name }}</td>
			<td>@if($d_a->approval_status == 1) {{ __('Approved') }} @else {{ __('UnApproved') }} @endif</td>
			<td>{!! $d_a->comments !!}</td>
			<td>{{ $d_a->updated_at }}</td>
			</tr>
			@php } @endphp
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
