@extends('layouts.app',['class'=> 'off-canvas-sidebar','title'=>'Technical Standards'])

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
                <h4 class="card-title">{{ __('Technical Standards') }}</h4>
                    <!--div class="card-header-corner" style="margin-top:-4%;"><a href="/admin/collection-form/new"><img class="icon" src="/i/plus.png"/></a></div-->
              </div>		

                <div class="card-body">
                    <div>
                        <p><strong>Technical Standards</strong></p>
                        <p>This section enlists various standards developed by governmental and non-governmental standardization organizations relating to technical specifications crucial for maintaining consistency, quality, and safety in solar energy fields. These standards foster uniformity and best practices across solar technology design, production, processes, and services, promoting industry excellence. </p>
                        <p><strong>Process Standards</strong></p>
                        <p>This sub-section indexes procedures implemented within ISA member countries for maintaining product compliance and quality across the solar energy industry. These standards ensure the consistent achievement of desired outcomes, particularly in adhering to technical marketplace regulations. </p>
                        <p><strong>Product Standards</strong></p>
                        <p>This sub-section catalogues the technical directives delineating specifications and criteria that define the attributes and characteristics of products within ISA member countries. These standards are essential for ensuring product quality and compliance with industry norms in the solar energy sector. </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
