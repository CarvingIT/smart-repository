@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h4>Upload</h4></div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

<p><strong>Please upload your Resume.</strong></p>
<form name="upload_resume_form" action="upload_resume" method="post" enctype="multipart/form-data">
@csrf()
<table><tr>
<td>
<input type="file" name="resume"></td>
<td>
<button type="submit">Upload Resume</button>
</td>
</tr></table>
</form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
