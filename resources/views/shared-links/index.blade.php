@extends('layouts.app', ['class'=> 'off-canvas-sidebar', 'title'=>'Shared Links'])

@section('content')
<div class="container">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-primary">
                    <h4 class="card-title ">{{ __('Shared Links') }}</h4>
                </div>
                <div class="card-body">
            @if (session('success'))
              <div class="alert alert-success" role="alert">
                {{ session('success') }}
              </div>
            @endif
            <div class="table-responsive">
              <table class="table table-striped">
                <thead class="text-primary">
                  @if(Auth::user()->hasRole('admin'))
                    <th style="width: 15%;">User</th>
                    <th style="width: 20%;">Document</th>
                    <th style="width: 20%;">Link</th>
                    <th style="width: 12%;">Expires At</th>
                    <th style="width: 10%;">Status</th>
                    <th class="text-right" style="width: 23%;">Actions</th>
                  @else
                    <th style="width: 30%;">Document</th>
                    <th style="width: 20%;">Link</th>
                    <th style="width: 15%;">Expires At</th>
                    <th style="width: 15%;">Permissions</th>
                    <th class="text-right" style="width: 20%;">Actions</th>
                  @endif
                </thead>
                <tbody>
                  @foreach ($sharedLinks as $link)
                    <tr>
                      @if(Auth::user()->hasRole('admin'))
                        <td>
                          <strong>{{ $link->user->name }}</strong><br>
                          <small class="text-muted">{{ $link->user->email }}</small>
                        </td>
                      @endif
                      <td>{{ $link->document->title }}</td>
                      <td>
                        <input type="text" class="form-control form-control-sm" value="{{ route('shared-links.public-view', $link->token) }}" id="link-{{ $link->id }}" readonly style="font-size: 12px;">
                      </td>
                      <td>{{ $link->expires_at ? $link->expires_at->format('d/m/Y H:i') : 'Never' }}</td>
                      <td>
                        @if($link->is_active)
                          <span class="badge badge-success">Active</span>
                        @else
                          <span class="badge badge-secondary">Inactive</span>
                        @endif
                        @if($link->password)
                          <span class="badge badge-warning">Protected</span>
                        @endif
                      </td>
                      <td class="td-actions text-right">
                        <button type="button" class="btn btn-info btn-link" onclick="copyLink('link-{{ $link->id }}')" title="Copy Link">
                          <i class="material-icons">content_copy</i>
                        </button>
                        @if(Auth::user()->hasRole('admin') || $link->user_id == Auth::id())
                          <a class="btn btn-success btn-link" href="{{ route('shared-links.edit', $link) }}" title="Edit">
                            <i class="material-icons">edit</i>
                          </a>
                          <form action="{{ route('shared-links.destroy', $link) }}" method="POST" style="display: inline-block;">
                              @csrf
                              @method('DELETE')
                              <button type="submit" class="btn btn-danger btn-link" title="Delete" onclick="return confirm('Are you sure you want to delete this link?');">
                                  <i class="material-icons">close</i>
                              </button>
                          </form>
                        @endif
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            
            <div class="row">
              <div class="col-12">
                {{ $sharedLinks->links() }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function copyLink(elementId) {
    var copyText = document.getElementById(elementId);
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    document.execCommand("copy");
    
    // Show feedback
    alert("Link copied to clipboard!");
}
</script>
@endsection
