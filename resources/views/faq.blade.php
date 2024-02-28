@extends('layouts.app',['class' => 'off-canvas-sidebar','title'=>'Smart Repository','activePage'=>'faq','titlePage'=>'FAQ'])
@section('content')
<div class="container">
<div class="container-fluid">

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-primary"><h4 class="card-title">FAQ</h4></div>

                <div class="card-body">
		<!--h2>FAQ</h2-->
<h4>Which document types are supported ?</h4>
<p>Virtually, any! We have tested indexing of content text with docs, pdfs, text files, presentations, spreadsheets and even screenshots containing text.</p>
<h4>Can we classify the content under a collection further ?</h4>
<p>Yes, you can do this with the help of custom meta descriptors (fields) for your collection. The same fields will appear as filters making it easy for your users to narrow down on the search of a document. The filters are persistent once set and work with the content search.</p>
		<h4>What are User permissions?</h4>
<p>You can manage different users by providing them unique user privileges that show them what they can do with the document.  This time it’s not just Read and Write! You can enable customized permission for every user, meaning you can give read permission for some documents to a user and give write permission to other documents according to the user privileges. Fantastic! Right?</p>

The following permissions can be given to a user:

<ul>
<li>MAINTAINER: This gives the user the permission to maintain the document entirely.</li>
<li>CREATE: This gives the user the permission to create a new document.</li>
<li>EDIT_ANY: This gives the user the permission to edit any of the documents in the collection.</li>
<li>EDIT_OWN: This gives the user the permission to edit only those documents from the collection that has been created by the user.</li>
<li>DELETE_ANY: This gives the user the permission to delete any of the documents in the collection.</li>
<li>DELETE_OWN: This gives the user the permission to delete only those documents from the collection that has been created by the user.</li>
<li>VIEW: This gives the user the permission to view all documents in the collection.</li>
</ul>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
