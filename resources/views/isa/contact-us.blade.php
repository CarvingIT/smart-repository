@extends('layouts.app',['class'=> 'off-canvas-sidebar','title'=>'Smart Repository'])

@section('content')

<style>
	
    strong{
		font-weight:600;
        color: #f15c12e0;
        font-size: 18px;
	}
    p{
        font-size: 16px;
    }
</style>
<div class="container">
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
            <div class="card-header card-header-primary">
                <h6 class="card-title">{{ __('CONTACT us') }}</h6>
                    <!--div class="card-header-corner" style="margin-top:-4%;"><a href="/admin/collection-form/new"><img class="icon" src="/i/plus.png"/></a></div-->
              </div>	
              
              <br>
                <div class="card-body">
                    <div class="beautify">
				
                        <p>
                        <strong>International Solar Alliance Secretariat</strong>
                        </p>
                        <p><span class="bi bi-geo-alt-fill c-icon"></span> Surya Bhawan,</p>
                        <p class="beautify">National Institute of Solar Energy Campus Gwal Pahari,</p>
                        <p class="beautify">Faridabad-Gurugram Road,</p>
                        <p><span class="bi bi-telephone c-icon"></span> +91 124 362 3090/69</p>
                        <p><span class="bi bi-envelope c-icon"></span> info@isolaralliance.org</p>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
