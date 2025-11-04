@extends('layouts.app', ['page' => __('Edit Shared Link'), 'pageSlug' => 'shared-links'])

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="title">Edit Shared Link for {{ $sharedLink->document->title }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('shared-links.update', $sharedLink) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="password">Password (leave blank to keep unchanged)</label>
                        <input type="password" name="password" id="password" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="expires_at">Expires At (optional)</label>
                        <input type="datetime-local" name="expires_at" id="expires_at" class="form-control" value="{{ $sharedLink->expires_at ? $sharedLink->expires_at->format('Y-m-d\TH:i') : '' }}">
                    </div>
                    <div class="form-check">
                        <label class="form-check-label">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $sharedLink->is_active ? 'checked' : '' }}>
                            Active
                            <span class="form-check-sign">
                                <span class="check"></span>
                            </span>
                        </label>
                    </div>
                    <div class="form-check">
                        <label class="form-check-label">
                            <input class="form-check-input" type="checkbox" name="downloadable" value="1" {{ $sharedLink->downloadable ? 'checked' : '' }}>
                            Allow Download
                            <span class="form-check-sign">
                                <span class="check"></span>
                            </span>
                        </label>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Update Link</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
