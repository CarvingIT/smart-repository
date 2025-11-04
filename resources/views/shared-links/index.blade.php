@extends('layouts.app', ['activePage' => 'shared-links', 'titlePage' => __('Shared Links')])

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card" style="margin-top: 120px;">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title">{{ __('Shared Links') }}</h4>
                        <p class="card-category">{{ __('Manage your shared document links') }}</p>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger" role="alert">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if($sharedLinks->count() > 0)
                            <div class="table-responsive">
                                <table class="table">
                                    <thead class="text-primary">
                                        <tr>
                                            @if(Auth::user()->hasRole('admin'))
                                                <th>Shared By</th>
                                            @endif
                                            <th>Document</th>
                                            <th>Link</th>
                                            <th>Protected</th>
                                            <th>Permission</th>
                                            <th>Expires At</th>
                                            <th>Active</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($sharedLinks as $link)
                                            <tr>
                                                @if(Auth::user()->hasRole('admin'))
                                                    <td>{{ $link->user->name }} ({{ $link->user->email }})</td>
                                                @endif
                                                <td>{{ $link->document->title }}</td>
                                                <td><a href="{{ route('shared-links.public-view', $link->token) }}" target="_blank" id="link-{{ $link->id }}">{{ route('shared-links.public-view', $link->token) }}</a></td>
                                                <td>
                                                    @if($link->password)
                                                        <i class="material-icons text-warning">lock</i>
                                                    @else
                                                        <i class="material-icons text-success">lock_open</i>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($link->permission_level === 'view')
                                                        <span class="badge badge-info">View Only</span>
                                                    @elseif($link->permission_level === 'download')
                                                        <span class="badge badge-warning">Download Only</span>
                                                    @else
                                                        <span class="badge badge-success">View & Download</span>
                                                    @endif
                                                </td>
                                                <td>{{ $link->expires_at ? $link->expires_at->format('d-m-Y H:i') : 'Never' }}</td>
                                                <td>{{ $link->is_active ? 'Yes' : 'No' }}</td>
                                                <td class="td-actions">
                                                    <button type="button" class="btn btn-info btn-sm" onclick="copyToClipboard('{{ route('shared-links.public-view', $link->token) }}')">
                                                        <i class="material-icons">content_copy</i>
                                                    </button>
                                                    <a href="{{ route('shared-links.edit', $link) }}" class="btn btn-sm btn-primary">
                                                        <i class="material-icons">edit</i>
                                                    </a>
                                                    <form action="{{ route('shared-links.destroy', $link) }}" method="POST" style="display: inline-block;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this shared link?')">
                                                            <i class="material-icons">close</i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center">
                                {{ $sharedLinks->links() }}
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="material-icons" style="font-size: 48px; color: #ccc;">share</i>
                                <h5 class="text-muted mt-3">No Shared Links Yet</h5>
                                <p class="text-muted">Create your first shared link by going to any document and clicking the share button.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            // Using a more modern notification if available, e.g., a toast library
            // For now, we'll use a simple alert.
            alert('Link copied to clipboard!');
        }, function(err) {
            alert('Error: Could not copy link.');
            console.error('Could not copy text: ', err);
        });
    }
</script>
@endpush
