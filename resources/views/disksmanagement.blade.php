@extends('layouts.app',['class'=> 'off-canvas-sidebar','title'=>'Manage Storages'])

@section('content')
<script src="/js/jquery.dataTables.min.js"></script>
<script src="/js/jquery-ui.min.js" defer></script>
<link href="/css/jquery-ui.css" rel="stylesheet">

<script>
$(document).ready(function() {
    $('#disks').DataTable({
        "language": {
            "search": "{{ __('Search:') }}",
            "lengthMenu": "{{ __('Show _MENU_ entries') }}",
            "info": "{{ __('Showing _START_ to _END_ of _TOTAL_ entries') }}",
            "infoEmpty": "{{ __('Showing 0 to 0 of 0 entries') }}",
            "infoFiltered": "{{ __('(filtered from _MAX_ total entries)') }}",
            "paginate": {
                "first": "{{ __('First') }}",
                "last": "{{ __('Last') }}",
                "next": "{{ __('Next') }}",
                "previous": "{{ __('Previous') }}"
            },
            "zeroRecords": "{{ __('No matching records found') }}"
        }
    });
} );

function showDeleteDialog(disk_id){
        str = randomString(6);
        $('#text_captcha').text(str);
        $('#hidden_captcha').text(str);
        $('#delete_disk_id').val(disk_id);
        deldialog = $( "#deletedialog" ).dialog({
                title: 'Are you sure you want to delete this storage ?',
                resizable: true
        });
}

function randomString(length) {
   var result           = '';
   var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
   var charactersLength = characters.length;
   for ( var i = 0; i < length; i++ ) {
      result += characters.charAt(Math.floor(Math.random() * charactersLength));
   }
   return result;
}
</script>
<div class="container">
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
            <div class="card-header card-header-primary">
                <h4 class="card-title">{{ __('Disks') }}</h4>
              </div>		

                <div class="card-body">
                    <div class="flash-message">
                    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                        @if(Session::has('alert-' . $msg))
			<div class="alert alert-<?php echo $msg; ?>">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="material-icons">close</i>
                        </button>
                        <span>{{ Session::get('alert-' . $msg) }}</span>
                        </div>
                        @endif
                    @endforeach
                    </div>
		<div class="row">
                  <div class="col-12 text-right">
                    <a href="/admin/disk-form/new" class="btn btn-sm btn-primary" title="{{ __('Add Disk') }}"><i class="material-icons">add</i></a>
                  </div>
                </div>

			<div class="table-responsive">
                    <table id="disks" class="table">
                        <thead class="text-primary">
                            <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Created') }}</th>
                            <th class="text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($disks as $d)
                        <tr>
                            <td>{{ $d->name }}</td>
                            <td>{{ $d->driver }}</td>
                            <td>{{ $d->created_at }}</td>
                            <td class="td-actions text-right">
                                <a rel="tooltip" class="btn btn-success btn-link" href="/admin/disk-form/{{$d->id}}">
				<i class="material-icons">edit</i>
                                <div class="ripple-container"></div>
				</a>
			<span class="btn btn-danger btn-link confirmdelete" onclick="showDeleteDialog({{ $d->id }});" title="{{ __('Delete Storage') }}"><i class="material-icons">delete</i></span>
                            </td>    <!-- use font awesome icons or image icons -->
                        </tr>
            <div id="deletedialog" style="display:none;">
                <form name="deletedisk" method="post" action="/admin/disk/delete">
                @csrf
                <p>{{ __('Enter') }} <span id="text_captcha"></span> {{ __('to delete') }}</p>
                <input type="text" name="delete_captcha" value="" />
                <input type="hidden" id="hidden_captcha" name="hidden_captcha" value="" />
                <input type="hidden" id="delete_disk_id" name="disk_id" value="{{ $d->id }}" />
                <button class="btn btn-danger" type="submit" value="delete">{{ __('Delete') }}</button>
                </form>
            </div>

                        @endforeach
                        </tbody>
                    </table>
			</div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
