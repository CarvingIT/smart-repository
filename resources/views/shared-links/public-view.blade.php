@extends('layouts.app',['class' => 'off-canvas-sidebar','title'=>'Shared Document','activePage'=>'shared','titlePage'=>'Shared Document'])

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title">{{ $sharedLink->document->title }}</h4>
                        <p class="card-category">
                            Shared by {{ $sharedLink->user->name }} • 
                            {{ $sharedLink->document->file_ext ? strtoupper($sharedLink->document->file_ext) : 'Unknown' }} • 
                            {{ $sharedLink->document->file_size ? number_format($sharedLink->document->file_size / 1024, 1) . ' KB' : 'Unknown size' }}
                        </p>
                        @if(in_array($sharedLink->permission_level ?? 'both', ['download', 'both']))
                            <div class="card-actions">
                                <a href="{{ route('shared-links.download', $sharedLink->token) }}" 
                                   class="btn btn-white btn-round">
                                    <i class="material-icons">cloud_download</i>
                                    Download
                                </a>
                            </div>
                        @endif
                    </div>
                    <div class="card-body">
                        @php
                            $ext = strtolower($sharedLink->document->file_ext);
                            $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']);
                            $isPdf = $ext === 'pdf';
                            $isText = in_array($ext, ['txt', 'md', 'log']);
                            $storage_drive = empty($sharedLink->document->collection->storage_drive) ? 'local' : $sharedLink->document->collection->storage_drive;
                        @endphp

                        <div class="document-preview-container">
                            @if($isImage)
                                <div class="text-center mb-4">
                                    <img src="{{ Storage::disk($storage_drive)->url($sharedLink->document->path) }}" 
                                         alt="{{ $sharedLink->document->title }}" 
                                         class="img-fluid"
                                         style="max-height: 80vh; max-width: 100%; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                                </div>
                            @elseif($isPdf)
                                <div class="mb-4">
                                    <iframe src="{{ Storage::disk($storage_drive)->url($sharedLink->document->path) }}" 
                                            width="100%" 
                                            height="800px"
                                            style="border: none; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                                    </iframe>
                                </div>
                            @elseif($isText && $sharedLink->document->text_content)
                                <div class="card">
                                    <div class="card-header">
                                        <h6>Document Content</h6>
                                    </div>
                                    <div class="card-body">
                                        <pre style="white-space: pre-wrap; font-family: 'Courier New', monospace; background: #f8f9fa; padding: 20px; border-radius: 8px;">{{ $sharedLink->document->text_content }}</pre>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="material-icons" style="font-size: 80px; color: #9c27b0;">{{ $sharedLink->document->icon() }}</i>
                                    <h5 class="mt-3">{{ $sharedLink->document->title }}</h5>
                                    <p class="text-muted">Preview not available for this file type.</p>
                                    @if(in_array($sharedLink->permission_level ?? 'both', ['download', 'both']))
                                        <p class="mt-3">
                                            <a href="{{ route('shared-links.download', $sharedLink->token) }}" 
                                               class="btn btn-primary btn-lg">
                                                <i class="material-icons">cloud_download</i>
                                                Download to View
                                            </a>
                                        </p>
                                    @endif
                                </div>
                            @endif
                        </div>

                        @if($sharedLink->description)
                            <hr class="mt-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6><strong>Description</strong></h6>
                                </div>
                                <div class="card-body">
                                    <p>{{ $sharedLink->description }}</p>
                                </div>
                            </div>
                        @endif

                        <hr class="mt-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card card-stats">
                                    <div class="card-body">
                                        <div class="d-flex">
                                            <div class="icon-big text-center">
                                                <i class="material-icons text-info">schedule</i>
                                            </div>
                                            <div class="numbers">
                                                <p class="card-category">Created</p>
                                                <p class="card-title">{{ $sharedLink->created_at->format('M j, Y H:i') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if($sharedLink->expires_at)
                            <div class="col-md-4">
                                <div class="card card-stats">
                                    <div class="card-body">
                                        <div class="d-flex">
                                            <div class="icon-big text-center">
                                                <i class="material-icons text-warning">access_time</i>
                                            </div>
                                            <div class="numbers">
                                                <p class="card-category">Expires</p>
                                                <p class="card-title">{{ $sharedLink->expires_at->format('M j, Y H:i') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="col-md-4">
                                <div class="card card-stats">
                                    <div class="card-body">
                                        <div class="d-flex">
                                            <div class="icon-big text-center">
                                                <i class="material-icons text-success">file_copy</i>
                                            </div>
                                            <div class="numbers">
                                                <p class="card-category">File Type</p>
                                                <p class="card-title">{{ $sharedLink->document->file_ext ? strtoupper($sharedLink->document->file_ext) : 'Unknown' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
