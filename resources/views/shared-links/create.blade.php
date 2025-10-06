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
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="password" class="bmd-label-floating">{{ __('Password (optional)') }}</label>
                                        <input type="password" name="password" id="password" class="form-control" placeholder="Leave empty for no password protection">
                                        @if ($errors->has('password'))
                                            <span class="text-danger">{{ $errors->first('password') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="expires_at" class="bmd-label-floating">{{ __('Expires At (optional)') }}</label>
                                        <input type="datetime-local" name="expires_at" id="expires_at" class="form-control">
                                        @if ($errors->has('expires_at'))
                                            <span class="text-danger">{{ $errors->first('expires_at') }}</span>
                                        @endif
                                        <small class="form-text text-muted">Leave empty for link that never expires</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input class="form-check-input" type="checkbox" name="downloadable" value="1" checked>
                                            {{ __('Allow Download') }}
                                            <span class="form-check-sign">
                                                <span class="check"></span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">{{ __('Generate Link') }}</button>
                            <a href="{{ route('shared-links.index') }}" class="btn btn-default">{{ __('Cancel') }}</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
