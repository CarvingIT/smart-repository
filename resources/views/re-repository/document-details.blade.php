@extends('layouts.app',['class' => 'off-canvas-sidebar','title'=>'Smart Repository','activePage'=>'faq','titlePage'=>'FAQ'])

@php
    $c = \App\Collection::find($document->collection_id);
    $meta_fields = $c->meta_fields;
    $meta_labels = array();
    foreach($meta_fields as $mf){
        $meta_labels[$mf->id] = @$mf->label;
    }
	$col_config = json_decode($c->column_config);
@endphp
@push('js')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link rel="stylesheet" href="/css/jquery-ui.css">
<script src="/js/jquery-ui.js"></script>
<style>
.Draft {
  color: grey;
}

.Active {
  color: green;
}

.Repealed {
  color: red;
}
</style>

<script>
 $( function() {
      $( "#accordion" ).accordion({
        'collapsible': true,
        'active':false,
        'heightStyle': "content",
    });
  } );

function randomString(length) {
   var result           = '';
   var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
   var charactersLength = characters.length;
   for ( var i = 0; i < length; i++ ) {
      result += characters.charAt(Math.floor(Math.random() * charactersLength));
   }
   return result;
}

function showDeleteDialog(){
    str = randomString(6);
    $('#text_captcha').text(str);
    $('#hidden_captcha').val(str);
        deldialog = $( "#deletedialog" ).dialog({
        title: 'Are you sure ?',
        resizable: true
        });
}
$(document).ready(function() {
        //alert("js is working");
        src = "{{ route('titlesuggest') }}";
        $( "#title-autocomplete" ).autocomplete({
            source: function( request, response ) {
                $.ajax({
                    url: src,
                    method: 'GET',
                    dataType: "json",
                    data: {
                        term : request.term
                    },
                    success: function(data) {
                        if(data.length > 0)
                        	response($.map(data, function(item){
								return {
									label:item.title,
									value:item.id
								}
							}
						));
                    },
                });
            },
            select: function (event, ui){
                $("#title-autocomplete").val(ui.item.label);
                $("#related_document_id").val(ui.item.value);
                return false;
            },
            minLength: 1,
        });
    });

</script>
@if (!empty($col_config->show_word_cloud))
<script src="/js/jQWCloudv3.4.1.js"></script>
<script>
var docwords = new Array();
@foreach($word_weights as $word=>$weight)
    docwords.push({word:'{{ $word }}', weight:{{ $weight }} });
@endforeach
$(document).ready(function()
{
    $("#wordcloud").jQWCloud({
        words:docwords, 
        //cloud_color: 'yellow',        
        minFont: 10,
        maxFont: 50,
        //fontOffset: 5,
        //cloud_font_family: 'Owned',
        //verticalEnabled: false,
        padding_left: 1,
        //showSpaceDIV: true,
        //spaceDIVColor: 'white',
        word_common_classes: 'WordClass',
        word_mouseEnter :function(){
            $(this).css("text-decoration","underline");
        },
        word_mouseOut :function(){
            $(this).css("text-decoration","none");
        },
        word_click: function(){
            //alert("Edit feature coming soon! You would like to edit the word: " +$(this).text());
            alert("Feature coming soon!");
        },
        beforeCloudRender: function(){
               date1=new Date();
        },
        afterCloudRender: function(){
                var date2=new Date();
                console.log("Cloud Completed in "+(date2.getTime()-date1.getTime()) +" milliseconds");
            }
    });
});
</script>
@endif
@endpush


