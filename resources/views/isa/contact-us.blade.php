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
                <h6 class="card-title">{{ __('CONTACT US') }}</h6>
                    <!--div class="card-header-corner" style="margin-top:-4%;"><a href="/admin/collection-form/new"><img class="icon" src="/i/plus.png"/></a></div-->
              </div>		

                <div class="card-body">
                    <div>
						<p> 
						<strong>International Solar Alliance Secretariat</strong><br/>
						<span class="bi bi-geo-alt-fill c-icon"></span> Surya Bhawan,<br/>
						<span class="ct-sub"></span> National Institute of Solar Energy Campus Gwal Pahari,<br/>
						<span class="ct-sub"></span> Faridabad-Gurugram Road,<br/>
						<span class="ct-sub"></span> Gurugram, Haryana – 122003, India<br/>
						<span class="bi bi-telephone c-icon"></span> +91 124 362 3090/69<br/>
                        <span class="bi bi-envelope c-icon"></span> info@isolaralliance.org
						</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
