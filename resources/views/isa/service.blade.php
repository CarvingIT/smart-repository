@extends('layouts.app',['class'=> 'off-canvas-sidebar','title'=>'Services'])

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
                <h6 class="card-title">{{ __('Terms of Service') }}</h6>
                    <!--div class="card-header-corner" style="margin-top:-4%;"><a href="/admin/collection-form/new"><img class="icon" src="/i/plus.png"/></a></div-->
              </div>		

                <div class="card-body">
                <div class="beautify">
				
<p>
<strong>What is Terms of Service ?</strong>
</p>
<p>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
