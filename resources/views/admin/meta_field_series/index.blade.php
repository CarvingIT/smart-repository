@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h3>Meta Field Series</h3>
            <a href="/admin/meta-field-series/create" class="btn btn-primary">Create Series</a>
            <table class="table mt-3">
                <thead>
                    <tr><th>ID</th><th>Meta Field</th><th>Prefix</th><th>Date Format</th><th>Next</th><th>Pad</th><th>Actions</th></tr>
                </thead>
                <tbody>
                @foreach($series as $s)
                    <tr>
                        <td>{{ $s->id }}</td>
                        <td>{{ optional($s->meta_field)->label }}</td>
                        <td>{{ $s->prefix }}</td>
                        <td>{{ $s->date_format }}</td>
                        <td>{{ $s->next_sequence }}</td>
                        <td>{{ $s->pad_length }}</td>
                        <td>
                            <a href="/admin/meta-field-series/{{ $s->id }}/edit" class="btn btn-sm btn-primary">Edit</a>
                            <form method="post" action="/admin/meta-field-series/{{ $s->id }}/delete" style="display:inline">
                                @csrf
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $series->links() }}
        </div>
    </div>
</div>
@endsection
