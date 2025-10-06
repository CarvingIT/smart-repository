<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $sharedLink->document->title }} - {{ config('app.name', 'Laravel') }}</title>
    <link href="{{ asset('material') }}/css/material-dashboard.css?v=2.1.1" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body {
            background-color: #fafafa;
            font-family: "Roboto", "Helvetica", "Arial", sans-serif;
        }
        .document-viewer {
            background: #fafafa;
            min-height: 100vh;
        }
        .document-header {
            background: white;
            padding: 30px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .document-content {
            padding: 0 15px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .document-preview {
            background: white;
            border-radius: 6px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 3px 1px -2px rgba(0, 0, 0, 0.12), 0 1px 5px 0 rgba(0, 0, 0, 0.2);
            margin-bottom: 30px;
        }
        .file-icon {
            font-size: 80px !important;
            color: #9c27b0;
            margin-bottom: 20px;
        }
        .card-header-icon {
            border-radius: 3px;
            background: linear-gradient(60deg, #66bb6a, #4caf50);
            padding: 15px;
            margin-top: -20px;
            margin-right: 15px;
            float: left;
        }
        .card-header-icon i {
            width: 33px;
            height: 33px;
            text-align: center;
            line-height: 33px;
            color: #fff;
        }
        .info-area {
            margin-left: 70px;
        }
        .btn {
            border-radius: 3px;
            position: relative;
            padding: 12px 30px;
            margin: 10px 1px;
            font-size: 12px;
            font-weight: 400;
            text-transform: uppercase;
            letter-spacing: 0;
            will-change: box-shadow, transform;
            transition: box-shadow 0.2s cubic-bezier(0.4, 0, 1, 1), background-color 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-primary {
            background-color: #9c27b0;
            border-color: #9c27b0;
        }
        .btn-primary:hover {
            background-color: #89229b;
            border-color: #7b1fa2;
        }
    </style>
</head>
<body class="document-viewer">
    <div class="document-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-start">
                        <div class="card-header-icon">
                            <i class="material-icons">{{ $sharedLink->document->icon() }}</i>
                        </div>
                        <div class="info-area">
                            <h4 class="mb-1">{{ $sharedLink->document->title }}</h4>
                            <small class="text-muted">
                                Shared by {{ $sharedLink->user->name }} • 
                                {{ $sharedLink->document->file_ext ? strtoupper($sharedLink->document->file_ext) : 'Unknown' }} • 
                                {{ $sharedLink->document->file_size ? number_format($sharedLink->document->file_size / 1024, 1) . ' KB' : 'Unknown size' }}
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-right">
                    @if(in_array($sharedLink->permission_level, ['download', 'both']))
                        <a href="{{ route('shared-links.download', $sharedLink->token) }}" 
                           class="btn btn-primary">
                            <i class="material-icons">cloud_download</i>
                            Download
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="document-content">
        <div class="document-preview">
            @if(in_array($sharedLink->permission_level, ['view', 'both']))
                @php
                    $ext = strtolower($sharedLink->document->file_ext);
                    $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']);
                    $isPdf = $ext === 'pdf';
                    $isText = in_array($ext, ['txt', 'md', 'log']);
                    $storage_drive = empty($sharedLink->document->collection->storage_drive) ? 'local' : $sharedLink->document->collection->storage_drive;
                @endphp

                @if($isImage)
                    <div class="mb-4">
                        <img src="{{ Storage::disk($storage_drive)->url($sharedLink->document->path) }}" 
                             alt="{{ $sharedLink->document->title }}" 
                             class="img-fluid"
                             style="max-height: 80vh; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                    </div>
                @elseif($isPdf)
                    <div class="mb-4">
                        <iframe src="{{ Storage::disk($storage_drive)->url($sharedLink->document->path) }}" 
                                width="100%" 
                                height="600px"
                                style="border: none; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                        </iframe>
                    </div>
                @elseif($isText && $sharedLink->document->text_content)
                    <div class="text-left" style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                        <pre style="white-space: pre-wrap; font-family: 'Courier New', monospace;">{{ $sharedLink->document->text_content }}</pre>
                    </div>
                @else
                    <i class="material-icons file-icon">{{ $sharedLink->document->icon() }}</i>
                    <h5>{{ $sharedLink->document->title }}</h5>
                    <p class="text-muted">Preview not available for this file type.</p>
                    @if(in_array($sharedLink->permission_level, ['download', 'both']))
                        <p class="mt-3">
                            <a href="{{ route('shared-links.download', $sharedLink->token) }}" 
                               class="btn btn-primary btn-lg">
                                <i class="material-icons">cloud_download</i>
                                Download to View
                            </a>
                        </p>
                    @endif
                @endif
            @else
                <i class="material-icons file-icon">{{ $sharedLink->document->icon() }}</i>
                <h5>{{ $sharedLink->document->title }}</h5>
                <p class="text-muted">This document is available for download only.</p>
                <a href="{{ route('shared-links.download', $sharedLink->token) }}" 
                   class="btn btn-primary btn-lg">
                    <i class="material-icons">cloud_download</i>
                    Download
                </a>
            @endif

            @if($sharedLink->description)
                <hr class="mt-4">
                <div class="text-left">
                    <h6><strong>Description:</strong></h6>
                    <p>{{ $sharedLink->description }}</p>
                </div>
            @endif

            <hr class="mt-4">
            <div class="row text-center">
                <div class="col-md-4">
                    <small class="text-muted">
                        <strong>Created:</strong><br>
                        {{ $sharedLink->created_at->format('M j, Y H:i') }}
                    </small>
                </div>
                @if($sharedLink->expires_at)
                <div class="col-md-4">
                    <small class="text-muted">
                        <strong>Expires:</strong><br>
                        {{ $sharedLink->expires_at->format('M j, Y H:i') }}
                    </small>
                </div>
                @endif
                <div class="col-md-4">
                    <small class="text-muted">
                        <strong>Access Count:</strong><br>
                        {{ $sharedLink->access_count }}
                        @if($sharedLink->max_access_count)
                            / {{ $sharedLink->max_access_count }}
                        @endif
                    </small>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer mt-5" style="padding: 30px 0; background-color: #ffffff; box-shadow: 0 -2px 4px rgba(0,0,0,0.1);">
        <div class="container">
            <div class="text-center">
                <p class="text-muted mb-0">
                    Powered by <a href="{{ config('app.url') }}" class="text-primary">{{ config('app.name') }}</a>
                </p>
            </div>
        </div>
    </footer>
</body>

<!--   Core JS Files   -->
<script src="{{ asset('material') }}/js/core/jquery.min.js"></script>
<script src="{{ asset('material') }}/js/core/popper.min.js"></script>
<script src="{{ asset('material') }}/js/core/bootstrap-material-design.min.js"></script>
<script src="{{ asset('material') }}/js/material-dashboard.js?v=2.1.1"></script>

</html>
