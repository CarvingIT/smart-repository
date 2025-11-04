@extends('layouts.app', ['activePage' => 'shared-links', 'titlePage' => __('Edit Shared Link')])

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title">{{ __('Edit Shared Link') }}</h4>
                        <p class="card-category">{{ __('Editing link for document: ') . $sharedLink->document->title }}</p>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('shared-links.update', $sharedLink) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group bmd-form-group">
                                        <label for="password" class="bmd-label-floating">{{ __('New Password (optional)') }}</label>
                                        <input type="password" name="password" id="password" class="form-control">
                                        <small class="form-text text-muted">Leave blank to keep the current password.</small>
                                        @if ($errors->has('password'))
                                            <span class="text-danger">{{ $errors->first('password') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group bmd-form-group">
                                        <label for="expires_at" class="bmd-label-static">{{ __('Expires At (optional)') }}</label>
                                        <input type="datetime-local" name="expires_at" id="expires_at" class="form-control" value="{{ old('expires_at', optional($sharedLink->expires_at)->format('Y-m-d\TH:i')) }}">
                                        @if ($errors->has('expires_at'))
                                            <span class="text-danger">{{ $errors->first('expires_at') }}</span>
                                        @endif
                                        <small class="form-text text-muted">Leave empty for link that never expires.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group bmd-form-group">
                                        <label for="permission_level" class="bmd-label-static">{{ __('Access Permission') }}</label>
                                        <select name="permission_level" id="permission_level" class="form-control">
                                            <option value="view" {{ old('permission_level', $sharedLink->permission_level) == 'view' ? 'selected' : '' }}>{{ __('View Only') }}</option>
                                            <option value="download" {{ old('permission_level', $sharedLink->permission_level) == 'download' ? 'selected' : '' }}>{{ __('Download Only') }}</option>
                                            <option value="both" {{ old('permission_level', $sharedLink->permission_level) == 'both' ? 'selected' : '' }}>{{ __('View and Download') }}</option>
                                        </select>
                                        @if ($errors->has('permission_level'))
                                            <span class="text-danger">{{ $errors->first('permission_level') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group bmd-form-group">
                                        <label for="description" class="bmd-label-floating">{{ __('Description (optional)') }}</label>
                                        <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $sharedLink->description) }}</textarea>
                                        @if ($errors->has('description'))
                                            <span class="text-danger">{{ $errors->first('description') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', $sharedLink->is_active) ? 'checked' : '' }}>
                                            {{ __('Link is Active') }}
                                            <span class="form-check-sign">
                                                <span class="check"></span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">{{ __('Update Link') }}</button>
                            <a href="{{ route('shared-links.index') }}" class="btn btn-default">{{ __('Cancel') }}</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
