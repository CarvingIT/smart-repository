@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><a href="/">Home</a> :: Features</div>

                <div class="card-body">
                    <h3>Features</h3>
                    <ul>
                    <li>Collaborative document/file management</li>
                    <li>User permissions that define what they can do with the documents</li>
                    <li>Full Text Search through the content of the documents</li>
                    <li>Optical Character Recognition (OCR) capabilities for photos (PNG/JPEG files)</li>
                    <li>Define your own meta tags for creating meta-searchable collections</li>
                    <li>Advanced search based on the meta fields you define</li>
                    <li>Revision history</li>
                    <li>Activity reports</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
