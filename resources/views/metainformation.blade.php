@extends('layouts.app',['class'=> 'off-canvas-sidebar'])

@section('content')
<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

<script type="text/javascript">
$( document ).ready(function() {
  var options_field_val = $('#type').val();
	if(options_field_val != 'Select' && options_field_val != 'SelectCombo' && options_field_val != 'MultiSelect'){
    	$("#options-field").hide();
	}
	if(options_field_val != 'TaxonomyTree'){
		$("#taxonomy-tree-selection").hide();
	}

  $("#type").change(function() {
    var val = $(this).val();
    if(val === "Select" || val === "SelectCombo" || val === "MultiSelect") {
        $("#options-field").show();
    }
    else {
        $("#options-field").hide();
    }

	if(val === 'TaxonomyTree'){
		$("#taxonomy-tree-selection").show();
	}
	else{
		$("#taxonomy-tree-selection").hide();
	}
  });


  $('#metafields').DataTable({
    "aoColumnDefs": [
            { "bSortable": false, "aTargets": [4]}
     ],
    "searching": false, 
    "paging": false, 
    "info": false
    });

});

function showMetaFieldForm(){
	$('#metafieldform').show();
	$('#addmetafieldbutton').hide();
}
</script>
<div class="container">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header card-header-primary"><h4 class="card-title"><a href="/collections">{{ __('Collections') }}</a> :: <a href="/collection/{{ $collection->id }}">{{ $collection->name }}</a> :: Manage Cataloging Fields</h4></div>
                <div class="col-md-12 text-right">
				@if(empty($edit_field->id))
                <a href="#" id="addmetafieldbutton" onclick="showMetaFieldForm();" class="btn btn-sm btn-primary" title="Add"><i class="material-icons">add</i></a>
				@endif
                <a href="/collection/{{ $collection->id }}" class="btn btn-sm btn-primary" title="Back"><i class="material-icons">arrow_back</i></a>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="material-icons">close</i>
                        </button>
                        <span>{{ session('status') }}</span>
                        </div>
                    @endif

                   <form method="post" action="/collection/{{$collection->id}}/meta" id="metafieldform" 
						@if(empty($edit_field->id))
						style="display:none;" 
						@endif
					>
                    @csrf()
                    <input type="hidden" name="collection_id" value="{{$collection->id}}" />
                    <input type="hidden" name="meta_field_id" value="{{$edit_field->id}}" />
                   <div class="form-group row">
                   <div class="col-md-4">
                   <label for="label" class="col-md-12 col-form-label text-md-right">Label</label> 
		   </div>
                    <div class="col-md-8">
                    <input type="text" name="label" id="label" class="form-control" placeholder="Label of the field you will be creating" value="{{ $edit_field->label }}" required />
                    </div>
                   </div>
                   <div class="form-group row">
                   <div class="col-md-4">
                   <label for="placeholder" class="col-md-12 col-form-label text-md-right">Placeholder text</label> 
		   </div>
                    <div class="col-md-8">
                    <input type="text" name="placeholder" id="placeholder" class="form-control" placeholder="A short description of what you want to store" value="{{ $edit_field->placeholder }}" required />
                    </div>
                   </div>
			<!-- Field available to -->
		@php $columns = Schema::getColumnListing('meta_fields'); 
				//print_r($columns);
		@endphp
		@if(in_array('available_to',$columns)) 
                   <div class="form-group row">
                   <div class="col-md-4">
                   <label for="placeholder" class="col-md-12 col-form-label text-md-right">Field available to</label> 
		   </div>
                   <div class="col-md-8">
				@php 
				if(!empty($edit_field->available_to)){
					$permissions_array = explode(",",$edit_field->available_to);
				} 
				@endphp
                        <select class="selectpicker" id="available_to" name="available_to[]" title="Permissions " multiple>
                                <option value="100">All</option> 
				@foreach($permissions as $permission)
                            	<option value="{{ $permission->id }}" @if(!empty($edit_field->available_to) && in_array($permission->id,$permissions_array)) selected @endif>{{ $permission->name }}</option> 
				@endforeach
                        </select>
                   </div>
                   </div>
		@endif {{-- if of checking available_to field exists ends--}}		
                   <div class="form-group row">
                   <div class="col-md-4">
                   <label for="type" class="col-md-12 col-form-label text-md-right">Field type</label> 
        		   </div>
                    <div class="col-md-8">
                        <select class="selectpicker" id="type" name="type">
                            <option value="Text" @if($edit_field->type == 'Text') selected @endif>Text</option> 
                            <option value="Textarea" @if($edit_field->type == 'Textarea') selected @endif>Textarea</option> 
                            <option value="Numeric" @if($edit_field->type == 'Numeric') selected @endif>Numeric</option> 
                            <option value="Select" @if($edit_field->type == 'Select') selected @endif>Select from options</option> 
                            <option value="MultiSelect" @if($edit_field->type == 'MultiSelect') selected @endif>Multiple select</option> 
                            <option value="SelectCombo" @if($edit_field->type == 'SelectCombo') selected @endif>Select with custom input</option> 
                            <option value="Date" @if($edit_field->type == 'Date') selected @endif>Date</option> 
                            <option value="TaxonomyTree" @if($edit_field->type == 'TaxonomyTree') selected @endif>Taxonomy Tree</option> 
                        </select>
                    </div>
                   </div>

                   <div class="form-group row" id="options-field">
		   			<div class="col-md-4">
                   <label for="options" class="col-md-12 col-form-label text-md-right">Options</label> 
                   </div>
                    <div class="col-md-8">
                    <input type="text" name="options" id="options" class="form-control" placeholder="Comma separated list of options" value="{{ $edit_field->options }}" />
                    </div>
                   </div>

                   <div class="form-group row" id="taxonomy-tree-selection">
		   			<div class="col-md-4">
                   <label for="tax-sel" class="col-md-12 col-form-label text-md-right">Select Tree</label> 
                   </div>
                    <div class="col-md-8">
					<select class="selectpicker" name="treeoptions">
						@php
							$trees = App\Taxonomy::where('parent_id', null)->get();
							foreach($trees as $t){
								echo '<option value="'.$t->id.'">'.$t->label.'</option>';
							}
						@endphp
					</select>
                    </div>
                   </div>
                   <div class="form-group row" id="display-order">
		   <div class="col-md-4">
                   <label for="display_order" class="col-md-12 col-form-label text-md-right">Display Order</label> 
                   </div>
                    <div class="col-md-8">
                    <input type="text" name="display_order" id="display_order" class="form-control" placeholder="A number" value="{{ $edit_field->display_order}}" required />
                    </div>
                   </div>

                   <div class="form-group row" id="display-order">
		   <div class="col-md-4">
                   <label for="is_required" class="col-md-12 col-form-label text-md-right">Is required ?</label> 
                   </div>
                    <div class="col-md-8">
                    <input type="checkbox" name="is_required" id="is_required" class="form-control1" value="1" 
					@if($edit_field->is_required == 1) {{ 'checked' }} @endif
					/>
                    </div>
                   </div>
                
                   <div class="form-group row mb-0"><div class="col-md-12 offset-md-4">
								<button type="submit" class="btn btn-primary">
                                    Save
                                </button> 
								<button onclick="document.location.href='/collection/{{$collection->id}}/meta';" class="btn btn-primary">
                                    Cancel
                                </button> 
                     </div></div> 
                   </form> 
		<div class="table-responsive">
                    <table id="metafields" class="table">
                        <thead class=" text-primary">
                            <tr>
                            <th>#</th>
                            <th>Label</th>
                            <th>Type</th>
                            <th>Options</th>
                            <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                    @foreach($meta_fields as $f)
                        <tr>
                            <td>{{ $f->display_order }}</td>
                            <td>{{ $f->label }}</td>
                            <td>{{ $f->type }}</td>
                            <td>{{ $f->options }}</td>
                            <td class="td-actions text-right">
                                <a href="/collection/{{ $collection->id }}/meta/{{ $f->id }}" class="btn btn-success btn-link">
				<i class="material-icons">edit</i>
                                <div class="ripple-container"></div>
				</a>
                                <a href="/collection/{{ $collection->id }}/meta/{{ $f->id }}/delete" class="btn btn-danger btn-link">
				<i class="material-icons">delete</i>
                                <div class="ripple-container"></div>
				</a>
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
