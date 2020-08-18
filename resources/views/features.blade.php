@extends('layouts.app',['class' => 'off-canvas-sidebar','title'=>'Smart Repository','activePage'=>'features','titlePage'=>'Features'])

@section('content')
<div class="container">
<div class="container-fluid">

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-primary"><h4 class="card-title">Features<h4></div>
                <div class="card-body">
<h4>Powerful Seach</h4>
<p>Full-text search is the highlight of Smart Repository. A very simple, intuitive and powerful search feature will help you find the documents in seconds!</p>
<h4>Optical Character Recognition</h4>
<p>Text that appears on images/screenshots is indexed for search. The images thus become searchable through the search field just like your other documents. The team has been working on continuously improving this exciting feature.</p>
<h4>Meta-searchable collections</h4>
<p>This feature enables you to define your own meta tags thus enabling you in creating rich catalogs. The same tags automatically appear as filters that narrow down search of a document! We believe, this means a huge saving in your organizational time which would otherwise be spent in looking for documents. Moreover, the filters are persistent and they work with the content search making the overall search an extremely powerful feature.</p> 

<h4>Collaborative Document Management</h4>
<p>
This is an exclusive feature that allows you to manage different files together at one place collaboratively for better work efficiency. This software also allows you to maintain multiple revisions of the same file. The latest revision is searchable. 
</p>
<h4>Distinctive User permissions</h4>
<p>You can manage different users by providing them unique user privileges that show them what they can do with the document. Fantastic! Right?</p>

<h4>Customizable</h4>
<p>Being Open Source, this is completely customizable for your branding requirements.</p>
<h4>No Limits</h4>
<p>There are no limits on the number of users or collections or the documents. All limits are imposed by your storages.</p>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
