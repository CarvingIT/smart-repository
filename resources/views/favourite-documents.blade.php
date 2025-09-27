@extends('layouts.app',['class'=> 'off-canvas-sidebar'])

@section('content')
@push('js')
<script src="/js/jquery.dataTables.min.js"></script>
<script>
// Setup CSRF token for AJAX requests
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).ready(function() {
    $('#favourites-table').DataTable({
        "columnDefs": [
            { "targets": [0], "className": 'text-center', "sortable": false },
            { "targets": [1], "className": 'text-left' },
            { "targets": [2], "className": 'text-left' },
            { "targets": [3], "className": 'text-right' },
            { "targets": [4], "className": 'text-left' },
            { "targets": [5], "className": 'td-actions text-right dt-nowrap', "sortable": false }
        ],
        "order": [[ 4, "desc" ]],
        "pageLength": 10,
        "language": {
            "processing": "<img src='/i/processing.gif'>"
        }
    });
});
</script>
@endpush

<div class="container">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title">{{ __('Favourite Documents') }}</h4>
                    </div>
                    <div class="card-body">
                        @if(count($favourites) == 0)
                            <div class="row">
                                <div class="col-12">
                                    <p>You haven't marked any documents as favourite yet. You can add documents to your favourites by clicking the star icon next to documents in any collection.</p>
                                </div>
                            </div>
                        @else
                            <div class="row">
                                <div class="col-12">
                                    <p>{{ count($favourites) }} favourite documents found.</p>
                                </div>
                            </div>
                            
                            <div class="table-responsive">
                                <table id="favourites-table" class="table">
                                    <thead class="text-primary">
                                        <tr>
                                            <th>{{ __('Type') }}</th>
                                            <th>{{ __('Title') }}</th>
                                            <th>{{ __('Collection') }}</th>
                                            <th>{{ __('Size') }}</th>
                                            <th>{{ __('Added to Favourites') }}</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($favourites as $fav)
                                        <tr>
                                            <td class="text-center">
                                                <img class="file-icon" src="/i/file-types/{{ $fav->document->icon() }}.png" />
                                            </td>
                                            <td>{{ $fav->document->title }}</td>
                                            <td>
                                                <a href="/collection/{{ $fav->document->collection_id }}" class="text-primary">
                                                    {{ $fav->document->collection->name }}
                                                </a>
                                            </td>
                                            <td>{{ $fav->document->human_filesize() }}</td>
                                            <td>{{ $fav->created_at->format('Y-M-d') }}</td>
                                            <td class="td-actions text-right">
                                                <!-- View Details -->
                                                <a class="btn btn-primary btn-link" title="Information and more" href="/collection/{{ $fav->document->collection_id }}/document/{{ $fav->document->id }}/details">
                                                    <i class="material-icons">forward</i>
                                                </a>
                                                
                                                <!-- Download -->
                                                @if(!empty($fav->document->path) && $fav->document->path != 'N/A')
                                                <a class="btn btn-primary btn-link" title="Download" href="/collection/{{ $fav->document->collection_id }}/document/{{ $fav->document->id }}" target="_blank">
                                                    <i class="material-icons">cloud_download</i>
                                                </a>
                                                @endif
                                                
                                                <!-- Remove from Favourites -->
                                                <span class="btn btn-danger btn-link" onclick="removeFavourite({{ $fav->document->id }}, {{ $fav->id }})" title="Remove from favourites">
                                                    <i class="material-icons">delete</i>
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Remove from favourites functionality
function removeFavourite(documentId, favouriteId) {
    if(confirm('Are you sure you want to remove this document from favourites?')) {
        $.ajax({
            url: '/favourite/remove',
            method: 'POST',
            data: {
                document_id: documentId
            },
            success: function(response) {
                if (response.success) {
                    // Remove the row from table
                    $(`tr:has([onclick="removeFavourite(${documentId}, ${favouriteId})"])`).fadeOut(300, function() {
                        $(this).remove();
                        
                        // Check if table is empty
                        if ($('#favourites-table tbody tr').length === 0) {
                            location.reload();
                        }
                    });
                    
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message);
                    }
                } else {
                    alert(response.message || 'Error occurred');
                }
            },
            error: function() {
                alert('Error occurred while removing favourite');
            }
        });
    }
}
</script>

@endsection