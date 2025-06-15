@extends('layouts.app',['class'=> 'off-canvas-sidebar'])
@section('content')
@push('js')
<script src="/js/jquery-3.3.1.js"></script>
<script src="/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#documents').DataTable({
    "order":[[2,'desc']],
    "aoColumnDefs": [
            { "bSortable": false, "aTargets": [3]},
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
            <div class="card-header card-header-primary"><h4 class="card-title">{{ __(ucfirst($status)) }}</h4>
            </div>
                 <div class="card-body">
		<div class="row">
                  <div class="col-12 text-right">
                <a href="/dashboard" class="btn btn-sm btn-primary" title="Back"><i class="material-icons">arrow_back</i></a>
                  </div>

                </div>
			<div class="table-responsive">
                    <table id="documents" class="display table responsive">
                        <thead class="text-primary">
                            <tr>
                            <th>Title</th>
                            <th>Comments</th>
                            <th>Created at</th>
                            <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
			@foreach($approvables as $item)
				@if($item->approvable_type == 'App\BinshopsPost')
					@php 
						//$post = App\BinshopsPublish::where('post_id',$item->approvable_id)->first();
						$post = App\BinshopsPost::find($item->approvable_id);
					if(!$post) continue;
				//print_r($post); echo $post->title; exit;
					@endphp
				@endif	
                        <tr>
                                <td>@if($item->approvable_type == 'App\BinshopsPost'){{ $post->defaultTitle() }}@else {{ @$item->approvable->title }}@endif</td>
                                <td>{{ $item->comments }}</td>
                                <td>{{ $item->created_at }}</td>
                        <td class="td-actions text-right">
				@if($item->approvable_type == 'App\BinshopsPost')
                            <a rel="tooltip" class="btn btn-success btn-link" href="/en/blog/{{ @$post->slug}}" data-original-title="" title="">
				@else
                            <a rel="tooltip" class="btn btn-success btn-link" href="/document/{{ $item->approvable_id }}/approval" data-original-title="" title="">
				@endif
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
