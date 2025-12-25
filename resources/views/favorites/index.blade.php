@extends('layouts.app',['class'=> 'off-canvas-sidebar'])

@section('content')
@push('js')
<script src="/js/node/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="/css/node/jquery.dataTables.min.css" />
<script>
$(document).ready(function() {
    var table = $('#favorites-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('favorites.data') }}",
            type: 'GET'
        },
        columns: [
            { data: 'title', name: 'title', className: 'text-left' },
            { data: 'collection_name', name: 'collection.name', className: 'text-left' },
            { data: 'created_at', name: 'created_at', className: 'text-left' },
            { data: 'size', name: 'size', className: 'text-left' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'td-actions text-right dt-nowrap' }
        ],
        order: [[2, 'desc']], // Sort by created_at descending
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
            "emptyTable": "{{ __('No data available in table') }}"
        }
    });

    // Handle unfavorite button click
    $(document).on('click', '.unfavorite-btn', function(e) {
        e.preventDefault();
        var documentId = $(this).data('document-id');
        
        $.ajax({
            url: '/favorites/toggle/' + documentId,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.status === 'success') {
                    // Reload the table
                    table.ajax.reload(null, false); // false keeps current page
                }
            },
            error: function(xhr) {
                console.error('Error removing favorite:', xhr);
            }
        });
    });
});
</script>

<script src="{{ asset("js/favorites.js") }}"></script>
@endpush

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-primary">
                    <h4 class="card-title">
                        @if(env('ENABLE_COLLECTION_LIST') == 1)<a href="/collections">{{ __('Collections') }}</a> ::@endif {{ __('My Favourites') }}
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table id="favorites-table" class="table table-striped table-hover">
                                    <thead class="text-primary">
                                        <tr>
                                            <th>{{ __('Title') }}</th>
                                            <th>{{ __('Collection') }}</th>
                                            <th>{{ __('Uploaded') }}</th>
                                            <th>{{ __('Size') }}</th>
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