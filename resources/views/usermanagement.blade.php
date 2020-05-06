@extends('layouts.app',['class'=> 'off-canvas-sidebar'])

@section('content')
<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
    $('#users').DataTable();
    "aoColumnDefs": [
           { "bSortable": false, "aTargets": [0, 3]},
           { "className": 'text-right', "aTargets": [0,1]},
           { "className": 'td-actions text-right', "aTargets": [3]}
     ],
    "order": [[ 3, "desc" ]],
    "processing":true,
    "serverSide":false,
    "ajax":'/admin/usermanagement',
    "columns":[
       {data:"name"},
       {data:"email"},
       {data:"created_at",
            render:{
               '_':'display',
              'sort': 'created_date'
            }
        },
        {data:"actions"},
    ]
    });
} );

</script>
<div class="container">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
	<!--
            <div class="card">
                <div class="card-header card-header-primary">Add User</div>
                <div class="card-body">
                    <div class="flash-message">
                    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                        @if(Session::has('alert-' . $msg))
			<div class="alert alert-success">
                    	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      	<i class="material-icons">close</i>
                    	</button>
                        <span>{{ Session::get('alert-' . $msg) }}</span>
                  	</div>

                        @endif
                    @endforeach
                    </div>

                   <form method="post" action="/admin/saveuser">
                    @csrf()
                   <label for="name">Name</label> 
                   <input type="text" name="name" id="name" value="" />
                   <label for="email">Email</label> 
                   <input type="text" name="email" id="email" value="" />

                   <input type="submit" value="Submit" />
                   </form> 
                </div>
            </div>
	-->
            <div class="card">
                <div class="card-header card-header-primary">
		<h4 class="card-title ">{{ __('Users') }}</h4>
		</div>

                <div class="card-body">
                @if (session('status'))
                  <div class="row">
                    <div class="col-sm-12">
                      <div class="alert alert-success">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <i class="material-icons">close</i>
                        </button>
                        <span>{{ session('status') }}</span>
                      </div>
                    </div>
                  </div>
                @endif
                <div class="row">
                  <div class="col-12 text-right">
                    <a href="{{ route('user.create') }}" class="btn btn-sm btn-primary" title="Add User"><i class="material-icons">add</i></a>
                  </div>
                </div>

		<div class="table-responsive">
                    <table id="users" class="table">
                        <thead class="text-primary">
                            <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($users as $u)
                        <tr>
                            <td>{{ $u->name }}</td>
                            <td>{{ $u->email }}</td>
                            <td>
                            <a href="/admin/user/{{ $u->id }}/edit"><img class="icon" src="/i/pencil-edit-button.png" /></a>
                            <a href="/admin/user/{{ $u->id }}/delete"><img class="icon" src="/i/trash.png" /></a>
                            </td>    
                        </tr>
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
