@extends('layouts.app',['class'=> 'off-canvas-sidebar','title'=>'Smart Repository'])

@section('content')

<style>
	p{
font-size: 15px;
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
                    <p><span class="bi bi-house-fill c-icon"></span> International Solar Alliance Secretariat</p>
                        <p><span class="bi bi-geo-alt-fill c-icon"></span> Surya Bhawan,</p>
                        <p class="beautify">National Institute of Solar Energy Campus Gwal Pahari,</p>
                        <p class="beautify">Faridabad-Gurugram Road,</p>
                        <p class="beautify">Gurugram, Haryana – 122003, India</p>
                        <p><span class="bi bi-telephone c-icon"></span> +91 124 362 3090/69</p>
                        <p><span class="bi bi-envelope c-icon"></span> <a href="mailto:info@isolaralliance.org">info@isolaralliance.org</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
