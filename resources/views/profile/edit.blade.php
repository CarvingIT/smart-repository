@extends('layouts.app', ['class'=>'off-canvas-sidebar', 'activePage' => 'profile', 'titlePage' => __('User Profile')])

@section('content')
  <div class="container" style="height:auto;">
      <div class="row align-items-center">
		<div class="col-md-9 ml-auto mr-auto mb-3 text-center">
		</div>
        <div class="col-lg-6 col-md-6 col-sm-8 ml-auto mr-auto">
          <form method="post" action="{{ route('profile.update') }}" autocomplete="off" class="form-horizontal">
            @csrf
            @method('put')

            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">{{ __('Edit Profile') }}</h4>
                <!--<p class="card-category">{{ __('User information') }}</p>-->
              </div>
              <div class="card-body ">
                @if (session('status'))
                  <div class="row">
                    <div class="col-sm-12">
                      <div class="alert alert-success">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <i class="material-icons">close</i>
                        </button>
                        <span>{{ session('status') }}</span>
                      </div>
                    </div>
                  </div>
                @endif
                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('Name') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('name') ? ' has-danger' : '' }}">
                      <input class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" id="input-name" type="text" placeholder="{{ __('Name') }}" value="{{ old('name', auth()->user()->name) }}" required="true" aria-required="true"/>
                      @if ($errors->has('name'))
                        <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('name') }}</span>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('Email') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
                      <input class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" id="input-email" type="email" placeholder="{{ __('Email') }}" value="{{ old('email', auth()->user()->email) }}" required />
                      @if ($errors->has('email'))
                        <span id="email-error" class="error text-danger" for="input-email">{{ $errors->first('email') }}</span>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-footer ml-auto mr-auto">
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
              </div>
            </div>
          </form>
        </div>
      </div>

	  @if(auth()->user()->password != '! Created through SSO')
      <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-8 ml-auto mr-auto">
          <form method="post" action="{{ route('profile.password') }}" class="form-horizontal">
            @csrf
            @method('put')

            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">{{ __('Change Password') }}</h4>
                <!--<p class="card-category">{{ __('Password') }}</p>-->
              </div>
              <div class="card-body ">
                @if (session('status_password'))
                  <div class="row">
                    <div class="col-sm-12">
                      <div class="alert alert-success">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <i class="material-icons">close</i>
                        </button>
                        <span>{{ session('status_password') }}</span>
                      </div>
                    </div>
                  </div>
                @endif
                <div class="row">
                  <label class="col-sm-4 col-form-label" for="input-current-password">{{ __('Current Password') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('old_password') ? ' has-danger' : '' }}">
                      <input class="form-control{{ $errors->has('old_password') ? ' is-invalid' : '' }}" input type="password" name="old_password" id="input-current-password" placeholder="{{ __('Current Password') }}" value="" required />
                      @if ($errors->has('old_password'))
                        <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('old_password') }}</span>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-4 col-form-label" for="input-password">{{ __('New Password') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
                      <input class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" id="input-password" type="password" placeholder="{{ __('New Password') }}" value="" required />
                      @if ($errors->has('password'))
                        <span id="password-error" class="error text-danger" for="input-password">{{ $errors->first('password') }}</span>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-4 col-form-label" for="input-password-confirmation">{{ __('Confirm New Password') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group">
                      <input class="form-control" name="password_confirmation" id="input-password-confirmation" type="password" placeholder="{{ __('Confirm New Password') }}" value="" required />
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-footer ml-auto mr-auto">
                <button type="submit" class="btn btn-primary">{{ __('Change password') }}</button>
              </div>
            </div>
          </form>
        </div>
      </div>

	@endif 

      <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-8 ml-auto mr-auto">
          <div class="card">
            <div class="card-header card-header-primary">
              <h4 class="card-title">{{ __('Two-Factor Authentication (TOTP)') }}</h4>
            </div>
            <div class="card-body">
              <p>{{ __('Use an authenticator app like Authy to generate 6-digit verification codes for sensitive collections.') }}</p>

              @if(auth()->user()->hasTwoFactorEnabled())
                <div class="alert alert-success">
                  {{ __('2FA is currently enabled on your account.') }}
                </div>
                <form method="post" action="{{ route('profile.twofactor.disable') }}" class="form-horizontal">
                  @csrf
                  <button type="submit" class="btn btn-danger">{{ __('Disable 2FA') }}</button>
                </form>
              @else
                <div class="alert alert-warning">
                  {{ __('2FA is currently disabled.') }}
                </div>

                @if(empty($twoFactorSecret))
                  <form method="post" action="{{ route('profile.twofactor.setup') }}" class="form-horizontal">
                    @csrf
                    <button type="submit" class="btn btn-primary">{{ __('Generate QR') }}</button>
                  </form>
                @else
                  <div class="row">
                    <div class="col-md-6 text-center">
                      <img src="{{ $twoFactorQrCodeUrl }}" alt="{{ __('2FA QR code') }}" style="max-width:220px; width:100%; border:1px solid #ddd; padding:8px;" />
                    </div>
                    <div class="col-md-6">
                      <p><strong>{{ __('Manual setup key:') }}</strong></p>
                      <p style="word-break:break-all;">{{ $twoFactorSecret }}</p>
                      <small class="text-muted">{{ __('If QR scan does not work, add account manually using this key and 30-second TOTP.') }}</small>
                    </div>
                  </div>

                  <hr />

                  <form method="post" action="{{ route('profile.twofactor.enable') }}" class="form-horizontal">
                    @csrf
                    <div class="form-group{{ $errors->has('code') ? ' has-danger' : '' }}">
                      <label for="two_factor_code">{{ __('Enter 6-digit code from app') }}</label>
                      <input class="form-control{{ $errors->has('code') ? ' is-invalid' : '' }}" name="code" id="two_factor_code" type="text" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" value="{{ old('code') }}" required />
                      @if ($errors->has('code'))
                        <span class="error text-danger">{{ $errors->first('code') }}</span>
                      @endif
                    </div>
                    <button type="submit" class="btn btn-success">{{ __('Enable 2FA') }}</button>
                  </form>
                @endif
              @endif
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-8 ml-auto mr-auto">
          <form method="post" action="/user/regenerate-api-token" class="form-horizontal">
            @csrf
            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">{{ __('API Token') }}</h4>
              </div>
              <div class="card-body ">
              </div>
              <div class="col-md-12">
				{{ __('API token will be shown here.') }}<br/>
                  <div class="row">
                    <div class="col-md-12">
                    	<div class="flash-message">
                    	@foreach (['danger', 'warning', 'success', 'info'] as $msg)
                        	@if(Session::has('alert-' . $msg))
                        	<div class="alert alert-<?php echo $msg; ?>">
								<!--
                            	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            	<i class="material-icons">close</i>
                            	</button>
								-->
                            	<span>{{ Session::get('alert-' . $msg) }}</span>
                        	</div>
                        	@endif
                    	@endforeach
                    	</div>
                    </div>
                  </div>
			  </div>
              <div class="card-footer ml-auto mr-auto">
                <button type="submit" class="btn btn-primary">{{ __('Regenerate') }}</button>
              </div>
            </div>
          </form>
        </div>

      </div>
  </div>
@endsection
