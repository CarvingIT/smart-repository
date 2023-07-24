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
