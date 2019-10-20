@extends('layouts.app')

@section('content')
<script>
$( document ).ready(function() {
    $("#options-field").hide();

  $("#type").change(function() {
    var val = $(this).val();
    if(val === "Select") {
        $("#options-field").show();
    }
    else {
        $("#options-field").hide();
    }
  });


  $('#metafields').DataTable({
    "aoColumnDefs": [
            { "bSortable": false, "aTargets": [3]}
     ],
    "searching": false, 
    "paging": false, 
    "info": false
    });

});

</script>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Manage Metadata Fields</div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                   <form method="post" action="/collection/{{$collection->id}}/meta">
                    @csrf()
                    <input type="hidden" name="collection_id" value="{{$collection->id}}" />
                    <input type="hidden" name="meta_field_id" value="{{$edit_field->id}}" />
                   <div class="form-group row">
                   <label for="label" class="col-md-4 col-form-label text-md-right">Label</label> 
                    <div class="col-md-6">
                    <input type="text" name="label" id="label" class="form-control" placeholder="Label of the field you will be creating" value="{{ $edit_field->label }}" />
                    </div>
                   </div>
                   <div class="form-group row">
                   <label for="placeholder" class="col-md-4 col-form-label text-md-right">Placeholder text</label> 
                    <div class="col-md-6">
                    <input type="text" name="placeholder" id="placeholder" class="form-control" placeholder="A short description of what you want to store" value="{{ $edit_field->placeholder }}" />
                    </div>
                   </div>
                   <div class="form-group row">
                   <label for="type" class="col-md-4 col-form-label text-md-right">Field type</label> 
                    <div class="col-md-6">
                        <select class="form-control" id="type" name="type">
                            <option value="Text" @if($edit_field->type == 'Text') selected @endif>Text</option> 
                            <option value="Numeric" @if($edit_field->type == 'Numeric') selected @endif>Numeric</option> 
                            <option value="Select" @if($edit_field->type == 'Select') selected @endif>Select from options</option> 
                            <option value="Date" @if($edit_field->type == 'Date') selected @endif>Date</option> 
                        </select>
                    </div>
                   </div>
                   <div class="form-group row" id="options-field">
                   <label for="options" class="col-md-4 col-form-label text-md-right">Options</label> 
                    <div class="col-md-6">
                    <input type="text" name="options" id="options" class="form-control" placeholder="Comma separated list of options" value="{{ $edit_field->options }}" />
                    </div>
                   </div>
                   <div class="form-group row" id="display-order">
                   <label for="display_order" class="col-md-4 col-form-label text-md-right">Display Order</label> 
                    <div class="col-md-6">
                    <input type="text" name="display_order" id="display_order" class="form-control" placeholder="A number" value="{{ $edit_field->display_order}}" />
                    </div>
                   </div>
                
                   <div class="form-group row mb-0"><div class="col-md-8 offset-md-4"><button type="submit" class="btn btn-primary">
                                    Save
                                </button> 
                     </div></div> 
                   </form> 
                </div>
            </div>
            <div class="card">
                <div class="card-header">Metadata Fields</div>
                <div class="card-body">
                    <table id="metafields">
                        <thead>
                            <tr>
                            <th>Label</th>
                            <th>Type</th>
                            <th>Options</th>
                            <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                    @foreach($meta_fields as $f)
                        <tr>
                            <td>{{ $f->label }}</td>
                            <td>{{ $f->type }}</td>
                            <td>{{ $f->options }}</td>
                            <td>
                                <a href="/collection/{{ $collection->id }}/meta/{{ $f->id }}"><img src="/i/pencil-edit-button.png" class="icon" /></a>
                                <a href="/meta/{{ $f->id }}/delete"><img src="/i/trash.png" class="icon" /></a>
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
@endsection
