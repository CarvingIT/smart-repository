@extends('layouts.app', ['class'=> 'off-canvas-sidebar'])

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-primary">
                    <h4 class="card-title">{{ __('Alerts') }}</h4>
                </div>
                <div class="card-body">
                    <div class="flash-message">
                        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                            @if(Session::has('alert-' . $msg))
                                <div class="alert alert-{{ $msg }}">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <i class="material-icons">close</i>
                                    </button>
                                    <span>{{ Session::get('alert-' . $msg) }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    @if($alerts->count() == 0)
                        <p class="mb-0">{{ __('No alerts yet.') }}</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="text-primary">
                                    <tr>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Alert') }}</th>
                                        <th>{{ __('Created') }}</th>
                                        <th class="text-right">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($alerts as $alert)
                                    <tr>
                                        <td>
                                            @if($alert->isRead())
                                                <span class="badge badge-secondary">{{ __('Read') }}</span>
                                            @else
                                                <span class="badge badge-danger">{{ __('Unread') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div><strong>{{ $alert->title }}</strong></div>
                                            @if(!empty($alert->message))
                                                <div class="text-muted">{{ $alert->message }}</div>
                                            @endif
                                        </td>
                                        <td>{{ $alert->created_at ? $alert->created_at->format('Y-m-d H:i') : '-' }}</td>
                                        <td class="td-actions text-right">
                                            @if(!empty($alert->url))
                                                <a href="{{ route('alerts.open', $alert->id) }}" class="btn btn-info btn-link" title="{{ __('Open alert link') }}">
                                                    <i class="material-icons">open_in_new</i>
                                                </a>
                                            @endif

                                            @if($alert->isRead())
                                                <form action="{{ route('alerts.unread', $alert->id) }}" method="post" style="display:inline-block;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-warning btn-link" title="{{ __('Mark as unread') }}">
                                                        <i class="material-icons">markunread</i>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('alerts.read', $alert->id) }}" method="post" style="display:inline-block;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-link" title="{{ __('Mark as read') }}">
                                                        <i class="material-icons">done</i>
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
                                {{ $alerts->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
