@extends('layouts.app', ['activePage' => 'shared-links', 'titlePage' => __('Shared Links')])

@section('content')
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header card-header-primary">
            <h4 class="card-title ">Shared Links</h4>
            <p class="card-category"> Here you can manage your shared links</p>
          </div>
          <div class="card-body">
            @if (session('success'))
              <div class="alert alert-success" role="alert">
                {{ session('success') }}
              </div>
            @endif
            <div class="table-responsive">
              <table class="table">
                <thead class=" text-primary">
                  <th>
                    Document
                  </th>
                  <th>
                    Link
                  </th>
                  <th>
                    Expires At
                  </th>
                  <th>
                    Permissions
                  </th>
                  <th class="text-right">
                    Actions
                  </th>
                </thead>
                <tbody>
                  @foreach ($sharedLinks as $link)
                    <tr>
                      <td>
                        {{ $link->document->title }}
                      </td>
                      <td>
                        <a href="{{ route('shared-links.public-view', $link->token) }}" target="_blank">{{ route('shared-links.public-view', $link->token) }}</a>
                      </td>
                      <td>
                        {{ $link->expires_at ? $link->expires_at->format('d/m/Y H:i') : 'Never' }}
                      </td>
                      <td>
                        {{ $link->permission_level }}
                      </td>
                      <td class="td-actions text-right">
                        <a rel="tooltip" class="btn btn-success btn-link" href="{{ route('shared-links.edit', $link) }}" data-original-title="" title="Edit">
                          <i class="material-icons">edit</i>
                          <div class="ripple-container"></div>
                        </a>
                        <form action="{{ route('shared-links.destroy', $link) }}" method="POST" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" rel="tooltip" class="btn btn-danger btn-link" data-original-title="" title="Delete" onclick="return confirm('Are you sure you want to delete this link?');">
                                <i class="material-icons">close</i>
                                <div class="ripple-container"></div>
                            </button>
                        </form>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            {{ $sharedLinks->links() }}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
