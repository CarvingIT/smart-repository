@extends('layouts.app',['class'=> 'off-canvas-sidebar'])
@push('js')
<link href="/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
<script src="/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
<script src="/js/tinymce/tinymce.min.js" referrerpolicy="origin"></script>
<link href="/css/select2.min.css" rel="stylesheet" />
<link href="/css/select2totree.css" rel="stylesheet" />
<script src="/js/select2.min.js"></script>
<script src="/js/select2totree.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $('.selectsequence').select2();
    $('.selectsequencetree').select2ToTree({placeholder:'Select one or more'});
    
    @if(empty($document->id))
    // validation for either document or external link
    $(document).on('submit','form#upload-form', function(){
        if(!$('#uploadfile').val() && !$('#externallink').val()){
            alert('You have to select a file or input an external link.');
            return false;
        }
    });
    @endif
});

tinymce.init({
				relative_urls: false,
                                selector: '.rich_text_editor',
                                //selector: 'textarea',
                                plugins: [
                                    "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                                    "searchreplace wordcount visualblocks visualchars code fullscreen",
                                    "insertdatetime media table nonbreaking save contextmenu directionality paste"
                                ],
                                toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | code",
                                relative_urls: false,
                                //remove_script_host: false,
                                //convert_urls: true,
                                force_br_newlines: true,
                                force_p_newlines: false,
                                forced_root_block: '', // Needed for 3.x
		            file_picker_callback : function(callback, value, meta) {
      var x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
      var y = window.innerHeight|| document.documentElement.clientHeight|| document.getElementsByTagName('body')[0].clientHeight;
      
      var cmsURL = '/laravel-filemanager?editor=' + meta.fieldname;
      if (meta.filetype == 'image') {
        cmsURL = cmsURL + "&type=Images";
      } else {
        cmsURL = cmsURL + "&type=Files";
      }

      tinyMCE.activeEditor.windowManager.openUrl({
        url : cmsURL,
        title : 'Filemanager',
        width : x * 0.8,
        height : y * 0.8,
        resizable : "yes",
        close_previous : "no",
        onMessage: (api, message) => {
          callback(message.content);
        }
      });
    }
	
   });


</script>

@endpush
@section('content')
<div class="container">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header card-header-primary"><h4 class="card-title"><a href="/collections">{{ __('Collections') }}</a> :: <a href="/collection/{{ $collection->id }}">{{ $collection->name }}</a> :: Upload Document</h4></div>
                <div class="col-md-12 text-right">
                <a href="javascript:window.history.back();" class="btn btn-sm btn-primary" title="Back"><i class="material-icons">arrow_back</i></a>
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

