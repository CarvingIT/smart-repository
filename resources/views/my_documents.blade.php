@extends('layouts.app',['class'=> 'off-canvas-sidebar'])

@section('content')
@push('js')
<script src="/js/jquery-3.3.1.js"></script>
<script src="/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#documents').DataTable({
    "aoColumnDefs": [
            { "bSortable": false, "aTargets": [2]},
			{ "className": 'align-top', "aTargets": [0]},
     ]
    });
} );
</script>
@endpush

<div class="container">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card">
            <div class="card-header card-header-primary"><h4 class="card-title">{{ __('My Uploaded Documents') }}</h4>
            </div>
                 <div class="card-body">
		<div class="row">
                  <div class="col-12 text-right">
                <a href="/collections" class="btn btn-sm btn-primary" title="Back"><i class="material-icons">arrow_back</i></a>
                  </div>

                </div>
			@foreach (['danger', 'warning', 'success', 'info'] as $msg)
                   @if(Session::has('alert-' . $msg))
                        <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                                <div class="mt-6 text-gray-900 leading-7 font-semibold ">
                                        <span @if($msg == 'danger') style="color:red" @endif>{{ Session::get('alert-' . $msg) }}</span>
                                </div>
                        </div>
                   @endif
               @endforeach

			<div class="table-responsive">
                    <table id="documents" class="display table responsive">
                        <thead class="text-primary">
                            <tr>
                            <th>Title</th>
                            <th>Collection</th>
                            <th>Approval By</th>
                            <th>Status</th>
                            <th>Created at</th>
                            <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
			@foreach($documents as $doc)
				@php
					$approval_details=\App\Approval::where('approvable_id',$doc->id)
							//->where('approval_status',0)
							->orderBy('id','desc')
							->first();
					//print_r($approval_details);
					//echo $approval_details->approval_status;
				@endphp
                        <tr>
                                <td>{{ $doc->title }}</td>
                                <td>{{ @$doc->collection->name }}</td>
                                <td>{{ @$approval_details->approver->name }}</td>
                                <td>@if (!empty($approval_details->approval_status) && $approval_details->approval_status == 1){{ __('Approved') }} @elseif(!empty($approval_details->approval_status) && $approval_details->approval_status == 0){{ __('Rejected') }}@else {{ __('Awaiting') }} @endif </td>
                                <td>{{ $doc->created_at }}</td>
                        	<td class="td-actions text-right">
					<a href="/collection/{{ $doc->collection_id }}/document/{{ $doc->id }}/details" title="View Document"><i class="material-icons">visibility</i></a>
                            		<a rel="tooltip" class="btn btn-success btn-link" href="/document/{{ $doc->id }}/edit" data-original-title="" title="">
                                    	<i class="material-icons">edit</i>
                                    	<div class="ripple-container"></div>
                                        </a>
                        	</td>
                    	</tr>
                        @endforeach
                        </tbody>
                    </table>
		</div> <!-- table-responsive ends -->
                 </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
