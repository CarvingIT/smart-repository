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
                <h4 class="card-title">{{ __('About Repository') }}</h4>
                    <!--div class="card-header-corner" style="margin-top:-4%;"><a href="/admin/collection-form/new"><img class="icon" src="/i/plus.png"/></a></div-->
              </div>		

                <div class="card-body">
                    <div>
				
<p>
<strong>About ISA Repository </strong>
</p>
<p>
The International Solar Alliance (ISA) is an action-oriented, member-driven, collaborative platform for increased deployment of solar energy technologies as a means for bringing energy access, ensuring energy security, and driving energy transition in its member countries.
</p> 
<p>
The ISA strives to develop and deploy cost-effective and transformational energy solutions powered by the sun to help member countries develop low-carbon growth trajectories, with particular focus on delivering impact in countries categorized as Least Developed Countries (LDCs) and the Small Island Developing States (SIDS). Being a global platform, ISA’s partnerships with multilateral development banks (MDBs), development financial institutions (DFIs), private and public sector orgnaisations, civil society and other international institutions is key to delivering the change its seeks to see in the world going ahead.
</p> 
<p>
The ISA is guided by its ‘Towards 1000’ strategy which aims to mobilise USD 1,000 billion of investments in solar energy solutions by 2030, while delivering energy access to 1,000 million people using clean energy solutions and resulting in installation of 1,000 GW of solar energy capacity. This would help mitigate global solar emissions to the tune of 1,000 million tonnes of CO2 every year. For meeting these goals, the ISA takes a programmatic approach. Currently, the ISA has 9 comprehensive programmes, each focusing on a distinct application that could help scale deployment of solar energy solutions. Activities under the programmes focuses on 3 priority areas – Analytics & Advocacy, Capacity Building, and Programmatic Support, that help create a favourable environment for solar energy investments to take root in the country.
</p> 
<p>
The ISA was conceived as a joint effort by India and France to mobilize efforts against climate change through deployment of solar energy solutions. It was conceptualized on the sidelines of the 21st Conference of Parties (COP21) to the United Nations Framework Convention on Climate Change (UNFCCC) held in Paris in 2015. With the amendment of its Framework Agreement in 2020, all member states of the United Nations are now eligible to join the ISA. At present, 116 countries are signatories to the ISA Framework Agreement, of which 94 countries have submitted the necessary instruments of ratification to become full members of the ISA.
</p>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
