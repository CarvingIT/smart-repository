@extends('layouts.app', ['class'=> 'off-canvas-sidebar', 'activePage' => 'taxonomies-management', 'titlePage' => __('Taxonomies Management')])

@section('content')
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">{{ __('Update Taxonomy') }}</h4>
                <p class="card-category"></p>
              </div>

              <div class="card-body ">
                 <div class="row">
                  <div class="col-md-12 text-right">
                      <a href="/admin/taxonomiesmanagement" class="btn btn-sm btn-primary" title="Back to List"><i class="material-icons">arrow_back</i></a>
                  </div>
                </div>
                <form method="post" action="{{ route('taxonomies.update', $taxonomy) }}" autocomplete="off">
                @csrf
                @method('put')
		          <div class="form-group row bmd-form-group">
                <div class="col-md-4">
                  <label class="col-md-12 col-form-label text-md-right">{{ __('Parent') }}</label>
                  </div>

                  <div class="col-md-8">

                    <select class="form-control{{ $errors->has('parent_id') ? ' is-invalid' : '' }}" name="parent_id" id="pId">
                      <option value="">Select Parent</option>
                        @foreach($parent_taxonomies as $p)
                      <option value="{{ $p->id }}" @if($p->id == $taxonomy->parent_id) selected @endif>{{ $p->label }} </option>
                        @endforeach
                    </select>
                  </div>
                </div>

                <div class="form-group row bmd-form-group">
                  <div class="col-md-4">
                  <label class="col-md-12 col-form-label text-md-right">{{ __('Tag') }}</label>
                  </div>
                  <div class="col-md-8">
                      <input class="form-control{{ $errors->has('label') ? ' is-invalid' : '' }}" name="label" id="input-label" type="text" placeholder="{{ __('Tag') }}" value="{{ old('label', $taxonomy->label) }}" required="true" aria-required="true"/>
                      @if ($errors->has('label'))
                        <span id="name-error" class="error text-danger" for="input-label">{{ $errors->first('label') }}</span>
                      @endif
                  </div>
                </div>
              </div>
              <div class="card-footer ml-auto mr-auto">
                <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
              </div>
             </form>
            </div>
        </div>
      </div>
    </div>

@endsection
