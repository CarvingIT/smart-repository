@extends('layouts.app',['class'=> 'off-canvas-sidebar'])

@section('content')

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
	$("#show-parents").show();
   }
   else{
	$("#taxonomy-tree-selection").hide();
	$("#show-parents").hide();
   }

    if(val === "Select" || val === "SelectCombo" || val === "MultiSelect" || val === 'Numeric' || val === 'TaxonomyTree') {
        $("#is_filter_div").show();
   }
   else{
        $("#is_filter_div").hide();
   }

    if(val === 'Numeric') {
        $("#numeric_values_div").show();
   }
   else{
        $("#numeric_values_div").hide();
   }

   if(val === 'Textarea'){
	$("#rich_text_editor").show();
   }
   else{
	$("#rich_text_editor").hide();
   }

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
                <div class="card-header card-header-primary"><h4 class="card-title"><a href="/collections">{{ __('Collections') }}</a> :: <a href="/collection/{{ $collection->id }}">{{ $collection->name }}</a> :: {{ __('Manage Cataloging Fields') }}</h4></div>
                <div class="col-md-12 text-right">
				@if(empty($edit_field->id))
                <a href="#" id="addmetafieldbutton" onclick="showMetaFieldForm();" class="btn btn-sm btn-primary" title="{{ __('Add') }}"><i class="material-icons">add</i></a>
				@endif
                <a href="/collection/{{ $collection->id }}" class="btn btn-sm btn-primary" title="{{ __('Back') }}"><i class="material-icons">arrow_back</i></a>
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
                   <label for="label" class="col-md-12 col-form-label text-md-right">{{ __('Label') }}</label> 
		   </div>
                    <div class="col-md-8">
                    <input type="text" name="label" id="label" class="form-control" placeholder="{{ __('Label of the field you will be creating') }}" value="{{ $edit_field->label }}" required />
                    </div>
                   </div>
                   <div class="form-group row">
                   <div class="col-md-4">
                   <label for="placeholder" class="col-md-12 col-form-label text-md-right">{{ __('Placeholder text') }}</label> 
		   </div>
                    <div class="col-md-8">
                    <input type="text" name="placeholder" id="placeholder" class="form-control" placeholder="{{ __('A short description of what you want to store') }}" value="{{ $edit_field->placeholder }}" required />
                    </div>
                   </div>
			<!-- Field available to -->
		@php /*$columns = Schema::getColumnListing('meta_fields'); */
				//print_r($columns);
		@endphp
		{{-- @if(in_array('available_to',$columns)) --}}
                   <div class="form-group row">
                   <div class="col-md-4">
                   <label for="placeholder" class="col-md-12 col-form-label text-md-right">{{ __('Field available to') }}</label> 
		   </div>
                   <div class="col-md-8">
				@php 
				$roles = \App\Role::get();
				$roles_array = [];
				$roles_array = explode(",",$edit_field->available_to);
				@endphp
                        <select class="selectpicker" id="available_to" name="available_to[]" title="{{ __('Roles') }}" multiple required>
                                <option value="100" @if(!empty($edit_field->available_to) && $edit_field->available_to == 100) selected @endif>{{ __('All') }}</option> 
				@foreach($roles as $role)
                            	<option value="{{ $role->id }}" @if(!empty($edit_field->available_to) && in_array($role->id,$roles_array)) selected @endif>{{ $role->name }}</option> 
				@endforeach
                        </select>
                   </div>
                   </div>
		{{--@endif --}} {{-- if of checking available_to field exists ends--}}		
                   <div class="form-group row">
                   <div class="col-md-4">
                   <label for="type" class="col-md-12 col-form-label text-md-right">{{ __('Field type') }}</label> 
        		   </div>
                    <div class="col-md-8">
                        <select class="selectpicker" id="type" name="type">
                            <option value="Text" @if($edit_field->type == 'Text') selected @endif>{{ __('Text') }}</option> 
                            <option value="Textarea" @if($edit_field->type == 'Textarea') selected @endif>{{ __('Textarea') }}</option> 
                            <option value="Numeric" @if($edit_field->type == 'Numeric') selected @endif>{{ __('Numeric') }}</option> 
                            <option value="Select" @if($edit_field->type == 'Select') selected @endif>{{ __('Select from options') }}</option> 
                            <option value="MultiSelect" @if($edit_field->type == 'MultiSelect') selected @endif>{{ __('Multiple select') }}</option> 
                            <option value="SelectCombo" @if($edit_field->type == 'SelectCombo') selected @endif>{{ __('Select with custom input') }}</option> 
                            <option value="Date" @if($edit_field->type == 'Date') selected @endif>{{ __('Date') }}</option> 
                            <option value="TaxonomyTree" @if($edit_field->type == 'TaxonomyTree') selected @endif>{{ __('Taxonomy Tree') }}</option> 
                        </select>
                    </div>
                   </div>

                   <div class="form-group row" id="options-field">
		   			<div class="col-md-4">
                   <label for="options" class="col-md-12 col-form-label text-md-right">{{ __('Options') }}</label> 
                   </div>
                    <div class="col-md-8">
                    <input type="text" name="options" id="options" class="form-control" placeholder="{{ __('Comma separated list of options') }}" value="{{ $edit_field->options }}" />
                    </div>
                   </div>

                   <div class="form-group row" id="taxonomy-tree-selection">
		   			<div class="col-md-4">
                   <label for="tax-sel" class="col-md-12 col-form-label text-md-right">{{ __('Select Tree') }}</label> 
                   </div>
                    <div class="col-md-8">
					<select class="selectpicker" name="treeoptions">
						@php
							$trees = App\Taxonomy::where('parent_id', null)->get();
							foreach($trees as $t){
								$selected = '';
								if(!empty($edit_field->options) && $edit_field->options == $t->id){
									$selected = " selected";	
								}
								echo '<option value="'.$t->id.'"' .$selected.'>'.$t->label.'</option>';
							}
						@endphp
					</select>
                    </div>
                   </div>
			@if($edit_field->type =='Numeric')
	           <div id="numeric_values_div" style="display:block;">	
			@else
	           <div id="numeric_values_div" style="display:none;">	
		        @endif
			@php
	                  $extra_attributes = empty($edit_field->extra_attributes)? null : json_decode($edit_field->extra_attributes);
                          $numeric_min_value = @$extra_attributes->numeric_min_value;
                          $numeric_max_value = @$extra_attributes->numeric_max_value;
                        @endphp

                   <div class="form-group row">
		   			<div class="col-md-4">
                   <label class="col-md-12 col-form-label text-md-right">{{ __('Minimum Value') }}</label> 
                   </div>
                    <div class="col-md-8">
                    <input type="text" name="numeric_min_value" id="numeric_min_value" class="form-control" placeholder="{{ __('A number') }}" value="{{ $numeric_min_value}}" />
                    </div>
                   </div>
                   <div class="form-group row">
		   			<div class="col-md-4">
                   <label class="col-md-12 col-form-label text-md-right">{{ __('Maximum Value') }}</label> 
                   </div>
                    <div class="col-md-8">
                    <input type="text" name="numeric_max_value" id="numeric_max_value" class="form-control" placeholder="{{ __('A number') }}" value="{{ $numeric_max_value}}" />
                    </div>
                   </div>
		   </div>
                   <div class="form-group row">
		   			<div class="col-md-4">
                   <label for="display_order" class="col-md-12 col-form-label text-md-right">{{ __('Display Order') }}</label> 
                   </div>
                    <div class="col-md-8">
                    <input type="number" name="display_order" id="display_order" class="form-control" placeholder="{{ __('A number') }}" value="{{ $edit_field->display_order}}" required />
                    </div>
                   </div>

                   <div class="form-group row">
		   			<div class="col-md-4">
                   <label for="results_display_order" class="col-md-12 col-form-label text-md-right">{{ __('Display Order in Search Results') }}</label> 
                   </div>
                    <div class="col-md-8">
                    <input type="number" name="results_display_order" id="results_display_order" class="form-control" placeholder="{{ __('A number') }}" value="{{ $edit_field->results_display_order}}" />
                    </div>
                   </div>
						@php
							$extra_attributes = empty($edit_field->extra_attributes)? null : json_decode($edit_field->extra_attributes);
							$width_on_info_page = @$extra_attributes->width_on_info_page;
							$results_classname = @$extra_attributes->results_classname;
							$filter_width_on_collection_page = @$extra_attributes->filter_width_on_collection_page;
						@endphp

                   <div class="form-group row">
		   			<div class="col-md-4">
                   <label for="results_classname" class="col-md-12 col-form-label text-md-right">{{ __('Classname in search result') }}</label> 
                   </div>
                    <div class="col-md-8">
                    <input type="text" name="results_classname" id="results_classname" class="form-control" placeholder="{{ __('classname') }}" value="{{ $results_classname}}" />
                    </div>
                   </div>

                   <div class="form-group row">
		   			<div class="col-md-4">
                   <label class="col-md-12 col-form-label text-md-right">{{ __('Width on info page') }}</label> 
                   </div>
                    <div class="col-md-8">
						<select name="width_on_info_page" class="form-control">
							<option value="12">{{ __('Full') }}</option>
							<option value="1" @if($width_on_info_page == '1') selected @endif>1/12</option>
							<option value="2" @if($width_on_info_page == '2') selected @endif>1/6</option>
							<option value="3" @if($width_on_info_page == '3') selected @endif>1/4</option>
							<option value="4" @if($width_on_info_page == '4') selected @endif>1/3</option>
							<option value="5" @if($width_on_info_page == '5') selected @endif>5/12</option>
							<option value="6" @if($width_on_info_page == '6') selected @endif>1/2</option>
							<option value="7" @if($width_on_info_page == '7') selected @endif>7/12</option>
							<option value="8" @if($width_on_info_page == '8') selected @endif>2/3</option>
							<option value="9" @if($width_on_info_page == '9') selected @endif>3/4</option>
							<option value="10" @if($width_on_info_page == '10') selected @endif>5/6</option>
							<option value="11" @if($width_on_info_page == '11') selected @endif>11/12</option>
						</select>
                    </div>
                   </div>

                   <div class="form-group row">
		   			<div class="col-md-4">
                   <label class="col-md-12 col-form-label text-md-right">{{ __('Filter width on collection page') }}</label> 
                   </div>
                    <div class="col-md-8">
						<select name="filter_width_on_collection_page" class="form-control">
							<option value="12">{{ __('Full') }}</option>
							<option value="1" @if($filter_width_on_collection_page == '1') selected @endif>1/12</option>
							<option value="2" @if($filter_width_on_collection_page == '2') selected @endif>1/6</option>
							<option value="3" @if($filter_width_on_collection_page == '3') selected @endif>1/4</option>
							<option value="4" @if($filter_width_on_collection_page == '4') selected @endif>1/3</option>
							<option value="5" @if($filter_width_on_collection_page == '5') selected @endif>5/12</option>
							<option value="6" @if($filter_width_on_collection_page == '6') selected @endif>1/2</option>
							<option value="7" @if($filter_width_on_collection_page == '7') selected @endif>7/12</option>
							<option value="8" @if($filter_width_on_collection_page == '8') selected @endif>2/3</option>
							<option value="9" @if($filter_width_on_collection_page == '9') selected @endif>3/4</option>
							<option value="10" @if($filter_width_on_collection_page == '10') selected @endif>5/6</option>
							<option value="11" @if($filter_width_on_collection_page == '11') selected @endif>11/12</option>
						</select>
                    </div>
                   </div>

                   <div class="form-group row">
				   <div class="col-md-4">
                   </div>
                    <div class="col-md-8">
                    <input type="checkbox" name="is_required" id="is_required" class="form-control1" value="1" 
					@if($edit_field->is_required == 1) {{ 'checked' }} @endif
					/>
                    <label for="is_required">{{ __('Is required') }}</label> 
                    </div>
                   </div>

                   <div class="form-group row" id="rich_text_editor"  style="display:none;">
				   <div class="col-md-4">
                   </div>
                    <div class="col-md-8">
                    <input type="checkbox" name="with_rich_text_editor" class="with_rich_text_editor form-control1" value="1" 
					@if($edit_field->with_rich_text_editor == 1) {{ 'checked' }} @endif
					/>
                    <label for="with_rich_text_editor">{{ __('With Rich Text Editor') }}</label> 
                    </div>
                   </div>

                   <div class="form-group row">
		   <div class="col-md-4">
                   </div>
                   <div class="col-md-8">
			@php 
                $show_on_details_page = @$extra_attributes->show_on_details_page;
                $show_parents = @$extra_attributes->show_parents; 
            @endphp
                   <input type="checkbox" name="show_on_details_page" id="show_on_details_page" class="form-control1" value="1" 
				@if($show_on_details_page == 1) {{ 'checked' }} @endif
				/>
                    <label for="show_on_details_page">{{ __('Show on details page') }}</label> 
                    </div>
                   </div>

                @if($edit_field->type == 'TaxonomyTree')
                   <div class="form-group row" id="show-parents">
                @else
                   <div class="form-group row" id="show-parents" style="display:none;">
                @endif
				   <div class="col-md-4">
                   </div>
                    <div class="col-md-8">
                    <input type="checkbox" name="show_parents" class="form-control1" value="1" 
					@if($show_parents == 1) {{ 'checked' }} @endif
					/>
                    <label>{{ __('Show parents in the list and details view') }}</label> 
                    </div>
                   </div>

                   <div class="form-group row mb-0"><div class="col-md-12 offset-md-4">
								<button type="submit" class="btn btn-primary">
                                    {{ __('Save') }}
                                </button> 
								<button onclick="document.location.href='/collection/{{$collection->id}}/meta';" class="btn btn-primary">
                                    {{ __('Cancel') }}
                                </button> 
                     </div></div> 
                   </form> 
		<div class="table-responsive">
                    <table id="metafields" class="table">
                        <thead class=" text-primary">
                            <tr>
                            <th>#</th>
                            <th>{{ __('Label') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Options') }}</th>
                            <th class="text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                    @foreach($meta_fields as $f)
                        <tr>
                            <td>{{ $f->display_order }}</td>
                            <td>{{ $f->label }}</td>
                            <td>{{ $f->type }}</td>
                            <td>
                                @if ($f->type == 'TaxonomyTree')
                                    @php
                                        $t_label = \App\Taxonomy::find($f->options);
                                        echo __('Select from: ').@$t_label->label;
                                    @endphp
                                @else
                                   @if (empty($f->options))
                                    {{ __('N/A') }}
                                   @else  
                                    {{ $f->options }}
                                   @endif
                                @endif
                            </td>
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
