@extends('layouts.app', ['class' => 'off-canvas-sidebar', 'activePage' => '2fa-login', 'titlePage' => __('2FA Login Verification')])

@section('content')
<div class="container" style="height:auto;">
    <div class="row align-items-center">
        <div class="col-lg-5 col-md-7 col-sm-9 ml-auto mr-auto">
            <form class="form" method="POST" action="{{ route('login.two-factor.verify') }}">
                @csrf

                <div class="card card-login card-hidden mb-3">
                    <div class="card-header card-header-primary text-center">
                        <h6 class="card-title"><strong>{{ __('Two-Factor Authentication') }}</strong></h6>
                    </div>
                    <div class="card-body">
                        <p class="card-description text-center">
                            {{ __('Your account is protected with two-factor authentication. Please enter the 6-digit code from your authenticator app (Authy, Google Authenticator, Microsoft Authenticator, etc.).') }}
                        </p>

                        <input type="hidden" name="intended" value="{{ old('intended', $intended) }}" />

                        <div class="bmd-form-group{{ $errors->has('code') ? ' has-danger' : '' }} mt-3">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="material-icons">security</i>
                                    </span>
                                </div>
                                <input type="text" name="code" class="form-control" placeholder="{{ __('Enter 6-digit code') }}" value="{{ old('code') }}" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" required autofocus>
                            </div>
                            @if ($errors->has('code'))
                                <div class="error text-danger pl-3" style="display:block;">
                                    <strong>{{ $errors->first('code') }}</strong>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer justify-content-center">
                        <button type="submit" class="btn btn-primary btn-sm btn-lg">{{ __('Verify & Continue') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
