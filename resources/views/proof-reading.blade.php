@extends('layouts.app',['class' => 'off-canvas-sidebar','title'=>'Smart Repository','activePage'=>'faq','titlePage'=>'FAQ'])

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
                      <div class="col-md-12">
	@php
		if(!empty($connection_error)){
			echo "ERROR: Language tool service is not accessible!";
		}
		else{
		$curation = array();
		foreach($lang_issues->matches as $i){
			$curation[$i->message][] = $i;
		}
		//print_r($curation);
		foreach($curation as $c => $list){
			echo "<h3>$c</h3>";
			foreach($list as $l){
				echo "<p>".$l->context->text."</p>";
			}
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