<form name="document_upload_form" id="upload-form" action="/collection/{{ $collection->id }}/upload" method="post" enctype="multipart/form-data" multiple >
@csrf()
<input type="hidden" name="collection_id" value="{{ $collection->id }}" />
@if(!empty($document->id))
<input type="hidden" name="document_id" value="{{ $document->id }}" />
@endif
		<div class="form-group row">
	   	   <div class="col-md-3">
		   <label for="title" class="col-md-12 col-form-label text-md-right">Title</label>
		   </div>
                    <div class="col-md-9">
                    <input class="form-control" type="text" id="title" name="title" size="40" value="{{ old('title',$document->title) }}" 
                    placeholder="If left blank, we shall guess!" maxlength="150" />
                    </div>
		</div>
		@if(empty($document->id) || $document->type != 'N/A')
		<div class="form-group row">
		   <div class="col-md-3">
		   <label for="uploadfile" class="col-md-12 col-form-label text-md-right">Document</label>
		   </div>
    		   <div class="col-md-9">
			   <label for='filesize'><font color="red">File size must be less than {{ $size_limit }}B.</font></label>
    		   <input id="uploadfile" type="file" class="form-control-file" name="document[]" multiple/> 
    		   </div>
		</div>
		@endif
		@if(empty($document->id) || $document->type == 'N/A')
		<div class="form-group row">
		   <div class="col-md-3">
		   <label for="externallink" class="col-md-12 col-form-label text-md-right">External Link</label>
		   </div>
    		   <div class="col-md-9">
				@if(empty($document->id))
				Enter a link below only if there's no document to be uploaded. 
				@endif
    		   <input type="text" class="form-control-file" id="externallink" name="external_link" 
					value="{{ old('external_link', $document->external_link) }}" 
                    placeholder="https://..." maxlength="150" > 
    		   </div>
		</div>
		@endif
		@if(!empty($document->id) && $document->type != 'N/A')
		<div class="form-group row">
		   <div class="col-md-3">
		   <label for="uploadfile" class="col-md-12 col-form-label text-md-right">Uploaded Document</label>
		   </div>
    		   <div class="col-md-9">
			@if(!empty($document->id))<a href="/collection/{{ $collection->id }}/document/{{ $document->id }}" target="_blank">{{ html_entity_decode($document->title) }} </a> @endif
    		   </div>
		</div>
		@endif
	<div class="select-data-container" style="position:fixed; top:25%; z-index:1000;"></div>
	@php
	/*
	$user_permissions = \App\UserPermission::select('permission_id')->where('user_id', Auth::user()->id)->get();
	foreach($user_permissions as $permission){
		$user_per[] = $permission->permission_id;
	}
	*/

	$user_rol = [];
	$user_roles = \App\UserRole::where('user_id',auth()->user()->id)->get();
	foreach($user_roles as $u_r){
		$user_rol[] = $u_r->role_id;
	}
	@endphp		
    @foreach($collection->meta_fields as $f)
	@php 
		//$permission_intersection = [];
		$role_intersection = [];
		if(!empty($f->available_to)){ 
			$available_to = explode(",",$f->available_to);
			$role_intersection = array_intersect($user_rol,$available_to);
		} 
		@endphp
	@if(!empty($role_intersection) || (!empty($f->available_to) && $f->available_to == '100'))
    <div class="form-group row">
		   <div class="col-md-3">
    			<label for="meta_field_{{$f->id}}" class="col-md-12 col-form-label text-md-right">{{$f->label}}
                @if ($f->is_required)
                    <span style="color:red;">*</span>
                @endif
                </label>
    		   </div>
        <div class="col-md-9">
        @if($f->type == 'Text')
        <input class="form-control" id="meta_field_{{$f->id}}" type="text" name="meta_field_{{$f->id}}" value="{{ old('meta_field_'.$f->id, $document->meta_value($f->id)) }}" placeholder="{{ $f->placeholder }}" @if($f->is_required == 1) {{ ' required' }} @endif />
        @elseif ($f->type == 'Textarea')
        <textarea id="document_description" class="form-control @if($f->with_rich_text_editor == 1) rich_text_editor @endif" rows="5" id="meta_field_{{$f->id}}" name="meta_field_{{$f->id}}" placeholder="{{ $f->placeholder }}" @if($f->is_required == 1) {{ ' required' }} @endif >{!! old('meta_field_'.$f->id, $document->meta_value($f->id)) !!}</textarea>
        @elseif ($f->type == 'Numeric')
        <input class="form-control" id="meta_field_{{$f->id}}" type="number" step="0.01" min="-9999999999.99" max="9999999999.99" name="meta_field_{{$f->id}}" value="{{ old('meta_field_'.$f->id, $document->meta_value($f->id)) }}" placeholder="{{ $f->placeholder }}" @if($f->is_required == 1) {{ ' required' }} @endif />
        @elseif ($f->type == 'Date')
        <input id="meta_field_{{$f->id}}" max="2999-12-31"  type="date" name="meta_field_{{$f->id}}" value="{{ old('meta_field_'.$f->id, $document->meta_value($f->id)) }}" placeholder="{{ $f->placeholder }}" @if($f->is_required == 1) {{ ' required' }} @endif />

        @elseif (in_array($f->type, array('Select', 'MultiSelect')))
        <select class="form-control selectsequence" id="meta_field_{{$f->id}}" name="meta_field_{{$f->id}}[]" @if($f->type == 'MultiSelect') multiple @endif 
		@if($f->is_required == 1) {{ ' required' }} @endif >
            @php
                $options = explode(",", $f->options);
				sort($options);
            @endphp
            <option value="">{{ $f->placeholder }}</option>
            @foreach($options as $o)
                @php
                    $o = ltrim(rtrim($o));
                    $old_vals = old('meta_field_'.$f->id, json_decode($document->meta_value($f->id)));
                    $old_vals = is_array($old_vals) ? $old_vals : [];
                @endphp
            	<option value="{{$o}}" @if(@in_array($o, $old_vals)) selected="selected" @endif >{{$o}}</option>
            @endforeach
        </select>
		@elseif ($f->type == 'SelectCombo')
		<input type="text" class="form-control" id="meta_field_{{$f->id}}" name="meta_field_{{$f->id}}" value="{{ old('meta_field_'.$f->id, $document->meta_value($f->id)) }}" autocomplete="off" list="optionvalues" placeholder="{{ $f->placeholder }}" @if($f->is_required == 1) {{ ' required' }} @endif />
		<label>You can select an option or type custom text above.</label>
		<datalist id="optionvalues">
            @php
                $options = explode(",", $f->options);
				sort($options);
            @endphp
            @foreach($options as $o)
                @php
                    $o = ltrim(rtrim($o));
                    $old_vals = json_decode($document->meta_value($f->id));
                    $old_vals = is_array($old_vals) ? $old_vals : [];
                @endphp
            <option>{{$o}}</option>
            @endforeach
		</datalist>
		@elseif ($f->type == 'TaxonomyTree')
			@php
				$tags = App\Taxonomy::all();
				$children = [];
				foreach($tags as $t){
					$children['parent_'.$t->parent_id][] = $t;
				}
				if(!function_exists('getTree')){
                
				function getTree($children, $document, $f, $level, $parent_id = null, $parents = null){
                    $level++;
					if(empty($children['parent_'.$parent_id])) return;
					foreach($children['parent_'.$parent_id] as $t){
							$selected = '';
                            $old_vals = old('meta_field_'.$f->id, json_decode($document->meta_value($f->id, true)));
                            $old_vals = is_array($old_vals) ? $old_vals : [];
							if (@in_array($t->id, $old_vals)){
								$selected='selected="selected"';
							}
                            $data_pup = ($level > 1) ? 'data-pup="'.$t->parent_id.'"' : '';
							if(!empty($children['parent_'.$t->id]) && count($children['parent_'.$t->id]) > 0){ 
								echo '<option '.$data_pup.' value="'.$t->id.'" '.$selected.' class="l'.$level.' non-leaf">'.$t->label.'</option>';
								$parents_tmp = $parents. $t->label .' - ';
								getTree($children, $document, $f, $level, $t->id, $parents_tmp);
							}
							else{
								echo '<option '.$data_pup.' class="l'.$level.'" value="'.$t->id.'" '.$selected.'>'.$t->label.'</option>';
							}
					}
				}
				}
			@endphp
        <select class="form-control selectsequencetree" id="meta_field_{{$f->id}}" name="meta_field_{{$f->id}}[]" multiple 
		@if($f->is_required == 1) {{ ' required' }} @endif>
			@php
			getTree($children, $document, $f, 0, $f->options);
			@endphp
		</select>
        <script>
             $('#meta_field_{{$f->id}}').val({{ preg_replace('/"/','',$document->meta_value($f->id, true)) }});
        </script>

        @endif
        </div>
    </div>
	@endif {{-- end of permissions if --}}
    @endforeach
	<div class="form-group row">
	   <div class="col-md-3 text-right">
   		   <input id="same_meta_docs" type="checkbox" name="same_meta_docs_upload" value="1" /> 
	   </div>
   	   <div class="col-md-9">
	   		<label for="same_meta_docs" class="col-md-12 col-form-label">Upload more documents of the same field values above</label>
   	   </div>
	</div>
<div class="form-group row mb-0">
    <div class="col-md-9 offset-md-4">
        <button type="submit" class="btn btn-primary"> Save </button>
    </div>
</div>

</form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
