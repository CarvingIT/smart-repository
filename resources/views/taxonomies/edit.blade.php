@extends('layouts.app', ['class'=> 'off-canvas-sidebar', 'activePage' => 'taxonomies-management', 'titlePage' => __('Taxonomies Management')])

@section('content')
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">{{ __('Update Taxonomies') }}</h4>
                <p class="card-category"></p>
              </div>

              <div class="card-body ">
                 <div class="row">
                  <div class="col-md-12 text-right">
                      <a href="/admin/taxonomiesmanagement" class="btn btn-sm btn-primary" title="Back to List"><i class="material-icons">arrow_back</i></a>
                  </div>
                </div>
                <form method="post" action="{{ route('taxonomies.update', $taxonomies) }}" autocomplete="off">
                @csrf
                @method('put')
                <div class="form-group row bmd-form-group">
                  <div class="col-md-4">
                  <label class="col-md-8 col-form-label text-md-right">{{ __('Taxonomies') }}</label>
                  </div>
                  <div class="col-md-6">
                      <input class="form-control{{ $errors->has('label') ? ' is-invalid' : '' }}" name="label" id="input-label" type="text" placeholder="{{ __('Comma separated list of Taxonomies') }}" value="{{ old('label', $taxonomies->label) }}" required="true" aria-required="true"/>
                      @if ($errors->has('label'))
                        <span id="name-error" class="error text-danger" for="input-label">{{ $errors->first('label') }}</span>
                      @endif
                  </div>
                </div>
              </div>
              <div class="card-footer ml-auto mr-auto">
                <button type="submit" class="btn btn-primary">{{ __('Update Taxonomies') }}</button>
              </div>
             </form>
            </div>
        </div>
      </div>
    </div>

@endsection
