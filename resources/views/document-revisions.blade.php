@extends('layouts.app')

@section('content')
<script>
$(document).ready(function() {
    $('#revisions').DataTable();
} );
</script>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
            <div class="card-header">{{ ($document_revisions[0]->document)->title }}</div>
                 <div class="card-body">
                    <table id="revisions" class="display" style="width:100%">
                        <thead>
                            <tr>
                            <th>Type</th>
                            <th>Created</th>
                            <th>Created By</th>
                            <th>Size</th>
                            </tr>
                        </thead>
                        <tbody>
                    @foreach($document_revisions as $dr)
                    <tr>
                        <td><img class="file-icon" src="/i/file-types/{{ ($dr->document)->icon($dr->path) }}.png" /></td>
                        <td>
                        <a href="/document-revision/{{$dr->id}}" target="_new">{{ $dr->created_at }}</a>
                        </td>
                        <td>{{ ($dr->user)->email }}</td>
                        <td>{{ $dr->size }}</td>
                    </tr>
                    @endforeach
                        </tbody>
                    </table>
                 </div>
            </div>
        </div>
    </div>
</div>
@endsection
