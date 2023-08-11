@extends('layouts.app', ['class'=> 'off-canvas-sidebar', 'activePage' => 'user-management', 'titlePage' => __('User Management')])

@section('content')
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">{{ __('Add User') }}</h4>
                <p class="card-category"></p>
              </div>

              <div class="card-body ">
                 <div class="row">
                  <div class="col-md-12 text-right">
                      <a href="/admin/usermanagement" class="btn btn-sm btn-primary" title="Back to List"><i class="material-icons">arrow_back</i></a>
                  </div>
                </div>
                <form method="post" action="{{ route('user.update', $user) }}" autocomplete="off">
                @csrf
                @method('put')
                <div class="form-group row bmd-form-group">
                  <div class="col-md-4">
                  <label class="col-md-8 col-form-label text-md-right">{{ __('Name') }}</label>
                  </div>
                  <div class="col-md-6">
                      <input class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" id="input-name" type="text" placeholder="{{ __('Name') }}" value="{{ old('name', $user->name) }}" required="true" aria-required="true"/>
                      @if ($errors->has('name'))
                        <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('name') }}</span>
                      @endif
                  </div>
                </div>
                <div class="form-group row">
                   <div class="col-md-4">
                  <label class="col-md-8 col-form-label text-md-right">{{ __('Email') }}</label>
                    </div>
                  <div class="col-md-6">
                      <input class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" id="input-email" type="email" placeholder="{{ __('Email') }}" value="{{ old('email', $user->email) }}" required />
                      @if ($errors->has('email'))
                        <span id="email-error" class="error text-danger" for="input-email">{{ $errors->first('email') }}</span>
                      @endif
                  </div>
                </div>
                <div class="form-group row">
                   <div class="col-md-4">
                  <label class="col-md-8 col-form-label text-md-right" for="input-password">{{ __(' Password') }}</label>
                    </div>
                  <div class="col-md-6">
                      <input class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" input type="password" name="password" id="input-password" placeholder="{{ __('Password') }}" value="" @if(empty($user->password)) required @endif />
                      @if ($errors->has('password'))
                        <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('password') }}</span>
                      @endif
                  </div>
                </div>
                <div class="form-group row">
                  <div class="col-md-4">
                  <label class="col-md-8 col-form-label text-md-right" for="input-password-confirmation">{{ __('Confirm Password') }}</label>
                  </div>
                  <div class="col-md-6">
                      <input class="form-control" name="password_confirmation" id="input-password-confirmation" type="password" placeholder="{{ __('Confirm Password') }}" value="" @if(empty($user->password)) required @endif/>
                  </div>
                </div>
                <div class="form-group row">
                  <div class="col-md-4">
                  <label class="col-md-8 col-form-label text-md-right" for="input-password-confirmation">{{ __('Role') }}</label>
                  </div>
                  <div class="col-md-6">
			@php $user_role_id = $user->userrole($user->id); @endphp
                      <select class="form-control" name="user_role" id="input-user-role" />
			<option value="">Select Role</option>
			@foreach($roles as $role)
				<option value="{{ $role->id }}" @if($role->id == $user_role_id) selected @endif>{{ ucfirst($role->name) }}</option>
			@endforeach
			</select>
                  </div>
                </div>
              </div>
              <div class="card-footer ml-auto mr-auto">
                <button type="submit" class="btn btn-primary">{{ __('Add User') }}</button>
              </div>
             </form>
            </div>
        </div>
      </div>
    </div>
@endsection
