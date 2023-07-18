@extends('layouts.app', ['activePage' => 'synonym-management', 'titlePage' => __('Synonyms Management')])

@section('content')
<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>


<script type="text/javascript">
$(document).ready(function() {
    $("#synonyms-list").DataTables();
</script>

  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
            <div class="card">
              <div class="card-header card-header-primary">
                <h4 class="card-title ">{{ __('Synonyms') }}</h4>
                <!--p class="card-category"> {{ __('Here you can manage synonyms') }}</p-->
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
                    <a href="{{ route('synonyms.create') }}" class="btn btn-sm btn-primary" title="Add User"><i class="material-icons">add</i></a>
                  </div>
                </div>
                <div class="table-responsive">
                  <table id="users-list" class="table">
                    <thead class=" text-primary">
                      <th>
                          {{ __('Name') }}
                      </th>
                      <th>
                        {{ __('Creation date') }}
                      </th>
                      <th class="text-right">
                        {{ __('Actions') }}
                      </th>
                    </thead>
                    <tbody>
                      @foreach($synonyms as $synonym)
                        <tr>
                          <td>
                            {{ $synonym->synonym }}
                          </td>
                          <td>
                            {{ $synonym->created_at }}
                          </td>
                          <td class="td-actions text-right">
                            @if ($synonym->id != auth()->id())
                              <form action="{{ route('synonym.destroy', $synonym) }}" method="post">
                                  @csrf
                                  @method('delete')
                    <a rel="tooltip" class="btn btn-success btn-link" href="{{ route('synonyms.edit', $synonym) }}" data-original-title="" title="">
                                    <i class="material-icons">edit</i>
                                    <div class="ripple-container"></div>
                                  </a>
                                  <button type="button" class="btn btn-danger btn-link" data-original-title="" title="" onclick="confirm('{{ __("Are you sure you want to delete this synonym?") }}') ? this.parentElement.submit() : ''">
                                      <i class="material-icons">close</i>
                                      <div class="ripple-container"></div>
                                  </button>
                              </form>
                            @else
                              <a rel="tooltip" class="btn btn-success btn-link" href="{{ route('synonyms.edit') }}" data-original-title="" title="">
                                <i class="material-icons">edit</i>
                                <div class="ripple-container"></div>
                              </a>
                            @endif
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
