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
            <div class="card-header card-header-primary"><h4 class="card-title"><a href="/collections">{{ __('Collections')}}</a>:: {{ __('Awaiting Approval Documents') }}</h4>
            </div>
                 <div class="card-body">
		<div class="row">
                  <div class="col-12 text-right">
                <a href="/collections" class="btn btn-sm btn-primary" title="Back"><i class="material-icons">arrow_back</i></a>
                  </div>

                </div>
			<div class="table-responsive">
                    <table id="documents" class="display table responsive">
                        <thead class="text-primary">
                            <tr>
                            <th>Title</th>
                            <th>Collection</th>
                            <th>Created By</th>
                            <th>Created at</th>
                            <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
			@foreach($awaiting_approvals_docs as $doc)
                        <tr>
                                <td>{{ $doc->title }}</td>
                                <td>{{ $doc->collection->name }}</td>
                                <td>{{ $doc->owner->name }}</td>
                                <td>{{ $doc->created_at }}</td>
                        	<td class="td-actions text-right">
                            		<a rel="tooltip" class="btn btn-success btn-link" href="/document/{{ $doc->id }}/approval" data-original-title="" title="">
                                    	<i class="material-icons">edit</i>
                                    	<div class="ripple-container"></div>
                                        </a>

                            		<a href="/collection/remove-user/" class="btn btn-danger btn-link">
                               		     <i class="material-icons">close</i>
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
