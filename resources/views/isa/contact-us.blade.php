@extends('layouts.app',['class'=> 'off-canvas-sidebar','title'=>'Smart Repository'])

@section('content')

<style>
	strong{font-weight:bold;}
</style>
<div class="container">
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
            <div class="card-header card-header-primary">
                <h6 class="card-title">{{ __('Contact Us') }}</h6>
                    <!--div class="card-header-corner" style="margin-top:-4%;"><a href="/admin/collection-form/new"><img class="icon" src="/i/plus.png"/></a></div-->
              </div>		

                <div class="card-body">
                    <div class="beautify">
						<p>
						<strong>International Solar Alliance Secretariat</strong><br/>
						Surya Bhawan,<br/>
						National Institute of Solar Energy Campus Gwal Pahari,<br/>
						Faridabad-Gurugram Road,<br/>
						Gurugram, Haryana – 122003, India<br/>
						Tel: +91 124 362 3090/69<br/>
						Email: info@isolaralliance.org
						</p>	
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