@section('content')
<div class="container">
<div class="container-fluid">

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-primary"><h4 class="card-title"><a href="/collections">{{ __('Collections') }}</a> :: <a href="/collection/{{ $c->id }}">{{$c->name}}</a> :: Document Details</h4></div>
                <div class="card-body">

                  <div class="row">
                      <div class="col-md-12 text-right">
                        <a href="#" onclick="$('#related_document_form').show(); return false;" class="btn btn-sm btn-primary" title="Related Documents">
                        <i class="material-icons">playlist_add</i>
                        </a>
                        <a href="/collection/{{ $c->id }}" class="btn btn-sm btn-primary" title="Back">
                        <i class="material-icons">arrow_back</i>
                        </a>
                      </div>
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

				<div id="related_document_form" style="display:none;">
				<h4>Add a related document</h4>
				<form method="post" action="/collection/{{ $document->collection->id }}/document/{{ $document->id }}/add-related-document">
				@csrf
				<input type="hidden" id="document_id" name="document_id" value="{{ $document->id }}" />
				<input type="hidden" id="related_document_id" name="related_document_id" value="" />
				<div class="row">
                    <div class="col-md-2">
				<label for="title-autocomplete" class="col-md-12 col-form-label text-md-right">Search title</label>
		    </div>
                    <div class="col-md-8">
                    	<input class="form-control" type="text" id="title-autocomplete" name="title-autocomplete"/> 
		    </div>
				</div>
				<div class="row">
                    <div class="col-md-2">
		   				<label for="display_order" class="col-md-12 col-form-label text-md-right">Display order</label>
					</div>
                    <div class="col-md-8">
                    <input class="form-control" type="number" id="display_order" name="display_order"/> 
					</div>
				</div>
				<div class="row">
                    <div class="col-md-2">
		   				<label for="title_override" class="col-md-12 col-form-label text-md-right">Override title</label>
					</div>
                    <div class="col-md-8">
                    <input class="form-control" type="text" id="title_override" name="title"/> 
					</div>
				</div>
				<div class="row">
                    <div class="col-md-12 text-center">
						<input class="btn btn-primary" type="submit" value="Add" />
						<input class="btn" type="button" value="Cancel" onclick="$('#related_document_form').hide();"/>
					</div>
				</div>
				</form>
				<br />
				</div>

					@php $display_meta = [];@endphp
					@foreach($document->collection->meta_fields as $meta_field)
						@php
                                        		$placeholder = strtolower($meta_field->placeholder);
                                        		$meta_placeholder = preg_replace("/ /","-",$placeholder);
                                        		$display_meta[$meta_placeholder]=$document->meta_value($meta_field->id);
                                		@endphp

					@endforeach

                  <div class="row">
                    <div class="col-md-12">
                       <h4 class="d-flex justify-content-start align-items-center">
                        <!--span id="doc-title" class="col-md-12"-->
			@if($c->content_type == 'Uploaded documents')
				<!--h4-->
				@if($document->type == 'application/pdf')
					@if(env('ENABLE_PDF_READER') == 1)
					<a href="/collection/{{ $c->id }}/document/{{ $document->id }}/pdf-reader" target="_new"><img class="file-icon" src="/i/file-types/{{ $document->icon($document->path) }}.png" style="float:left;"></a>&nbsp;
            				<a title="Read online" href="/collection/{{ $document->collection_id }}/document/{{ $document->id }}/pdf-reader" target="_new">
					@else
					<a href="/collection/{{ $c->id }}/document/{{ $document->id }}" target="_new"><img class="file-icon" src="/i/file-types/{{ $document->icon($document->path) }}.png" style="float:left;"></a>&nbsp;
            				<a title="Read online" href="/collection/{{ $document->collection_id }}/document/{{ $document->id }}" target="_new">
					@endif
				@elseif($document->type == 'application/vnd.openxmlformats-officedocument.presentationml.presentation')
					<a href="/collection/{{ $c->id }}/document/{{ $document->id }}">
					<img class="file-icon" src="/i/file-types/{{ $document->icon($document->path) }}.png" style="float:left;"></a>&nbsp;<a href="/collection/{{ $c->id }}/document/{{ $document->id }}">
				@elseif(preg_match('/^audio/',$document->type) || preg_match('/^video/',$document->type))
					<div style="text-align:center;">
                        		<h3><a href="/collection/{{ $c->id }}/document/{{ $document->id }}"><img class="file-icon" src="/i/file-types/{{ $document->icon($document->path) }}.png"></a>{{ $document->title }}</h3>
        				<video controls >
        				<source src="/collection/{{ $c->id }}/document/{{ $document->id }}" type="video/mp4">
        				</video>
        				</div>
            			<a title="See online" href="/collection/{{ $document->collection_id }}/document/{{ $document->id }}/media-player" target="_blank">
				@elseif($document->type == 'image/jpeg' || $document->type == 'image/png')
					<div style="text-align:center;">
                        		<h3><a href="/collection/{{ $c->id }}/document/{{ $document->id }}"><img class="file-icon" src="/i/file-types/{{ $document->icon($document->path) }}.png"></a>{{ $document->title }}</h3>
					<img src="/collection/{{ $c->id }}/document/{{ $document->id }}" style="width:50%">
					</div>
            			<a title="View" href="/collection/{{ $document->collection_id }}/document/{{ $document->id }}" target="_blank">
				@else
				@if ($document->path != 'N/A')
				<a href="/collection/{{ $c->id }}/document/{{ $document->id }}"><img class="file-icon" src="/i/file-types/{{ $document->icon($document->path) }}.png" style="float:left;"></a>&nbsp;<a href="/collection/{{$c->id}}/document/{{$document->id}}" target="_new" style="text-decoration:underline;">
				@else
				<a href="/collection/{{ $c->id }}/document/{{ $document->id }}"><img class="file-icon" src="/i/file-types/{{ $document->icon($document->path) }}.png" style="float:left;" /></a>&nbsp;<a href="/collection/{{$c->id}}/document/{{$document->id}}" target="_blank" style="text-decoration:underline;">

				@endif
				@endif
			@else
			<a href="{{ $document->url }}" target="_new" style="text-decoration:underline;">
			@endif
			{!! strip_tags($document->title) !!}
                         <!--span class='{{ $display_meta['status'] }}' >
                               <i class="fa fa-file"></i>
                         </span-->
			</a></h4>
			<!--/span-->{{-- don't need this span --}}
                        </div>
				<br />

			@if($c->content_type == 'Uploaded documents')
			@if ($document->related_documents->count() > 0 || $document->related_to->count() >0)
				{{--@if(!empty($document->related_documents) && !$document->related_documents->isEmpty())--}}
				<div class="col-md-9 row">
			@else
				<div class="col-md-12 row">
			@endif
					<ul>
					<li>
						@if(!empty($display_meta['document-short-name']))
						{{ $display_meta['document-short-name'] }} 
						@endif
						@if(!empty($display_meta['id-as-per-document']))
						({{ $display_meta['id-as-per-document'] }})
						@endif
					</li>
					<li>
						@if(!empty($display_meta['issuing-authority']))
						By {{ $display_meta['issuing-authority'] }} 
						@endif
						@if(!empty($display_meta['date-of-issuance']))
						on {{ $display_meta['date-of-issuance'] }}
						@endif
					</li>
					<li>{{ $display_meta['sector'] }} {{ $display_meta['type-of-document/-document-type-detail'] }}</li>
					<li>
						@if(!empty($display_meta['start-year-of-consideration']) || !empty($display_meta['end-year-of-consideration'])) 
						Applicable 
						@endif
						@if(!empty($display_meta['start-year-of-consideration'])) 
							from {{ $display_meta['start-year-of-consideration'] }} 
						@endif
						@if(!empty($display_meta['end-year-of-consideration']))
							to {{ $display_meta['end-year-of-consideration'] }}
						@endif
					</li>
					<li>{{ $display_meta['petitioner'] }}</li>
					<li>{{ $display_meta['respondents'] }}</li>
					<li>{{ $display_meta['additional-tags'] }}</li>
					</ul>


			@if(\Auth::user() && ($c->require_approval == 1))
                  	<div class="col-md-12">
				<h3>Document Status</h3>
				@if(!empty($document->approved_on))
				@endif

				@if(\Auth::user()->hasPermission($document->collection_id ,'APPROVE'))
				<form name="doc_approve" method="post" action="/approve-document">
				@csrf		
				<input type="hidden" name="collection_id" value="{{ $document->collection_id }}">
				<input type="hidden" name="document_id" value="{{ $document->id }}">
				<br />
					@if(!empty($document->approved_on))
						<h4>Document is Approved</h4> 
						<input id="approved_on" type="hidden" name="approved_on" value=""/>
						<button type="submit" class="btn btn-primary">Reject Document</button>
					@else
						<h4>Document is unapproved</h4> 
						<input id="approved_on" type="hidden" name="approved_on" value="1"/>
						<button type="submit" class="btn btn-primary">Approve Document</button>
					@endif
				</form>
				@endif {{-- has approve permission --}}
			</div>
				@if(empty($document->approved_on))
                  	<div class="col-md-12">
                   		<h3>Comments</h3>
				@foreach($comments as $comment)
				<div>{{ $comment->user->name }}({{ $comment->user->email }}) said: <p>{{ $comment->comment }}</p></div>
				@endforeach	
			</div>
                  	<div class="col-md-12">
				<form name="comment" action="/save-comment" method="post">
				@csrf
				<input type="hidden" name="collection_id" value="{{ $document->collection_id }}">
				<input type="hidden" name="document_id" value="{{ $document->id }}">
                   			<h3>Leave a Comment</h3>
                    			<div id="doc_meta_description" class="col-md-12"><textarea name="comment" id="comment" class="form-control" cols="80" rows="10" value="" placeholder="Enter your comment here" required ></textarea></div>
					<button type="submit" class="btn btn-primary"> Add Comment </button>
				</form>	
                        </div>
				@endif {{-- display comments only for unapproved documents --}}
			@endif {{-- display document status and comment section only for logged in user --}}

				</div>
				@if ($document->related_documents->count() > 0 || $document->related_to->count() > 0)
				<div class="col-md-3" id="accordion">
				<!--div class="col-md-3"-->
					<h5>Related Documents</h5>
					<p style="background-color:#E6E9E3;border:none;">
					{{-- $display_meta['document-short-name'] --}}
					@php
						$display_docs = [];
						$parent = App\RelatedDocument::where('related_document_id', $document->id)->first();
						if(empty($parent)){		
						$p_doc = App\Document::where('id',$document->id)->first();
						}		
						else{
						$p_doc = App\Document::where('id',$parent->document_id)->first();
						}
						foreach($p_doc->collection->meta_fields as $meta_field){
                                        		$p_doc_placeholder = strtolower($meta_field->placeholder);
                                        		$p_doc_meta_placeholder = preg_replace("/ /","-",$p_doc_placeholder);
							$p_doc_meta[$p_doc_meta_placeholder] = $p_doc->meta_value($meta_field->id);	
						}
					        echo '<a href="/collection/'.$p_doc->collection->id.'/document/'.$p_doc->id.'/details" style="color:#3f819e;">'.$p_doc_meta['document-short-name'].'</a><br /><br />';
					@endphp
						@php $r_d_doc= []; $display_doc = []; @endphp

						@if(!$document->related_documents->isEmpty())
						@foreach ($document->related_documents->sortBy('display_order') as $r_d)
						@php 
						$r_d_doc = App\Document::where('id',$r_d->related_document_id)->first();
						foreach($r_d_doc->collection->meta_fields as $meta_field){
                                        		$r_d_placeholder = strtolower($meta_field->placeholder);
                                        		$r_d_meta_placeholder = preg_replace("/ /","-",$r_d_placeholder);
							$r_d_meta[$r_d_meta_placeholder] = $r_d_doc->meta_value($meta_field->id);	
						}
						if(preg_match("/Principal/i",$r_d->title)){
							$display_doc['Principal'][] = array('title'=>$r_d->title,'collection_id'=>$r_d->related_document->collection->id,'doc_id'=>$r_d->related_document->id);
						}
						if(preg_match("/1st Amendment/i",$r_d->title)){
							$display_doc['1st_Amendment'][] = array('title'=>$r_d->title,'collection_id'=>$r_d->related_document->collection->id,'doc_id'=>$r_d->related_document->id);
						}
						if(preg_match("/2nd Amendment/i",$r_d->title)){
							$display_doc['2nd_Amendment'][] = array('title'=>$r_d->title,'collection_id'=>$r_d->related_document->collection->id,'doc_id'=>$r_d->related_document->id);
						}
						if(preg_match("/3rd Amendment/i",$r_d->title)){
							$display_doc['3rd_Amendment'][] = array('title'=>$r_d->title,'collection_id'=>$r_d->related_document->collection->id,'doc_id'=>$r_d->related_document->id);
						}
						if(preg_match("/4th Amendment/i",$r_d->title)){
							$display_doc['4th_Amendment'][] = array('title'=>$r_d->title,'collection_id'=>$r_d->related_document->collection->id,'doc_id'=>$r_d->related_document->id);
						}
						if(preg_match("/5th Amendment/i",$r_d->title)){
							$display_doc['5th_Amendment'][] = array('title'=>$r_d->title,'collection_id'=>$r_d->related_document->collection->id,'doc_id'=>$r_d->related_document->id);
						}
						if(preg_match("/6th Amendment/i",$r_d->title)){
							$display_doc['6th_Amendment'][] = array('title'=>$r_d->title,'collection_id'=>$r_d->related_document->collection->id,'doc_id'=>$r_d->related_document->id);
						}
						if(preg_match("/7th Amendment/i",$r_d->title)){
							$display_doc['7th_Amendment'][] = array('title'=>$r_d->title,'collection_id'=>$r_d->related_document->collection->id,'doc_id'=>$r_d->related_document->id);
						}
						if(preg_match("/8th Amendment/i",$r_d->title)){
							$display_doc['8th_Amendment'][] = array('title'=>$r_d->title,'collection_id'=>$r_d->related_document->collection->id,'doc_id'=>$r_d->related_document->id);
						}
						if(preg_match("/9th Amendment/i",$r_d->title)){
							$display_doc['9th_Amendment'][] = array('title'=>$r_d->title,'collection_id'=>$r_d->related_document->collection->id,'doc_id'=>$r_d->related_document->id);
						}
						if(preg_match("/10th Amendment/i",$r_d->title)){
							$display_doc['10th_Amendment'][] = array('title'=>$r_d->title,'collection_id'=>$r_d->related_document->collection->id,'doc_id'=>$r_d->related_document->id);
						}
						@endphp
						@endforeach
						@endif 
						@php 
	//print_r($display_doc); exit;
if(!empty($display_doc)){
foreach($display_doc as $key => $value){
//print_r($value);	
//echo $key. $value['collection_id']."<hr />";
//exit;
//echo $value[0]['collection_id']; 
echo "<strong>".preg_replace("/_/"," ",$key)."</strong><br />";
//echo "<strong><a href='/collection/".$value['collection_id']."/document/".$value['doc_id']."/details'>".preg_replace("/_/"," ",$key)."</a></strong>";
	//echo "<ul>";
	foreach($value as $item){
	$doc_item = preg_replace("/Principal|1st|2nd|3rd|4th|5th|6th|7th|8th|9th|10th Amendment/i","",$item['title']);
	echo "<a href='/collection/".$item['collection_id']."/document/".$item['doc_id']."/details' style='color: #3f819e;'>".$doc_item."</a><br />";
	}
	//echo "</ul>";
	echo "<br />";
}
}
@endphp
{{--
						<!--a href="/collection/{{ $r_d->related_document->collection->id }}/document/{{ $r_d->related_document->id }}/details" style="color:#3f819e;">{{ $r_d->title }}</a><br /><br /-->
--}}
					</p>
				</div>
				@endif
			</div><!-- row ends -->
			@endif

        <div id="deletedialog" style="display:none;">
        <form name="deletedoc" method="post" action="/document/delete">
        @csrf
        <p>Enter <span id="text_captcha"></span> to delete</p>
        <input type="text" name="delete_captcha" value="" />
        <input type="hidden" id="hidden_captcha" name="hidden_captcha" value="" />
        <input type="hidden" id="delete_doc_id" name="document_id" value="{{ $document->id }}" />
        <button class="btn btn-danger" type="submit" value="delete">Delete</button>
        </form>
        </div>

                  <div class="row">
                      <div class="col-md-12">
						@if (Auth::user() && Auth::user()->canEditDocument($document->id))
                        <a href="/document/{{ $document->id }}/edit" class="btn btn-sm btn-primary" title="Edit">
                        <i class="material-icons">edit</i>
                        </a>
						@endif
						@if (Auth::user() && Auth::user()->canDeleteDocument($document->id))
                        <a href="javascript:return false;" onclick="showDeleteDialog();" class="btn btn-sm btn-primary" title="Delete">
                        <i class="material-icons">delete</i>
                        </a>
						@endif
                      </div>
                  </div>
						@if (!empty($col_config->show_word_cloud))
                        <div class="col-md-12"><div id="wordcloud"><img src='/i/processing.gif'></div></div>
						@endif

						@if (!empty($col_config->show_audit_trail))
						<div class="col-md-12">
						<h3>Audit Trail</h3>
						@php
						$all_audits = [];
						foreach($document->audits as $a){
							$audit_metadata = $a->getMetadata();
							$all_audits[$audit_metadata['audit_created_at']][] = $a;
						}

						foreach($document->meta as $m){
							foreach($m->audits as $ma){
								$audit_metadata = $ma->getMetadata();
								$all_audits[$audit_metadata['audit_created_at']][] = $ma;
							}
						}
						// ordering of audits
						krsort($all_audits);
						@endphp

						@if(count($all_audits) == 0)
							<p>No changes have taken place to the meta information of this document.</p>
						@else
						<div class="col-md-12">
						@foreach($all_audits as $k=>$va)
						<h3>{{ $k }}</h3>
						<div>
						@foreach($va as $v)
							@php
								$audit_meta = $v->getMetadata();
								$modified = $v->getModified();
								$model_type = $v->auditable_type;
								$model_id = $v->auditable_id;
							@endphp
								<p>Audit event: {{ @$audit_meta['audit_event'] }}</p>
								<p>User: {{ @$audit_meta['user_name'] }}</p>
								<p>User agent: {{ @$audit_meta['audit_user_agent'] }}</p>
								<p>URL: {{ @$audit_meta['audit_url'] }}</p>
								<p>IP Address: {{ @$audit_meta['audit_ip_address'] }}</p>
								<h4>Modifications</h4>
								@foreach($modified as $mk => $mv)
									@php
									if($mk == 'meta_field_id' || $mk == 'id') continue;
									$what_changed = $mk;
									if($model_type == 'App\MetaFieldValue'){
										$mfv = App\MetaFieldValue::find($model_id);
										$what_changed = @$mfv->meta_field->label;
									}
									@endphp
								<p>
								<em class="audit-changes">{{ $what_changed }}</em>
									was updated from
									<em class="audit-changes">@if(!empty($mv['old'])) {{ $mv['old'] }} @else {{ 'NULL' }} @endif </em>
									to
								<em class="audit-changes">@if(!empty($mv['new'])) {{ $mv['new'] }} @else {{ 'NULL' }} @endif</em>.
								</p>
								<hr />
								@endforeach	
						@endforeach
						</div>
						@endforeach
						</div><!-- accordion ends -->
						@endif

						</div>
						@endif

						@php
						$all_collections = \App\Collection::whereNull('parent_id')->get();
						$level = '';
						function listChildren($c,$level){
							$level = $level.'- ';
							foreach($c->children as $ch){
								echo '<option value="'.$ch->id.'">'.$level.$ch->name.'</option>';
								if($ch->children->count()){
								listChildren($ch, $level);
								}
							}
						}
						@endphp

						@if(Auth::user() && Auth::user()->hasRole('admin'))
							<div class="col-md-12">
								<h3>Actions</h3>
								<form method="post" action="/collection/move_document">
								@csrf
								<input type="hidden" name="document_id" value="{{ $document->id }}" />
								<div class="row">
								<div class="col-md-9">
									<select class="selectpicker" name="collection_id">
										<option value="">Move to - </option>
										@foreach ($all_collections as $c)
										<option value="{{ $c->id }}">
										{{ $c->name }}
										</option>
										@php
										if ($c->children->count()){
										listChildren($c, $level);
										}	
										@endphp
										@endforeach
									</select>
								</div>
								<div class="col-md-3">
									<button type="submit" class="btn btn-primary"> Move </button>
								</div>
								</div>
								</form>
							</div>	
						@endif


                    </div>

                   </div><!-- card body ends -->
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
