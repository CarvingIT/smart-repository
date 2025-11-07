@extends('layouts.app', ['class' => 'off-canvas-sidebar', 'activePage' => 'shared-document', 'title' => __('Shared Document')])

@section('content')
<div class="container" style="height: auto;">
  <div class="row align-items-center">
    <div class="col-lg-8 col-md-10 ml-auto mr-auto">
        <div class="card card-login card-hidden mb-3">
            <div class="card-header card-header-primary text-center">
                <h4 class="card-title"><strong>{{ $sharedLink->document->title }}</strong></h4>
            </div>
            <div class="card-body">
                <p class="card-description text-center">{{ $sharedLink->description }}</p>
                
                <div class="text-center">
                    @if(in_array($sharedLink->permission_level, ['download', 'both']))
                        <a href="{{ route('shared-links.download', $sharedLink->token) }}" class="btn btn-primary btn-round">Download</a>
                    @endif
                </div>

                <div class="mt-4">
                    <iframe src="{{ route('documents.view', ['id' => $sharedLink->document->id, 'collection_id' => $sharedLink->document->collection_id]) }}" width="100%" height="600px" frameborder="0"></iframe>
                </div>
            </div>
        </div>
    </div>
  </div>
</div>
@endsection
