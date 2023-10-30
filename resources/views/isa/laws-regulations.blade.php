@extends('layouts.app',['class'=> 'off-canvas-sidebar','title'=>'LAWS'])

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
                <h5 class="card-title">{{ __('Laws, Regulations & Policies') }}</h5>
                    <!--div class="card-header-corner" style="margin-top:-4%;"><a href="/admin/collection-form/new"><img class="icon" src="/i/plus.png"/></a></div-->
              </div>		

                <div class="card-body">
                    <div class="beautify">
                        <p><strong>Legal and Regulatory</strong></p>
                        <p>This section provides a comprehensive repository of laws, rules, and regulations governing clean and renewable energy, with a specific emphasis on solar energy, in ISA member countries. Explore this resource to gain insights into the legal framework shaping the sustainable energy transition across our diverse member nations.</p>
                        <p><strong>Energy Laws & Regulations</strong></p>
                        <p>This sub-section offers a thorough compilation of case laws, statutes, rules, regulations, and acts, all pivotal in shaping the clean and renewable energy sector with a specific focus on solar energy.</p>
                        <p><strong>Plans, Policies & Programmes</strong></p>
                        <p>This sub-section includes the documents that form the backbone of public administration and government management like strategic plans, policies, roadmaps, guidelines, programs, and missions, essential for effective governance and the pursuit of national objectives and aspirations.</p>
                        <p><strong>Tariff Policies and Framework</strong></p>
                        <p>This sub-section houses documents which provide valuable insights into tariff structures and guidelines set by governments and regulatory bodies, focusing on renewable energy, particularly solar power.</p>
                        <p><strong>Fiscal Frameworks</strong></p>
                        <p>This sub-section provides policies and regulations that dictate a government's fiscal management. It encompasses crucial aspects such as subsidies, investments, and expenditure decisions relevant to the sector.</p>
                        <p><strong>Financial Regulations</strong></p>
                        <p>This sub-section offers a wealth of information on incentives and regulations specifically tailored to the solar sector. Discover the financial frameworks, rules, and incentives that drive investment and sustainability in solar energy.</p>
                        <p><strong>Antitrust and Competition Legislations</strong></p>
                        <p>This sub-section is a comprehensive resource that outlines legal frameworks aimed at fostering fair competition and preventing anticompetitive practices in markets which impact the solar energy investments.</p>
                        <p><strong>Environmental Frameworks</strong></p>
                        <p>This sub-section compiles the rules, guidelines, and principles set forth by governments and international bodies to safeguard the environment which have a bearing on the deployment of solar energy.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
