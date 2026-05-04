@extends('layouts.app',['class'=> 'off-canvas-sidebar'])

@section('content')
@push('js')
<script src="/build/assets/js/dataTables.js"></script>
<link rel="stylesheet" href="/build/assets/css/dataTables.dataTables.min.css" />
<script>
$(document).ready(function() {
    var isAdmin = false;

        var columns = [
        { data: 'name', name: 'name', className: 'text-left' },
        { data: 'collection_name', name: 'collection.name', className: 'text-left' },
        { data: 'query_summary', name: 'query_summary', className: 'text-left', orderable: false },
        { data: 'created_at', name: 'created_at', className: 'text-left' },
        { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'td-actions text-right dt-nowrap' }
    ];
    
    
    var table = $('#saved-searches-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('saved-searches.data') }}",
            type: 'GET'
        },
        columns: columns,
        order: [[3, 'desc']], // Sort by created_at descending
        language: {
            "search": "{{ __('Search:') }}",
            "lengthMenu": "{{ __('Show') }} _MENU_ {{ __('entries') }}",
            "info": "{{ __('Showing') }} _START_ {{ __('to') }} _END_ {{ __('of') }} _TOTAL_ {{ __('entries') }}",
            "infoEmpty": "{{ __('Showing 0 to 0 of 0 entries') }}",
            "infoFiltered": "({{ __('filtered from') }} _MAX_ {{ __('total entries') }})",
            "paginate": {
                "first": "{{ __('First') }}",
                "last": "{{ __('Last') }}",
                "next": "{{ __('Next') }}",
                "previous": "{{ __('Previous') }}"
            },
            "processing": "{{ __('Processing...') }}",
            "zeroRecords": "{{ __('No matching records found') }}",
            "emptyTable": "{{ __('No saved searches yet') }}"
        }
    });

    // Handle delete button click
    $(document).on('click', '.delete-search-btn', function(e) {
        e.preventDefault();
        var searchId = $(this).data('search-id');
        
        if (!confirm('{{ __("Are you sure you want to delete this saved search?") }}')) {
            return;
        }
        
        $.ajax({
            url: '/saved-searches/' + searchId,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.status === 'success') {
                    // Reload the table
                    table.ajax.reload(null, false);
                }
            },
            error: function(xhr) {
                console.error('Error deleting saved search:', xhr);
                alert('{{ __("Error deleting saved search") }}');
            }
        });
    });
});
</script>
@endpush

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-primary">
                    <h4 class="card-title">
                        @if(env('ENABLE_COLLECTION_LIST') == 1)<a href="/collections">{{ __('Collections') }}</a> ::@endif {{ __('Saved Searches') }}
                        
                    </h4>
                </div>
                <div class="card-body">
                    <div class="flash-message">
                        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                            @if(Session::has('alert-' . $msg))
                                <div class="alert alert-{{ $msg }}">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <i class="material-icons">close</i>
                                    </button>
                                    <span>{{ Session::get('alert-' . $msg) }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table id="saved-searches-table" class="table table-striped table-hover">
                                    <thead class="text-primary">
                                        <tr>
                                            <th>{{ __('Name') }}</th>
                                            <th>{{ __('Collection') }}</th>
                                            
                                            <th>{{ __('Query') }}</th>
                                            <th>{{ __('Created') }}</th>
                                            <th class="text-right">{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
