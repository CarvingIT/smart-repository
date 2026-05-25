@extends('layouts.app', ['class' => 'off-canvas-sidebar', 'title' => 'Test SMTP Configuration'])

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header card-header-primary">
                    <h4 class="card-title">{{ __('Test SMTP Configuration') }}</h4>
                    <p class="card-category">{{ __('Send a test email to verify SMTP settings') }}</p>
                </div>

                <div class="card-body">
                    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                        @if(Session::has('alert-' . $msg))
                            <div class="alert alert-{{ $msg }}">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <i class="material-icons">close</i>
                                </button>
                                <span>{{ Session::get('alert-' . $msg) }}</span>
                            </div>
                        @endif
                    @endforeach

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <i class="material-icons">close</i>
                            </button>
                            <ul class="mb-0 pl-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="post" action="{{ route('admin.smtp.test.send') }}">
                        @csrf

                        <div class="form-group row align-items-center">
                            <div class="col-md-4 text-md-right mb-2 mb-md-0">
                                <label for="email" class="col-form-label">{{ __('Email Address') }}</label>
                            </div>
                            <div class="col-md-8">
                                <input
                                    id="email"
                                    type="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    name="email"
                                    value="{{ old('email') }}"
                                    placeholder="name@example.com"
                                    required
                                    autofocus
                                >
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4 d-flex justify-content-between align-items-center">
                                <a href="{{ url('/admin/system-info') }}" class="btn btn-secondary">{{ __('Back') }}</a>
                                <button type="submit" class="btn btn-primary">{{ __('Send Test Email') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection