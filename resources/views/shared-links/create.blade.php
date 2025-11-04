@extends('layouts.app', ['activePage' => 'documents', 'titlePage' => __('Share Document')])

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
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
                                    <div class="form-group bmd-form-group">
                                        <label for="password" class="bmd-label-floating">{{ __('Password (optional)') }}</label>
                                        <input type="password" name="password" id="password" class="form-control">
                                        @if ($errors->has('password'))
                                            <span class="text-danger">{{ $errors->first('password') }}</span>
                                        @endif
                                        <small class="form-text text-muted">Leave empty for no password protection</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group bmd-form-group">
                                        <label for="expires_at" class="bmd-label-static">{{ __('Expires At (optional)') }}</label>
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
                                    <div class="form-group bmd-form-group">
                                        <label for="permission_level" class="bmd-label-static">{{ __('Access Permission') }}</label>
                                        <select name="permission_level" id="permission_level" class="form-control">
                                            <option value="view">{{ __('View Only') }}</option>
                                            <option value="download">{{ __('Download Only') }}</option>
                                            <option value="both" selected>{{ __('View and Download') }}</option>
                                        </select>
                                        @if ($errors->has('permission_level'))
                                            <span class="text-danger">{{ $errors->first('permission_level') }}</span>
                                        @endif
                                        <small class="form-text text-muted">Choose what users can do with this shared document</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group bmd-form-group">
                                        <label for="description" class="bmd-label-floating">{{ __('Description (optional)') }}</label>
                                        <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                                        @if ($errors->has('description'))
                                            <span class="text-danger">{{ $errors->first('description') }}</span>
                                        @endif
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
