<!DOCTYPE html>
<head>
<title>{{ env('APP_NAME', 'Smart Repository') }}::Document Viewer</title>
<link rel="icon" type="image/png" href="/material/img/favicon.png">
<link href="/css/jquery.dataTables.min.css" rel="stylesheet" />
<script src="/js/jquery-3.5.1.js"></script>
<script src="/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
<script src="/js/tinymce/tinymce.min.js" referrerpolicy="origin"></script>
<link href="/css/select2.min.css" rel="stylesheet" />
<link href="/css/select2totree.css" rel="stylesheet" />
<script src="/js/select2.min.js"></script>
<script src="/js/select2totree.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $('.selectsequence').select2();
    $('.selectsequencetree').each(function(index,element){
        $(this).select2ToTree({placeholder:'Select one or more'});
    });
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

<style>
.row {
  display:flex;
  height:100%;
  overflow:clip;
}
#meta-view {
flex:40%;
overflow-y:scroll;
height:auto;
}
#doc-view {
  flex:60%;
}
body{
margin:0;
font-family:"Roboto", "Helvetica", "Arial", sans-serif;
font-size:1em;
font-size:0.8em;
background-color:#eee;
}
label{
color:#666;
}
h4{
    display:block;
    position:sticky;
    top:0;
    background-color:#9124a3;
    color:#fff;
    margin:0;
    padding:9px;
    font-family:sans-serif;
    border-top:2px solid #999;
    line-height:14px;
    font-weight:normal;
    font-size:15px;
    color:#eee;
    box-shadow:2px 2px 3px #666;
}
#meta-data{
    padding:1em;
}
.row{
--bs-gutter-x:0em;
}
</style>
</head>
<body>
@php
    $doc = \App\Document::find($document_id);
@endphp
<div class="row">
<div id="doc-view">
@php
if(!is_null($path_count)){
    $path = json_decode($doc->path);
    $file_url = $path[$path_count];
    $file_path  = $path[$path_count];
@endphp
<iframe id="pdfreader" class="pdf" src="/js/ViewerJS/?zoom=page-width&title={{ $doc->title }}#../../collection/{{ $collection_id }}/document/{{ $document_id }}/details/{{ $path_count }}" width="100%" height="100%"></iframe> 
@php
}
else{
@endphp
<iframe id="pdfreader" class="pdf" src="/js/ViewerJS/?zoom=page-width&title={{ $doc->title }}#../../collection/{{ $collection_id }}/document/{{ $document_id }}" width="100%" height="100%"></iframe>
@php
}
@endphp
</div>
<div id="meta-view">
	<h4>Associated Meta Information <span class="text-right" style="float:right"></h4>
    <div id="meta-data">
    <form name="edit-meta-data" method="post" action="/collection/{{ $collection_id }}/upload">
        @csrf()
        <input type="hidden" name="collection_id" value="{{ $collection_id }}" />
        <input type="hidden" name="referer" value="@php if(!empty($_SERVER['HTTP_REFERER'])){ echo $_SERVER['HTTP_REFERER'];} @endphp">

        @if(!empty($document_id))
        <input type="hidden" name="document_id" value="{{ $document_id }}" />
        @endif
    {{--
    --}}

