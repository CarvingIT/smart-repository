@extends('layouts.app', ['class'=> 'off-canvas-sidebar', 'activePage' => 'botman-answers', 'titlePage' => __('Manage Chatbot Answers')])

@section('content')
    <div class="container">
    <div class="container-fluid">
      <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">{{ __('Botman Question/Answer') }}</h4>
                <p class="card-category"></p>
              </div>

              <div class="card-body">
                 <div class="row">
                  <div class="col-md-12 text-right">
                      <a href="/botman-answers" class="btn btn-sm btn-primary" title="Back to List"><i class="material-icons">arrow_back</i></a>
                  </div>
                </div>

                @if(empty(@$record))
                <form method="post" action="{{ route('botman-answers.store') }}" autocomplete="off">
                @else
                <form method="post" action="{{ route('botman-answers.update', @$record) }}" autocomplete="off">
                @endif
                @csrf
				@if (@$record)
                @method('PUT')
				@else
                @method('post')
				@endif
                <div class="form-group row bmd-form-group">
                  <div class="col-md-4">
                  <label class="col-md-12 col-form-label text-md-right">{{ __('Question') }}</label>
                  </div>
                  <div class="col-md-8">
                      <input class="form-control{{ $errors->has('question') ? ' is-invalid' : '' }}" name="question" id="question" type="text" placeholder="{{ __('Question') }}" value="{{ @$record->question }}" required="true" aria-required="true"/>
                      @if ($errors->has('question'))
                        <span id="question-error" class="error text-danger" for="question">{{ $errors->first('question') }}</span>
                      @endif
                  </div>
                </div>
                <div class="form-group row bmd-form-group">
                  <div class="col-md-4">
                  <label class="col-md-12 col-form-label text-md-right">{{ __('Answer') }}</label>
                  </div>
                  <div class="col-md-8">
					  <textarea name="answer" style="width:100%;" rows="10">{{ @$record->answer }}</textarea>
                      @if ($errors->has('answer'))
                        <span id="answer-error" class="error text-danger" for="answer">{{ $errors->first('answer') }}</span>
                      @endif
                  </div>
                </div>

              </div>
              <div class="card-footer ml-auto mr-auto">
                <button type="submit" class="btn btn-primary">{{ __('Save Record') }}</button>
              </div>
             </form>
            </div>
        </div>
      </div>
    </div>
</div>
@endsection
