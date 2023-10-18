@extends('layouts.app',['class'=> 'off-canvas-sidebar'])
@push('js')
<link href="/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
<script src="/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
<script src="/js/tinymce/tinymce.min.js" referrerpolicy="origin"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $('.selectsequence').select2();
});

tinymce.init({
                                //selector: '#document_description',
                                selector: 'textarea',
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
                              file_picker_callback (callback, value, meta) {
        let x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth
        let y = window.innerHeight|| document.documentElement.clientHeight|| document.getElementsByTagName('body')[0].clientHeight
	
	tinymce.activeEditor.windowManager.openUrl({
          url : '/file-manager/tinymce5',
          title : 'File manager',
          width : x * 0.8,
          height : y * 0.8,
          onMessage: (api, message) => {
			//alert(message.content);
            //callback(message.content, { text: message.text })
            callback('/media/i/'+message.text, { text: message.text })
          }
        })
      },
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

<form name="document_upload_form" action="/collection/{{ $collection->id }}/upload" method="post" enctype="multipart/form-data">
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
                    <input class="form-control" type="text" id="title" name="title" size="40" value="@if(!empty($document->id)){{ html_entity_decode($document->title) }}@endif" 
                    placeholder="If left blank, we shall guess!" />
                    </div>
		</div>
		<div class="form-group row">
		   <div class="col-md-3">
		   <label for="uploadfile" class="col-md-12 col-form-label text-md-right">Document</label>
		   </div>
    		   <div class="col-md-9">
		   <label for='filesize'><font color="red">File size must be less than {{ $size_limit }}B.</font></label>
    		   <input id="uploadfile" type="file" class="form-control-file" name="document" @if(empty($document->id)) {{ ' required' }}@endif> 
    		   </div>
		</div>
		@if(!empty($document->id))
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
	$user_permissions = \App\UserPermission::select('permission_id')->where('user_id', Auth::user()->id)->get();
	foreach($user_permissions as $permission){
		$user_per[] = $permission->permission_id;
	}
	@endphp		
    @foreach($collection->meta_fields as $f)
	@php 
		$permission_intersection = [];
		if(!empty($f->available_to)){ 
			$available_to = explode(",",$f->available_to);
			$permission_intersection = array_intersect($user_per,$available_to);
		} 
		@endphp
	@if(in_array('1',$user_per) || (in_array('2',$permission_intersection) && in_array('4',$permission_intersection)) || (!empty($f->available_to) && $f->available_to == '100'))
    <div class="form-group row">
		   <div class="col-md-3">
    			<label for="meta_field_{{$f->id}}" class="col-md-12 col-form-label text-md-right">{{$f->label}}</label>
    		   </div>
        <div class="col-md-9">
        @if($f->type == 'Text')
        <input class="form-control" id="meta_field_{{$f->id}}" type="text" name="meta_field_{{$f->id}}" value="{{ html_entity_decode($document->meta_value($f->id)) }}" placeholder="{{ $f->placeholder }}" @if($f->is_required == 1) {{ ' required' }} @endif />
        @elseif ($f->type == 'Textarea')
        <textarea id="document_description" class="form-control" rows="5" id="meta_field_{{$f->id}}" name="meta_field_{{$f->id}}" placeholder="{{ $f->placeholder }}" @if($f->is_required == 1) {{ ' required' }} @endif >{!! $document->meta_value($f->id) !!}</textarea>
        @elseif ($f->type == 'Numeric')
        <input class="form-control" id="meta_field_{{$f->id}}" type="number" step="0.01" min="-9999999999.99" max="9999999999.99" name="meta_field_{{$f->id}}" value="{{ html_entity_decode($document->meta_value($f->id)) }}" placeholder="{{ $f->placeholder }}" @if($f->is_required == 1) {{ ' required' }} @endif />
        @elseif ($f->type == 'Date')
        <input id="meta_field_{{$f->id}}" max="2999-12-31"  type="date" name="meta_field_{{$f->id}}" value="{{ html_entity_decode($document->meta_value($f->id)) }}" placeholder="{{ $f->placeholder }}" @if($f->is_required == 1) {{ ' required' }} @endif />

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
                @endphp
				@if($f->type == 'MultiSelect' || $f->type == 'Select')
            	<option value="{{$o}}" @if(@in_array($o, json_decode($document->meta_value($f->id)))) selected="selected" @endif >{{$o}}</option>
				@else
            	<option value="{{$o}}" @if($o == $document->meta_value($f->id)) selected="selected" @endif >{{$o}}</option>
				@endif
            @endforeach
        </select>
		@elseif ($f->type == 'SelectCombo')
		<input type="text" class="form-control" id="meta_field_{{$f->id}}" name="meta_field_{{$f->id}}" value="{{ $document->meta_value($f->id) }}" autocomplete="off" list="optionvalues" placeholder="{{ $f->placeholder }}" @if($f->is_required == 1) {{ ' required' }} @endif />
		<label>You can select an option or type custom text above.</label>
		<datalist id="optionvalues">
            @php
                $options = explode(",", $f->options);
				sort($options);
            @endphp
            @foreach($options as $o)
                @php
                    $o = ltrim(rtrim($o));
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
				function getTree($children, $document, $f, $parent_id = null, $parents = null){
					if(empty($children['parent_'.$parent_id])) return;
					foreach($children['parent_'.$parent_id] as $t){
							$selected = '';
							if(@in_array($t->id, json_decode($document->meta_value($f->id, true)))){
								$selected='selected="selected"';
							}
							if(!empty($children['parent_'.$t->id]) && count($children['parent_'.$t->id]) > 0){ 
								echo '<option value="'.$t->id.'" '.$selected.'>'.$parents.$t->label.'</option>';
								$parents_tmp = $parents. $t->label .' - ';
								getTree($children, $document, $f, $t->id, $parents_tmp);
							}
							else{
								echo '<option value="'.$t->id.'" '.$selected.'>'.$parents.$t->label.'</option>';
							}
					}
				}
				}
			@endphp
        <select class="form-control selectsequence" id="meta_field_{{$f->id}}" name="meta_field_{{$f->id}}[]" multiple 
		@if($f->is_required == 1) {{ ' required' }} @endif >
			@php
			getTree($children, $document, $f, $f->options);
			@endphp
		</select>
		<script>
			$('#meta_field_{{$f->id}}').val({{ $document->meta_value($f->id, true) }});
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
