@extends('layouts.app',['class'=> 'off-canvas-sidebar'])
@push('js')
{{-- Bootstrap 4 is already loaded by layout, do NOT load Bootstrap 5 here (causes dropdown conflict) --}}

<link rel="stylesheet" href="/build/assets/css/dataTables.dataTables.min.css" />
<link rel="stylesheet" href="/build/assets/css/jquery-ui.min.css" />
<script src="/build/assets/js/jquery.min.js"></script>
<script src="/build/assets/js/dataTables..js"></script>
<script src="/build/assets/js/jquery-ui.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
        $("#approvals").DataTable(
                {
                stateSave:true,
                "scrollX": true,
                columnDefs: [
                        { width: '20%', targets: 0 },
            { "orderable": false, targets: 3 }
                ],
                "lengthMenu": [ 100, 500, 1000 ],
        "pageLength": 100
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

<form name="document_upload_form" action="/approvals/document/{{ $document->id }}/save_status" method="post">
@csrf()
<input type="hidden" name="collection_id" value="{{ $collection->id }}" />
@if(!empty($document->id))
<input type="hidden" name="document_id" value="{{ $document->id }}" />
@endif
        @if(!empty($current_approval))
        <input type="hidden" name="approval_id" value="{{ $current_approval->id }}" />
        @endif
		<div class="form-group row">
	   	   <div class="col-md-3">
		   <label for="title" class="col-md-12 col-form-label text-md-right">Document</label>
		   </div>
                    <div class="col-md-9">
		    <a href="/document/{{ $document->id }}/edit" target="_new">{{ $document->title }}</a>
                    </div>
		</div>
        @php
            $currentStageIndex = null;
            if (!empty($current_approval) && !empty($approval_workflow_stages)) {
                foreach ($approval_workflow_stages as $stageIndex => $stage) {
                    if ((int) $stage['role_id'] === (int) $current_approval->approved_by_role) {
                        $currentStageIndex = $stageIndex;
                        break;
                    }
                }
            }
        @endphp

        @if(!empty($approval_workflow_stages))
        <div class="form-group row">
            <div class="col-md-12">
                <h5 style="margin-top: 15px;">Approval Workflow</h5>
            </div>
        </div>
        @foreach($approval_workflow_stages as $stageIndex => $stage)
        <div class="card mb-3 @if(!empty($stage['is_current'])) border border-primary @endif">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <strong>{{ $loop->iteration }}. {{ $stage['role_name'] }}</strong>
                    @if(!empty($stage['is_current']))
                        <span class="badge badge-primary" style="margin-left: 8px;">Current stage</span>
                    @endif
                </div>
                <div>
                    @if(!empty($stage['approval']) && $stage['approval']->approval_status === 1)
                        <span class="badge badge-success">Approved</span>
                    @elseif(!empty($stage['approval']) && $stage['approval']->approval_status === 0)
                        <span class="badge badge-danger">Rejected</span>
                    @elseif(!empty($stage['approval']))
                        <span class="badge badge-warning">Pending</span>
                    @else
                        <span class="badge badge-secondary">Not started</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                @if(!empty($stage['approval']) && !empty($stage['approval']->approver))
                    <div class="text-muted" style="margin-bottom: 8px;">Updated by {{ $stage['approval']->approver->name }}</div>
                @endif
                @if(empty($stage['labels']))
                    <div class="text-muted">No checklist configured for this stage.</div>
                @else
                    @php
                        $stageValues = !empty($stage['approval']) && is_array($stage['approval']->checklist_values)
                            ? array_values($stage['approval']->checklist_values)
                            : [];
                    @endphp
                    <div class="row">
                        @foreach($stage['labels'] as $checklistIndex => $checklistLabel)
                            @php
                                $isChecked = !empty($stageValues[$checklistIndex]);
                                $inputId = 'approval_stage_'.$stageIndex.'_checklist_'.$checklistIndex;
                            @endphp
                            <div class="col-md-6" style="margin-bottom: 8px;">
                                <div>
                                    @if(!empty($stage['is_editable']))
                                        <input type="hidden" name="checklist_values[{{ $checklistIndex }}]" value="0">
                                    @endif
                                    <label style="margin-bottom: 0;">
                                        <input
                                            class=""
                                            type="checkbox"
                                            value="1"
                                            @if(!empty($stage['is_editable']))
                                                name="checklist_values[{{ $checklistIndex }}]"
                                                id="{{ $inputId }}"
                                            @else
                                                disabled
                                            @endif
                                            @if($isChecked) checked @endif
                                        >
                                        <span style="margin-left: 0px;">{{ $checklistLabel }}</span>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        @endforeach
        @endif

        @if(!empty($current_approval))
        <div class="form-group row">
           <div class="col-md-3">
           <label for="approved" class="col-md-12 col-form-label text-md-right">Approval Status</label>
           </div>
           <div class="col-md-9">
           <select id="approval_status" name="approval_status" class="selectpicker" required>
            <option value="">Select Status</option>
            <option value="1" @if(isset($current_approval_status) && $current_approval_status === 1) selected @endif>Approved</option>
            <option value="0" @if(isset($current_approval_status) && $current_approval_status === 0) selected @endif>Rejected</option>
           </select>
           </div>
        </div>
        <div class="form-group row">
           <div class="col-md-3">
           <label for="approved" class="col-md-12 col-form-label text-md-right">Comments</label>
           </div>
           <div class="col-md-9">
                 <textarea class="form-control" id="approval_comment" name="comments">{{ old('comments', $current_approval_comments ?? '') }}</textarea> 
           </div>
        </div>
        <div class="select-data-container" style="position:fixed; top:25%; z-index:1000;"></div>
        <div class="form-group row mb-0">
            <div class="col-md-9 offset-md-4">
                <button type="submit" class="btn btn-primary"> Save </button>
            </div>
        </div>
        @else
        <div class="alert alert-warning" style="margin-top: 15px;">No pending approval found for your role.</div>
        @endif
</form>

		<div class="table-responsive">
        <table id="approvals" class="display" style="width:100%;">
                        <thead class="text-primary">
                            <tr>
                            <th>Timestamp</th>
                            <th>User</th>
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
			<td>{{ $d_a->created_at }}</td>
			<td>{{ @$d_a->approver->name }}</td>
            <td>@if($d_a->approval_status === 1) {{ __('Approved') }} @elseif ($d_a->approval_status === 0) {{ __('Rejected') }} @else {{ 'Awaiting approval' }} @endif</td>
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
