@extends('layouts.app',['class'=> 'off-canvas-sidebar','title'=>'Privacy Policy'])

@section('content')
<style>
	strong{
		font-weight:bold;
	}
</style>

<div class="container">
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
            <div class="card-header card-header-primary">
                <h5 class="card-title">{{ __('Privacy policy') }}</h6>
                    <!--div class="card-header-corner" style="margin-top:-4%;"><a href="/admin/collection-form/new"><img class="icon" src="/i/plus.png"/></a></div-->
              </div>		

                <div class="card-body">
                <div class="beautify">
				
<p>
<strong>Privacy policy ?</strong>
</p>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
