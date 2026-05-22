@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <h3>{{ $series->id ? 'Edit' : 'Create' }} Series</h3>
            <form method="post" action="{{ $series->id ? '/admin/meta-field-series/'.$series->id : '/admin/meta-field-series' }}">
                @csrf
                <div class="form-group">
                    <label>Meta Field</label>
                    <select name="meta_field_id" class="form-control">
                        @foreach($meta_fields as $mf)
                            <option value="{{ $mf->id }}" @if($series->meta_field_id == $mf->id) selected @endif>{{ $mf->label }} ({{ $mf->id }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Prefix</label>
                    <input type="text" name="prefix" class="form-control" value="{{ $series->prefix ?? '' }}" />
                </div>
                <div class="form-group">
                    <label>Date Format</label>
                    <input type="text" name="date_format" class="form-control" value="{{ $series->date_format ?? '' }}" />
                </div>
                <div class="form-group">
                    <label>Next Sequence</label>
                    <input type="number" name="next_sequence" class="form-control" value="{{ $series->next_sequence ?? 1 }}" />
                </div>
                <div class="form-group">
                    <label>Pad Length</label>
                    <input type="number" name="pad_length" class="form-control" value="{{ $series->pad_length ?? 0 }}" />
                </div>
                <button class="btn btn-primary">Save</button>
            </form>
        </div>
    </div>
</div>
@endsection
