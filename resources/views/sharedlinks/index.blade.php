@extends('layouts.app', ['activePage' => 'shared-links', 'titlePage' => __('Shared Links')])

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title">{{ __('My Shared Links') }}</h4>
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
                                            <th>Document</th>
                                            <th>Link</th>
                                            <th>Expires At</th>
                                            <th>Active</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($sharedLinks as $link)
                                            <tr>
                                                <td>{{ $link->document->title }}</td>
                                                <td><a href="{{ route('shared-links.public-view', $link->token) }}" target="_blank">{{ route('shared-links.public-view', $link->token) }}</a></td>
                                                <td>{{ $link->expires_at ? $link->expires_at->format('d-m-Y H:i') : 'Never' }}</td>
                                                <td>{{ $link->is_active ? 'Yes' : 'No' }}</td>
                                                <td>
                                                    <a href="{{ route('shared-links.edit', $link) }}" class="btn btn-sm btn-primary">Edit</a>
                                                    <form action="{{ route('shared-links.destroy', $link) }}" method="POST" style="display: inline-block;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
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
