@extends('layouts.app',['class'=> 'off-canvas-sidebar','title'=>'Publications'])

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
                <h6 class="card-title">{{ __('Publications') }}</h6>
                    <!--div class="card-header-corner" style="margin-top:-4%;"><a href="/admin/collection-form/new"><img class="icon" src="/i/plus.png"/></a></div-->
              </div>		

                <div class="card-body">
                <div class="beautify">
                        <p><strong>Publications</strong></p>
                        <p>This section offers a comprehensive collection of publications from ISA member countries. This diverse resource includes research papers, books, reports, case studies, theses, and more, spanning a broad spectrum of topics related to solar energy. </p>
                        <p><strong>Research Papers</strong></p>
                        <p>This sub-section has scholarly documents that provide an in-depth analysis, evaluation, or interpretation of a specific aspect of solar energy, based on empirical evidence. These papers are involved in thorough analysis and interpretation of data to contribute to the existing body of knowledge in academia.</p>
                        <p><strong>Case Studies</strong></p>
                        <p>This sub-section has documents that offer in-depth examinations of specific groups, communities, or regions. These methodical analyses explore various variables, providing comprehensive insights into real-world applications and the impact of solar energy initiatives on diverse units.</p>
                        <p><strong>Thesis/Dissertation</strong></p>
                        <p>This sub-section acts as a repository of scholarly work from graduate and postgraduate students. These Masters' theses and doctoral dissertations represent extensive research on various aspects of solar energy, offering valuable insights and contributions to the field of renewable energy and sustainability.</p>
                        <p><strong>Views</strong></p>
                        <p>This sub-section features insightful views and opinion pieces by sectoral experts. These pieces, often found in blogs, news sources, or magazines, offer subjective perspectives on solar energy. They can often provide valuable insights into industry trends, emerging issues, and expert viewpoints within the field.</p>
                        <p><strong>Books</strong></p>
                        <p>This sub-section has a curated collection of books dedicated to various aspects of solar energy. Covering topics ranging from solar technology and innovation to sustainable practices, this resource offers comprehensive knowledge and references for anyone interested in the field of solar energy.</p>
                        <p><strong>Reports</strong></p>
                        <p>This sub-section houses reports published by credible sources like international organizations, thinks tanks, research groups, governments etc offering a comprehensive analyses across the solar energy sector, designed to cater to the specific needs and interests of diverse audience.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
