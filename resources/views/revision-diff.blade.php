@extends('layouts.app',['class'=> 'off-canvas-sidebar'])

@section('content')

<div class="container">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
            <div class="card-header card-header-primary">
                <h4 class="card-title">Showing diffrece in revisions of  "{{ $document->title }}"</h4></div>
                 <div class="card-body">
			<div class="row">
                  <div class="col-md-12 text-right">
                      <a href="/document/{{ $document->id }}/revisions" class="btn btn-sm btn-primary" title="Back to revisions"><i class="material-icons">arrow_back</i></a>
                  </div>
            </div>
            <div class="row">
                <div class="col-md-6"><h4>{{ $rev1->created_at }} ({{ $rev1->user->name }})</h4></div>
                <div class="col-md-6"><h4>{{ $rev2->created_at }} ({{ $rev2->user->name }})</h4></div>
            </div>
                @php
                // compare two strings line by line
                $diff = \App\Diff::compare($rev1->text_content, $rev2->text_content);
                $diff_table = \App\Diff::toTable($diff);
                echo $diff_table;
                @endphp
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
