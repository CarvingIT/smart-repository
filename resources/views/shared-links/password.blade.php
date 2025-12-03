@extends('layouts.app', ['class' => 'off-canvas-sidebar', 'activePage' => 'password-protect', 'title' => __('Password Protected')])

@section('content')
<div class="container" style="height: auto;">
  <div class="row align-items-center">
    <div class="col-lg-4 col-md-6 col-sm-8 ml-auto mr-auto">
      <form class="form" method="POST" action="{{ route('shared-links.verify-password', $sharedLink->token) }}">
        @csrf
        <div class="card card-login card-hidden mb-3">
          <div class="card-header card-header-primary text-center">
            <h4 class="card-title"><strong>{{ __('Password Required') }}</strong></h4>
          </div>
          <div class="card-body">
            <p class="card-description text-center">{{ __('This content is password protected. Please enter your password to view it.') }}</p>
            <div class="bmd-form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">
                    <i class="material-icons">lock_outline</i>
                  </span>
                </div>
                <input type="password" name="password" class="form-control" placeholder="{{ __('Password...') }}" required>
              </div>
              @if ($errors->has('password'))
                <div id="password-error" class="error text-danger pl-3" for="password" style="display: block;">
                  <strong>{{ $errors->first('password') }}</strong>
                </div>
              @endif
            </div>
          </div>
          <div class="card-footer justify-content-center">
            <button type="submit" class="btn btn-primary btn-link btn-lg">{{ __('Unlock') }}</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
