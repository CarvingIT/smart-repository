@extends('layouts.app',['class' => 'off-canvas-sidebar','title'=>'Smart Repository','activePage'=>'features','titlePage'=>'Features'])

@section('content')
<div class="container">
<div class="container-fluid">

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-primary"><h4 class="card-title">Features<h4></div>
                <div class="card-body">
		<!--h2>Features</h2-->
<strong>Meta-searchable collections</strong>
<p>This feature enables you to define your own meta tags for creating meta-searchable collections. Incase you do not remember the exact file name also, this feature enables you to search for a specific content based on the meta- tags defined. Isn’t that wonderful?</p>

<strong>Document / File management</strong>
<p>
This is an exclusive feature that allows you to manage different files together at one place for better work efficiency. This software also allows you to maintain multiple revisions of the same file. Once a document is added into the collection, the document is stored in multiple versions.
</p>
		<strong>Distinctive User permissions:</strong>
<p>You can manage different users by providing them unique user privileges that show them what they can do with the document.  This time it’s not just Read and Write! You can enable customized permission for every user, meaning you can give read permission for some documents to a user and give write permission to other documents according to the user privileges. Fantastic! Right?</p>

                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
