@extends('layouts.app',['class'=> 'off-canvas-sidebar'])

@section('content')
@push('js')
<script src="/build/assets/js/dataTables.js"></script>
<script>
$(document).ready(function() {
    $('#collection_users').DataTable({
    "aoColumnDefs": [
            { "bSortable": false, "aTargets": [2]},
			{ "className": 'align-top', "aTargets": [0]},
     ],
    "language": {
        "sEmptyTable": "{{ __('No data available in table') }}",
        "sInfo": "{{ __('Showing _START_ to _END_ of _TOTAL_ entries') }}",
        "sInfoEmpty": "{{ __('Showing 0 to 0 of 0 entries') }}",
        "sInfoFiltered": "{{ __('(filtered from _MAX_ total entries)') }}",
        "sLengthMenu": "{{ __('Show _MENU_ entries') }}",
        "sLoadingRecords": "{{ __('Loading...') }}",
        "sProcessing": "{{ __('Processing...') }}",
        "sSearch": "{{ __('Search:') }}",
        "sZeroRecords": "{{ __('No matching records found') }}",
        "oPaginate": {
            "sFirst": "{{ __('First') }}",
            "sLast": "{{ __('Last') }}",
            "sNext": "{{ __('Next') }}",
            "sPrevious": "{{ __('Previous') }}"
        }
    }
    });
} );
</script>
@endpush

<div class="container">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card">
            <div class="card-header card-header-primary"><h4 class="card-title"><a href="/collections">{{ __('Collections')}}</a> :: <a href="/collection/{{ $collection->id }}">{{ $collection->name }}</a> :: {{ __('Collection users') }}</h4>
            </div>
                 <div class="card-body">
		<div class="row">
                  <div class="col-12 text-right">
                    <a href="/collection/{{ $collection->id }}/user" class="btn btn-sm btn-primary" title="{{ __('Add User') }}"><i class="material-icons">add</i></a>
                <a href="/collection/{{ $collection->id }}" class="btn btn-sm btn-primary" title="{{ __('Back') }}"><i class="material-icons">arrow_back</i></a>
                  </div>

                </div>
			<div class="table-responsive">
                    <table id="collection_users" class="display table responsive">
                        <thead class="text-primary">
                            <tr>
                            <th>{{ __('ID') }}</th>
                            <th>{{ __('Permissions') }}</th>
                            <th class="text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                    @foreach($collection_users as $user_id=>$perms)
                    @if(!$perms[0]->user) 
                        @continue
                    @endif
                    <tr>
                        <td>
                        {{ ($perms[0]->user)->email }}
                        </td>
                        <td>
                        @foreach($perms as $p)
                            {{ __(($p->permission)->description) }} @if(!empty($p->till_date)) {{ __('till') }} {{ $p->till_date }}@endif<br/>
                        @endforeach
                        </td>
                        <td class="td-actions text-right">
                            <a rel="tooltip" class="btn btn-success btn-link" href="/collection/{{ $collection->id }}/user/{{ ($perms[0]->user)->id }}" data-original-title="" title="">
                                    <i class="material-icons">edit</i>
                                    <div class="ripple-container"></div>
                                  </a>

                            <a href="/collection/{{ $collection->id }}/remove-user/{{ ($perms[0]->user)->id }}" class="btn btn-danger btn-link">
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
