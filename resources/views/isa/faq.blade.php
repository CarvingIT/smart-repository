@extends('layouts.app',['class'=> 'off-canvas-sidebar','title'=>'FAQ'])

@section('content')
<style>
	strong{
		font-weight:600;
        color: #f15c12e0;
        font-size: 18px;
	}
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
                <h6 class="card-title">{{ __('FAQS') }}</h6>
                    <!--div class="card-header-corner" style="margin-top:-4%;"><a href="/admin/collection-form/new"><img class="icon" src="/i/plus.png"/></a></div-->
              </div>		

                <div class="card-body">
                        <div class="beautify">
				<br>
                            <p>
                            <strong>What is ISA?</strong>
                            </p>
                            <p>
                            The International Solar Alliance (ISA) is an action-oriented, member-driven, collaborative platform for increased deployment of solar energy technologies as a means for bringing energy access, ensuring energy security, and driving energy transition in its member countries. The ISA strives to develop and deploy cost-effective and transformational energy solutions powered by the sun to help member countries develop low-carbon growth trajectories, with particular focus on delivering impact in countries categorized as Least Developed Countries (LDCs) and the Small Island Developing States (SIDS). To know more, <a href="/about">click here.</a> 
                            </p>
                            <p>
                            <strong>What is the aim of the Regulation Repository?</strong>
                            </p>
                            <p>
                            The Regulation Repository aims to serve as a single window platform to provide all law, regulations, policies, guidelines etc relevant to the solar energy sector related to ISA member countries. It serves to facilitate understanding of the complex regulatory landscape, promoting compliance with established standards. This repository supports research efforts and provides valuable insights for researchers, policymakers, and industry experts. To know more, <a href="/about">click here.</a>
                            </p>
                            <p>
                            <strong>Who are the stakeholders of the Repository?</strong>
                            </p>
                            <p>
                            The stakeholders of the Repository are diverse and include a wide range of individuals and organizations. These stakeholders typically consist of government agencies, policymakers, legal experts, solar industry professionals, researchers, academics, environmental organizations, clean energy advocates, businesses involved in solar energy, and the general public. The repository caters to all those interested in understanding, complying with, or contributing to solar energy regulation, making it a valuable resource for a broad spectrum of stakeholders within the solar energy sector.
                            </p>
                            <p>
                            <strong>What resource types are available in Repository?</strong>
                            </p>
                            <p>
                            The Repository offers a rich array of resources that encompass legal, technical, and research documents related to solar energy. These resources include legal frameworks, regulations, case studies, reports, standards, academic research papers, theses, dissertations, expert opinions, and publications from ISA member countries. Additionally, it houses information on fiscal frameworks, pricing, taxation, and government initiatives in the solar energy sector. These diverse resources aim to provide a comprehensive and centralized source of knowledge for all aspects of solar energy regulation and industry development.
                            </p>
                            <p>
                            <strong>How do I navigate the Repository?</strong>
                            </p>
                            <p>
                            To navigate the Repository effectively, we recommend referring to the provided User Manual. This comprehensive guide will walk you through the repository's features, search functionalities, and how to access the specific resources you need, ensuring a seamless and productive experience.
                            </p>
                            <p>
                            <strong>A document that would be relevant to the repository is not uploaded here. How can I inform/share it?</strong>
                            </p>
                            <p>
                            If you have a document that you believe would be valuable for the Repository and it's not currently uploaded, we welcome your contribution. Please write to us at <a href="mailto:info@isolaralliance.org">info@isolaralliance.org</a> with the title “Additional Document for Regulation Repository”. We will work with you to make the document available in the Repository. Your contribution is highly appreciated as it helps enhance the repository's resources and benefit the broader solar energy community.
                            </p>
                            <p>
                            <strong>I want to collaborate and write an opinion piece. How do I do it?</strong>
                            </p>
                            <p>
                            We appreciate your interest in contributing an opinion piece to our Repository. To collaborate and submit your work, please contact us at <a href="mailto:info@isolaralliance.org">info@isolaralliance.org</a> and we will walk you through the submission process, including the editorial guidelines and any specific requirements for publication. When you write to us, we request you to title the email “Opinion piece for Regulation Repository” for faster and more efficient support. We look forward to featuring your valuable insights on solar energy regulation.
                            </p>
                            <p>
                            <strong>I have a query that is not listed here. How do I address it?</strong>
                            </p>
                            <p>
                            If your query is not listed in our FAQ section, we encourage you to reach out to us on <a href="mailto:info@isolaralliance.org">info@isolaralliance.org</a> for personalized assistance.
                            </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
