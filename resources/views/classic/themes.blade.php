@extends('layouts.app',['class'=> 'off-canvas-sidebar','title'=>'Smart Repository'])

@section('content')
<!--
<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
-->
<script src="/js/jquery.dataTables.min.js"></script>
<script src="/js/jquery-ui.js" defer></script>
<link href="/css/jquery-ui.css" rel="stylesheet">

<script>
$(document).ready(function() {
    $('#collections').DataTable();
} );

function showDeleteDialog(collection_id){
        str = randomString(6);
        $('#text_captcha').text(str);
        $('#hidden_captcha').text(str);
        $('#delete_collection_id').val(collection_id);
        deldialog = $( "#deletedialog" ).dialog({
                title: 'Are you sure ?',
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
    <div class="row">
        <div class="col-md-12">
            <div class="card">
            <div class="card-header card-header-primary">
                <h6 class="card-title">{{ __('Themes') }}</h6>
                    <!--div class="card-header-corner" style="margin-top:-4%;"><a href="/admin/collection-form/new"><img class="icon" src="/i/plus.png"/></a></div-->
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
	
                    <div class="container">
					<div class="row">
                            @php
							$meta = App\MetaField::where('label', env('THEME_FIELD_LABEL','Theme'))->first();
							$meta_id = $meta->id;	
							$major_themes = explode("|", env('MAJOR_THEMES',"themes"));
							$major_theme_ids = [];
                            $tags = App\Taxonomy::orderBy('label','ASC')->get();
                            $children = [];
                            foreach($tags as $t){
                              $children['parent_'.$t->parent_id][] = $t;
								if(in_array($t->label, $major_themes)){
									$major_theme_ids[$t->label] = $t->id;
								}
                            }
					function getTree($children, $parent_id = null, $meta_id=null, $rmfv_map=null){
		   		      if(empty($children['parent_'.$parent_id])) return;
         				foreach($children['parent_'.$parent_id] as $t){
        					if(!empty($children['parent_'.$t->id]) && count($children['parent_'.$t->id]) > 0){
                				// get compare with query string parameter to mark as checked
                                    echo '<li>';
                                        echo '<strong>'.$t->label.'</strong>';
										echo '<ul>';
                  						getTree($children, $t->id, $meta_id, $rmfv_map);
										echo '</ul>';
                                    echo '</li>';
             				}
             				else{
	                            echo '<li class="ct-num">';
                  				echo '<a href="/collection/1?collection_id=1&meta_'.$meta_id.'[]='.$t->id.'">'.$t->label.'</a>';
                                echo '</li>';
             				}
				}#foreach	
		}#function ends
						
      					 @endphp
						@foreach ($major_themes as $mt)
						<div class="col-lg-4">
						<h5>{{ $mt }}</h5><br/>
                        <ul class="list-unstyled">
							@php
								getTree($children, $major_theme_ids[$mt], $meta_id);	
							@endphp
                        </ul>
						</div><!-- col-lg-4 -->
						@endforeach
						</div><!-- row -->
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
