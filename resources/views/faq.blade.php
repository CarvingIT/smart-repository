@extends('layouts.app',['title'=>'Smart Repository','activePage'=>'FAQ','titlePage'=>'FAQ'])

@section('content')
<div class="container" style="height:auto; margin-top:5%;">
<div class="container-fluid">

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-primary"><h4 class="card-title"><a href="/">Home</a> :: FAQ</h4></div>

                <div class="card-body">
		<h2>FAQ</h2>
		<strong>Distinctive User permissions:</strong>
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
