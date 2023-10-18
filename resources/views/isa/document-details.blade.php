@extends('layouts.app',['class'=> 'off-canvas-sidebar','title'=>'Smart Repository'])

@section('content')
<div class="container">
	<div class="container-fluid"> <div class="row justify-content-center">
		<div class="col-md-12">
			<div class="card">
				<div class="card-header card-header-primary"><h4 class="card-title"><a href="/collections">{{
					__('Collections') }}</a> :: <a href="/collection/"></a> :: Document Details</h4></div> <div
					class="card-body">

					<div class="row"> <div class="col-md-12 text-right">
						<a href="javascript:window.history.back();" class="btn btn-sm btn-primary" title="Back"> <i
							class="material-icons">arrow_back</i> </a>
							</div> </div>

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
							</div>

							
						</div>


						<div class="col-md-12">
							<h3>Document Status</h3>
						
							<form name="doc_approve" method="post" action="/approve-document">
							@csrf		
							<input type="hidden" name="collection_id" value="">
							<input type="hidden" name="document_id" value="">
							<br />
								
									<h4>Document is Approved</h4> 
									<input id="approved_on" type="hidden" name="approved_on" value=""/>
									<button type="submit" class="btn btn-primary">Disapprove Document</button>
								
									<h4>Document is Disapproved</h4> 
									<input id="approved_on" type="hidden" name="approved_on" value="1"/>
									<button type="submit" class="btn btn-primary">Approve Document</button>
								
							</form>
							
						</div>


				</div>
			</div>
		</div>
	</div>
</div>
@endsection