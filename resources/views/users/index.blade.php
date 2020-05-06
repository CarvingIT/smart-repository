@extends('layouts.app', ['activePage' => 'user-management', 'titlePage' => __('User Management')])

@section('content')
<script src="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css" /></script>
<script src="https://cdn.datatables.net/scroller/2.0.1/css/scroller.dataTables.min.css" /></script>

<script type="text/javascript">
$(document).ready(function() {
    $('#users').DataTable({
    "processing": true,
    "serverSide": true,
    "ajax": "{{ route('user.index') }}",
    "columns":[
       {data:"name"},
       {data:"email"},
       {data:"created_at",
            render:{
               '_':'display',
               'sort': 'created_date'
            }
        }
    ]
    });

});
</script>

  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
            <div class="card">
              <div class="card-header card-header-primary">
                <h4 class="card-title ">{{ __('Users') }}</h4>
                <!--p class="card-category"> {{ __('Here you can manage users') }}</p-->
<!--div class="card-header-corner" style="margin-top:-5%;"><a href="/user/create"><img class="icon" src="/i/plus.png"/></a></div-->
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
                    <thead class=" text-primary">
                      <th>
                          {{ __('Name') }}
                      </th>
                      <th>
                        {{ __('Email') }}
                      </th>
                      <th>
                        {{ __('Creation date') }}
                      </th>
                      <th class="text-right">
                        {{ __('Actions') }}
                      </th>
                    </thead>
                    <tbody>
<?php print_r($users);?>
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