<!-- Lines from the upload form starts here-->
    
	{{--@foreach($doc->meta as $m)--}}
    @foreach($collection->meta_fields as $f)

    	<p><label>{{$f->label}}</label><br />
        @if($f->type == 'Text')
        <input class="form-control" id="meta_field_{{$f->id}}" type="text" name="meta_field_{{$f->id}}" value="{{ old('meta_field_'.$f->id, $doc->meta_value($f->id)) }}" placeholder="{{ $f->placeholder }}" @if($f->is_required == 1) {{ ' required' }} @endif />
        @elseif ($f->type == 'Textarea')
        <textarea id="document_description" class="form-control @if($f->with_rich_text_editor == 1)rich_text_editor @endif" rows="5" id="meta_field_{{$f->id}}" name="meta_field_{{$f->id}}" placeholder="{{ $f->placeholder }}" @if($f->is_required == 1) {{ ' required' }} @endif >{!! old('meta_field_'.$f->id, $doc->meta_value($f->id)) !!}</textarea>
        @elseif ($f->type == 'Numeric')
        <input class="form-control" id="meta_field_{{$f->id}}" type="number" step="0.01" min="-9999999999.99" max="9999999999.99" name="meta_field_{{$f->id}}" value="{{ old('meta_field_'.$f->id, $doc->meta_value($f->id)) }}" placeholder="{{ $f->placeholder }}" @if($f->is_required == 1) {{ ' required' }} @endif />
        @elseif ($f->type == 'Date')
        <input id="meta_field_{{$f->id}}" max="2999-12-31"  type="date" name="meta_field_{{$f->id}}" value="{{ old('meta_field_'.$f->id, $doc->meta_value($f->id)) }}" placeholder="{{ $f->placeholder }}" @if($f->is_required == 1) {{ ' required' }} @endif />

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
                    $old_vals = old('meta_field_'.$f->id, json_decode($doc->meta_value($f->id)));
                    $old_vals = is_array($old_vals) ? $old_vals : [];
                @endphp
            	<option value="{{$o}}" @if(@in_array($o, $old_vals)) selected="selected" @endif >{{$o}}</option>
            @endforeach
        </select>
		@elseif ($f->type == 'SelectCombo')
		<input type="text" class="form-control" id="meta_field_{{$f->id}}" name="meta_field_{{$f->id}}" value="{{ old('meta_field_'.$f->id, $doc->meta_value($f->id)) }}" autocomplete="off" list="optionvalues" placeholder="{{ $f->placeholder }}" @if($f->is_required == 1) {{ ' required' }} @endif />
		<label>You can select an option or type custom text above.</label>
		<datalist id="optionvalues">
            @php
                $options = explode(",", $f->options);
				sort($options);
            @endphp
            @foreach($options as $o)
                @php
                    $o = ltrim(rtrim($o));
                    $old_vals = json_decode($doc->meta_value($f->id));
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
				function getTree($children, $doc, $f, $level, $parent_id = null, $parents = null){
                    $level++;
					if(empty($children['parent_'.$parent_id])) return;
					foreach($children['parent_'.$parent_id] as $t){
							$selected = '';
                            $old_vals = old('meta_field_'.$f->id, json_decode($doc->meta_value($f->id, true)));
                            $old_vals = is_array($old_vals) ? $old_vals : [];
							if (@in_array($t->id, $old_vals)){
								$selected='selected="selected"';
							}
                            $data_pup = ($level > 1) ? 'data-pup="'.$t->parent_id.'"' : '';
							if(!empty($children['parent_'.$t->id]) && count($children['parent_'.$t->id]) > 0){ 
								echo '<option '.$data_pup.' value="'.$t->id.'" '.$selected.' class="l'.$level.' non-leaf">'.$t->label.'</option>';
								$parents_tmp = $parents. $t->label .' - ';
								getTree($children, $doc, $f, $level, $t->id, $parents_tmp);
							}
							else{
								echo '<option '.$data_pup.' class="l'.$level.'" value="'.$t->id.'" '.$selected.'>'.$t->label.'</option>';
							}
					}
				}
				}
			@endphp
        <select class="form-control selectsequencetree" id="meta_field_{{$f->id}}" name="meta_field_{{$f->id}}[]" multiple @if($f->is_required == 1) {{ ' required' }} @endif>
			@php
			getTree($children, $doc, $f, 0, $f->options);
			@endphp
		</select>
        <script>
             $('#meta_field_{{$f->id}}').val({{ preg_replace('/"/','',$doc->meta_value($f->id, true)) }});
        </script>

        @endif
        </p>
    @endforeach
    
<!-- Lines from the upload form ends here -->

        <button type="submit" class="btn btn-primary"> Save </button>
    </form>
    </div>
</div>
</div>
<script>
	var iframe = document.getElementById('pdfreader');

    iframe.onload = function() {
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        const cssLink = iframeDoc.createElement('link');
        cssLink.href = '/c-{{ $collection_id }}/doc-viewer.css';
        cssLink.rel = 'stylesheet';
        cssLink.type = 'text/css';
        iframeDoc.head.appendChild(cssLink);
    };

	var innerDoc = iframe.contentDocument || iframe.contentWindow.document;
</script>
</body>
</html>
