@extends('layouts.app',['class' => 'off-canvas-sidebar','title'=>'Smart Repository','activePage'=>'faq','titlePage'=>'FAQ'])

@push('js')
 <link rel="stylesheet" href="/css/jquery-ui.css">
  <script src="/js/jquery-ui.js"></script>
  <script>
  $( function() {
	  $( "#accordion" ).accordion({
	  	'collapsible': true,
  		'active':false,
		'heightStyle': "content",
  	});
  } );
  </script>

@endpush
@section('content')
<div class="container">
<div class="container-fluid">

@php
    $c = $document->collection;
@endphp
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-primary"><h4 class="card-title"><a href="/collections">Collections</a> :: <a href="/collection/{{ $c->id }}">{{$c->name}}</a> :: <a href="/collection/{{ $c->id }}/document/{{ $document->id }}/details">{{ $document->title }}</a> :: Proofreading</h4></div>
                <div class="card-body">
                  <div class="row">
                      <div class="col-md-12 text-right">
                        <a href="javascript:window.history.back();" class="btn btn-sm btn-primary" title="Back">
                        <i class="material-icons">arrow_back</i>
                        </a>
                      </div>
                  </div>

                  <div class="row">
		      <h3>Proofreading results</h3>
                      <div class="col-md-12" id="accordion">
	@php
		if(!empty($connection_error)){
			echo "This service is not currently running. Please contact your administrator.";
		}
		else{
		$curation = array();
		foreach($lang_issues->matches as $i){
			$curation[$i->message][] = $i;
		}
		//print_r($curation);
		foreach($curation as $c => $list){
			echo "<h3>".$c." (".count($list).")</h3>";
			echo "<div>";
			foreach($list as $l){
				$offset = $l->context->offset;
				$length = $l->context->length;
				$context_text = $l->context->text;
				$problem_word = substr($context_text, $offset, $length);
				$context_text = substr_replace($context_text, '<span class="lang_problem">'.$problem_word.'</span>', $offset, $length);
				echo '<p>'.$context_text.'</p>';
			}
			echo "</div>";
		}
		}
	@endphp
		
                      </div>
                  </div>


                   </div><!-- card body ends -->
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
