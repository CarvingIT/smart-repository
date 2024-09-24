@extends('layouts.app', ['class'=> 'off-canvas-sidebar', 'activePage' => 'user-management', 'titlePage' => __('User Management')])

@section('content')
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">{{ __('Edit Template') }}</h4>
                <p class="card-category"></p>
              </div>

              <div class="card-body ">
                 <div class="row">
                  <div class="col-md-12 text-right">
                      <a href="/admin/srtemplatemanagement" class="btn btn-sm btn-primary" title="Back to List"><i class="material-icons">arrow_back</i></a>
                  </div>
                </div>
                @if(empty($template))
                <form method="post" action="{{ route('template.store') }}" autocomplete="off">
                @else
                <form method="post" action="{{ route('template.update', $template) }}" autocomplete="off">
                @endif
                @csrf
                @method('put')
                <div class="form-group row bmd-form-group">
                  <div class="col-md-4">
                  <label class="col-md-12 col-form-label text-md-right">{{ __('Template Name') }}</label>
                  </div>
                  <div class="col-md-8">
			<select class="form-control" name="template_name" id="input-template_name" />
			<option value="">Select Template</option>
                        <option value="Search Result" @if($template->template_name == 'Search Result') selected @endif>Search Result</option>
                        <option value="Document Details" @if($template->template_name == 'Document Details') selected @endif>Document Details</option>
                        </select>
                  </div>
                </div>

                <div class="form-group row bmd-form-group">
                  <div class="col-md-4">
                  <label class="col-md-12 col-form-label text-md-right">{{ __('Collection') }}</label>
                  </div>
                  <div class="col-md-6">
                        @php $collections = \App\Collection::all(); @endphp
                      <select class="form-control" name="collection_id" id="input-collection_id" />
                        <option value="">Select Collection</option>
                        @foreach($collections as $c)
                                <option value="{{ $c->id }}" @if($c->id == $template->collection_id) selected @endif>{{ ucfirst($c->name) }}</option>
                        @endforeach
                        </select>
                  </div>
                </div>

                <div class="form-group row">
                   <div class="col-md-4">
                  <label class="col-md-12 col-form-label text-md-right">{{ __('Html Code') }}</label>
                    </div>
                  <div class="col-md-8">
                      <input class="form-control{{ $errors->has('html_code') ? ' is-invalid' : '' }}" name="html_code" id="input-email" type="text" placeholder="{{ __('Html Code') }}" value="{{ $template->html_code }}" required />
                      @if ($errors->has('html_code'))
                        <span id="email-error" class="error text-danger" for="input-field">{{ $errors->first('html_code') }}</span>
                      @endif
                  </div>
                </div>
                <div class="form-group row">
                   <div class="col-md-4">
                  <label class="col-md-12 col-form-label text-md-right">{{ __('Description') }}</label>
                    </div>
                  <div class="col-md-8">
			<textarea class="form-control" rows='8' cols='10' name="description">{{ $template->description }}</textarea>
                  </div>
                </div>
              <div class="card-footer ml-auto mr-auto">
                <button type="submit" class="btn btn-primary">{{ __('Update Template') }}</button>
              </div>
             </form>
            </div>
        </div>
      </div>
    </div>
@endsection
