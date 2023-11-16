@extends('layouts.app', ['class'=> 'off-canvas-sidebar', 'activePage' => 'synonyms-management', 'titlePage' => __('Synonyms Management')])

@section('content')
    <div class="container">
    <div class="container-fluid">
      <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">{{ __('Add Synonyms') }}</h4>
                <p class="card-category"></p>
              </div>

              <div class="card-body">
                 <div class="row">
                  <div class="col-md-12 text-right">
                      <a href="/admin/synonymsmanagement" class="btn btn-sm btn-primary" title="Back to List"><i class="material-icons">arrow_back</i></a>
                  </div>
                </div>

                @if(empty($synonyms))
                <form method="post" action="{{ route('synonyms.store') }}" autocomplete="off">
                @else
                <form method="post" action="{{ route('synonyms.update', $synonyms) }}" autocomplete="off">
                @endif
                @csrf
                @method('post')
                <div class="form-group row bmd-form-group">
                  <div class="col-md-4">
                  <label class="col-md-12 col-form-label text-md-right">{{ __('Synonyms') }}</label>
                  </div>
                  <div class="col-md-8">
                      <input class="form-control{{ $errors->has('synonyms') ? ' is-invalid' : '' }}" name="synonyms" id="input-name" type="text" placeholder="{{ __('Comma separated list of synonyms') }}" value="{{ old('synonyms') }}" required="true" aria-required="true"/>
                      @if ($errors->has('synonyms'))
                        <span id="name-error" class="error text-danger" for="input-synonyms">{{ $errors->first('synonyms') }}</span>
                      @endif
                  </div>
                </div>
              </div>
              <div class="card-footer ml-auto mr-auto">
                <button type="submit" class="btn btn-primary">{{ __('Add Synonyms') }}</button>
              </div>
             </form>
            </div>
        </div>
      </div>
    </div>
</div>
@endsection
