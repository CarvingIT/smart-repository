@extends('layouts.app',['class'=> 'off-canvas-sidebar', 'title'=>__('Manage Synonyms')])

@section('content')
<script src="/js/jquery.dataTables.min.js"></script>
<script src="/js/jquery-ui.js" defer></script>
<link href="/css/jquery-ui.css" rel="stylesheet">

<script type="text/javascript">
$(document).ready(function() {
    $("#synonyms").DataTable({
        "language": {
            "lengthMenu": "{{ __('Show _MENU_ entries') }}",
            "zeroRecords": "{{ __('No matching records found') }}",
            "info": "{{ __('Showing _START_ to _END_ of _TOTAL_ entries') }}",
            "infoEmpty": "{{ __('Showing 0 to 0 of 0 entries') }}",
            "infoFiltered": "{{ __('(filtered from _MAX_ total entries)') }}",
            "search": "{{ __('Search:') }}",
            "paginate": {
                "first": "{{ __('First') }}",
                "last": "{{ __('Last') }}",
                "next": "{{ __('Next') }}",
                "previous": "{{ __('Previous') }}"
            }
        }
    });
} );

function showDeleteDialog(synonyms_id){
        str = randomString(6);
        $('#text_captcha').text(str);
        $('#hidden_captcha').text(str);
        $('#delete_synonyms_id').val(synonyms_id);
        deldialog = $( "#deletedialog" ).dialog({
                title: '{{ __("Are you sure ?") }}',
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
    <div class="row justify-content-center">
        <div class="col-md-12">
	<!--
            <div class="card">
                <div class="card-header card-header-primary">Add synonyms</div>
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
                </div>
            </div>
	-->
            <div class="card">
                <div class="card-header card-header-primary">
		<h4 class="card-title ">{{ __('Synonyms') }}</h4>
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
                    <a href="{{ route('synonyms.create') }}" class="btn btn-sm btn-primary" title="{{ __('Add Synonyms') }}"><i class="material-icons">add</i></a>
                  </div>
                </div>

		<div class="table-responsive">
                    <table id="synonyms" class="table">
                        <thead class="text-primary">
                            <tr>
                            <th>{{ __('Synonyms') }}</th>
                            <th class="text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($synonyms as $u)
                        <tr>
                            <td>{{ $u->synonyms}}</td>
                            <td class="td-actions text-right">
                            <!--a href="/admin/synonyms/{{ $u->id }}/edit" rel="tooltip" class="btn btn-success btn-link"-->
                            <a href="/synonyms/{{ $u->id }}/edit" rel="tooltip" class="btn btn-success btn-link">
				    <i class="material-icons">edit</i>
                                    <div class="ripple-container"></div>
				</a>
				<span class="btn btn-danger btn-link confirmdelete" onclick="showDeleteDialog({{ $u->id }});" title="{{ __('Delete Synonym') }}"><i class="material-icons">delete</i></span>
                            </td>
                        </tr>
	    <div id="deletedialog" style="display:none;">
                <form name="deletedoc" method="post" action="/admin/synonyms/delete">
                @csrf
                <p>{{ __('Enter') }} <span id="text_captcha"></span> {{ __('to delete') }}</p>
                <input type="text" name="delete_captcha" value="" />
                <input type="hidden" id="hidden_captcha" name="hidden_captcha" value="" />
                <input type="hidden" id="delete_synonyms_id" name="synonyms_id" value="{{ $u->id }}" />
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
