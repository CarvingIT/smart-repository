@extends('layouts.app', ['activePage' => 'documents', 'titlePage' => __('Share Document')])

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title">{{ __('Create Shared Link') }}</h4>
                        <p class="card-category">{{ __('Share document: ') . $document->title }}</p>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('shared-links.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="document_id" value="{{ $document->id }}">
                            <div class="form-group">
                                <label for="password">Password (optional)</label>
                                <input type="password" name="password" id="password" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="expires_at">Expires At (optional)</label>
                                <input type="datetime-local" name="expires_at" id="expires_at" class="form-control">
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" type="checkbox" name="downloadable" value="1" checked>
                                    Allow Download
                                    <span class="form-check-sign">
                                        <span class="check"></span>
                                    </span>
                                </label>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Generate Link</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
