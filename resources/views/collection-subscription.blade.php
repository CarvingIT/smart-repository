@extends('layouts.app',['class'=> 'off-canvas-sidebar'])

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header card-header-primary">
                    <h4 class="card-title">{{ $collection->name }} :: {{ __('SOAP based subscription') }}</h4>
                </div>
                <div class="card-body">
                    @if(session('alert-danger'))
                        <div class="alert alert-danger">{{ session('alert-danger') }}</div>
                    @endif
                    <p>{{ __('Enter the subscriber details below. A challan will be generated and you will be sent to the payment gateway.') }}</p>

                    <div class="mb-3">
                        <strong>{{ __('Amount') }}:</strong>
                        @if(isset($subscriptionConfig->soap_subscription_amount) && $subscriptionConfig->soap_subscription_amount !== '')
                            {{ $subscriptionConfig->soap_subscription_amount }}
                        @else
                            {{ __('Not configured') }}
                        @endif
                    </div>

                    <form method="POST" action="/collection/{{ $collection->id }}/soap-subscription">
                        @csrf
                        <div class="row align-items-center mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-right" style="color: black;">{{ __('Name') }} :</label>
                            <div class="col-md-5">
                                <div class="form-group bmd-form-group m-0" style="padding-top: 0;">
                                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', optional(auth()->user())->name) }}" required />
                                </div>
                            </div>
                        </div>
                        <div class="row align-items-center mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-right" style="color: black;">{{ __('Email') }} :</label>
                            <div class="col-md-5">
                                <div class="form-group bmd-form-group m-0" style="padding-top: 0;">
                                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email', optional(auth()->user())->email) }}" required />
                                </div>
                                @error('email')
                                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="row align-items-center mb-3">
                            <label for="mobile" class="col-md-4 col-form-label text-md-right" style="color: black;">{{ __('Mobile No.') }} :</label>
                            <div class="col-md-5">
                                <div class="form-group bmd-form-group m-0" style="padding-top: 0;">
                                    <input type="text" name="mobile" id="mobile" class="form-control" value="{{ old('mobile') }}" placeholder="{{ __('e.g. 9876543210') }}" required />
                                </div>
                                @error('mobile')
                                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">{{ __('Generate Challan') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection