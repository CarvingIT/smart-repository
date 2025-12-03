@extends('layouts.app', ['class'=> 'off-canvas-sidebar', 'title'=>'Create Shared Link'])

@section('content')
<div class="container">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <form method="post" action="{{ route('shared-links.store') }}" autocomplete="off" class="form-horizontal">
                @csrf
                <input type="hidden" name="document_id" value="{{ $document->id }}">
                <div class="card">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title">{{ __('Create Shared Link for: ') . $document->title }}</h4>
                    </div>
                    <div class="card-body">
              <div class="row">
                <div class="col-md-12 text-right">
                    <a href="{{ route('shared-links.index') }}" class="btn btn-sm btn-primary">{{ __('Back to list') }}</a>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-2 col-form-label">{{ __('Password (optional)') }}</label>
                <div class="col-sm-10">
                  <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
                    <input class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" id="input-password" type="password" placeholder="{{ __('Password') }}" value="" />
                    @if ($errors->has('password'))
                      <span id="password-error" class="error text-danger" for="input-password">{{ $errors->first('password') }}</span>
                    @endif
                  </div>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-2 col-form-label">{{ __('Expires At (optional)') }}</label>
                <div class="col-sm-10">
                  <div class="form-group{{ $errors->has('expires_at') ? ' has-danger' : '' }}">
                    <input class="form-control{{ $errors->has('expires_at') ? ' is-invalid' : '' }}" name="expires_at" id="input-expires_at" type="datetime-local" />
                    @if ($errors->has('expires_at'))
                      <span id="expires_at-error" class="error text-danger" for="input-expires_at">{{ $errors->first('expires_at') }}</span>
                    @endif
                  </div>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-2 col-form-label">{{ __('Permission Level') }}</label>
                <div class="col-sm-10">
                  <div class="form-group{{ $errors->has('permission_level') ? ' has-danger' : '' }}">
                    <select class="form-control{{ $errors->has('permission_level') ? ' is-invalid' : '' }}" name="permission_level" id="input-permission_level">
                        <option value="download" selected>View and Download</option>
                    </select>
                    @if ($errors->has('permission_level'))
                      <span id="permission_level-error" class="error text-danger" for="input-permission_level">{{ $errors->first('permission_level') }}</span>
                    @endif
                  </div>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-2 col-form-label">{{ __('Description (optional)') }}</label>
                <div class="col-sm-10">
                  <div class="form-group{{ $errors->has('description') ? ' has-danger' : '' }}">
                    <textarea class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}" name="description" id="input-description" placeholder="{{ __('Description') }}"></textarea>
                    @if ($errors->has('description'))
                      <span id="description-error" class="error text-danger" for="input-description">{{ $errors->first('description') }}</span>
                    @endif
                  </div>
                </div>
              </div>
                    </div>
                    <div class="card-footer ml-auto mr-auto">
                        <button type="submit" class="btn btn-primary">{{ __('Create Link') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
  </div>
</div>
@endsection
