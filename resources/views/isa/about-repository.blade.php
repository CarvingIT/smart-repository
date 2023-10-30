@extends('layouts.app',['class'=> 'off-canvas-sidebar','title'=>'FAQ'])

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
                <h6 class="card-title">{{ __('About the Regulation Repository') }}</h6>
                    <!--div class="card-header-corner" style="margin-top:-4%;"><a href="/admin/collection-form/new"><img class="icon" src="/i/plus.png"/></a></div-->
              </div>		
                <div class="card-body">
                    <div class="beautify">
                        <p>
                        The International Solar Alliance is committed to facilitating the global transition to sustainable and renewable energy sources, by making solar the preferred choice. ISA recognizes that regulations play a pivotal role in shaping the energy landscape of a nation and believes that by drawing inspiration from successful regulatory frameworks of a country, another country can lay the foundation for a brighter, more sustainable future.
                        </p> 
                        <p>
                        As a testament to this commitment, the ISA has spearheaded the project to collate laws, regulations, policies, guidelines, judgements, technical standards, publications and reports relating to the regulatory environment in our member countries, and make them available in this Regulation Repository on Solar Energy. These resources have been meticulously gathered to serve as a comprehensive and valuable knowledge hub, promoting the development and harmonization of solar regulations worldwide.
                        </p> 
                        <p>
                        ISA’s primary aim is to provide this repository for the benefit of its member countries, academicians, experts, and the public at large. Whether you are an official in a member nation, an academic seeking invaluable research material, an expert in the field, or a concerned citizen interested in the future of sustainable energy, this platform is designed to be your go-to resource. ISA aims to harness the power of knowledge and collaboration to drive solar energy adoption and contribute to a greener, more sustainable planet.
                        </p>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
