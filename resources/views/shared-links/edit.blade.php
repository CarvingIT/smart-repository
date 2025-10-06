@extends('layouts.app', ['activePage' => 'shared-links', 'titlePage' => __('Edit Shared Link')])

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12 d-flex justify-content-center">
                <div class="card mt-5 shadow-sm w-100" style="max-width: 600px;">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title mb-0">{{ __('Edit Shared Link') }}</h4>
                        <small class="card-category">
                            {{ __('Edit shared link for: ') . ($sharedLink->document->title ?? 'Unknown Document') }}
                        </small>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('shared-links.update', $sharedLink) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            {{-- Password --}}
                            <div class="form-group mb-4">
                                <label for="password" class="form-label">{{ __('Password (leave blank to keep unchanged)') }}</label>
                                <input type="password" name="password" id="password" class="form-control">
                                @error('password')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Expires At --}}
                            <div class="form-group mb-4">
                                <label for="expires_at" class="form-label">{{ __('Expires At (optional)') }}</label>
                                <input type="datetime-local" name="expires_at" id="expires_at" class="form-control"
                                    value="{{ optional($sharedLink->expires_at)->format('Y-m-d\TH:i') }}">
                                @error('expires_at')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Checkboxes --}}
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                            {{ $sharedLink->is_active ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            {{ __('Active') }}
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="downloadable" name="downloadable" value="1"
                                            {{ $sharedLink->downloadable ? 'checked' : '' }}>
                                        <label class="form-check-label" for="downloadable">
                                            {{ __('Allow Download') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            {{-- Buttons --}}
                            <div class="d-flex justify-content-start gap-2 flex-wrap">
                                <button type="submit" class="btn btn-primary btn-sm">{{ __('UPDATE LINK') }}</button>
                                <a href="{{ route('shared-links.index') }}" class="btn btn-secondary btn-sm">{{ __('Cancel') }}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
