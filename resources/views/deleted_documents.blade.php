@extends('layouts.app',['class'=> 'off-canvas-sidebar','activePage'=>'documents','titlePage'=>'Deleted Documents', 'title'=>'Smart Repository'])

@section('content')
@push('js')
<script src="/js/jquery.dataTables.min.js"></script>
<script src="/js/jquery-ui.js" defer></script>
<script type="text/javascript" src="/js/transliteration-input.bundle.js"></script>
<link href="/css/jquery-ui.css" rel="stylesheet">
<script>
$(document).ready(function() {
    oTable = $('#documents').DataTable({
    "ajax":'/admin/deleted-documents-data',
    "serverSide":true,
    "language": 
	{          
	"processing": "<img src='/i/processing.gif'>",
	},
    "columns":[
       {data:"type",
          render:{
            '_':'display',
            'sort':'filetype'
          }
       },
       {data:"title"},
       {data:"collection"},
       {data:"size",
           render:{
             '_': 'display',
             'sort': 'bytes'
            }
        },
        {data:"created_at",
            render:{
               '_':'display',
              'sort': 'created_date'
            }
        },
        {data:"deleted_at",
            render:{
               '_':'display',
              'sort': 'deleted_date'
            }
        },
        {data:"actions"},
    ],
    });

} );

</script>
@endpush

<div class="container">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
		<div class="card-header card-header-primary">
                <h4 class="card-title ">{{__('Deleted Documents')}}</h4>
        	</div>
	        <div class="card-body">
		<div class="card search-filters-card">
		@if(!empty(env('TRANSLITERATION')))
		   <div class="col-12 text-right">
		   <input type="text" id="collection_search" placeholder="{{__('Search')}}" />
			<style>
			.dataTables_filter {
			display: none;
			}
			</style>
		   </div>
		<script>
			let searchbox = document.getElementById("collection_search");
			enableTransliteration(searchbox, '{{ env('TRANSLITERATION') }}');
			$('#collection_search').keyup(function(){
      			oTable.search($(this).val()).draw() ;
			})
		</script>
		</div>
		@endif
		<div class="table-responsive">
                    <table id="documents" class="table">
                        <thead class="text-primary">
                            <tr>
                            <th>{{__('Type')}}</th>
                            <th>{{__('Title')}}</th>
                            <th>{{__('Collection')}}</th>
                            <th>{{__('Size')}}</th>
                            <th>{{__('Created')}}</th>
                            <th>{{__('Deleted')}}</th>
                            <th class="text-right"><!--Actions--></th>
                            </tr>
                        </thead>
                    </table>
        	</div>
            </div>
        </div>
    </div>
</div>
@endsection
