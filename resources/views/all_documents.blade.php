@extends('layouts.app',['class'=> 'off-canvas-sidebar','activePage'=>'documents','titlePage'=>'All Documents', 'title'=>'Smart Repository'])

@section('content')
@push('js')
<script src="/js/jquery.dataTables.min.js"></script>
<script src="/js/jquery-ui.js" defer></script>
<script type="text/javascript" src="/js/transliteration-input.bundle.js"></script>
<link href="/css/jquery-ui.css" rel="stylesheet">
<script>
var deldialog;
$(document).ready(function() {
    oTable = $('#documents').DataTable({
    "aoColumnDefs": [
           { "bSortable": false, "aTargets": [4]},
           { "className": 'text-right dt-nowrap', "aTargets": [2,3]},
           { "className": 'td-actions text-right dt-nowrap', "aTargets": [4]}
     ],
    "processing":true,
    "order": [[ 3, "desc" ]],
    "serverSide":true,
    "ajax":'/documents/search',
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
       {data:"size",
           render:{
             '_': 'display',
             'sort': 'bytes'
            }
        },
        {data:"updated_at",
            render:{
               '_':'display',
              'sort': 'updated_date'
            }
        },
        {data:"actions"},
    ],
    });

} );

function showDeleteDialog(document_id){
	str = randomString(6);
	$('#text_captcha').text(str);
	$('#hidden_captcha').text(str);
	$('#delete_doc_id').val(document_id);
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
@endpush
<div id="deletedialog" style="display:none;">
                <form name="deletedoc" method="post" action="/document/delete">
                @csrf
                <p>Enter <span id="text_captcha"></span> to delete</p>
                <input type="text" name="delete_captcha" value="" />
                <input type="hidden" id="hidden_captcha" name="hidden_captcha" value="" />
                <input type="hidden" id="delete_doc_id" name="document_id" value="" />
                <button class="btn btn-danger" type="submit" value="delete">Delete</button>
                </form>
            </div>

<div class="container">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
		<div class="card-header card-header-primary">
                <h4 class="card-title ">{{__('All Documents')}}</h4>
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
                            <th>{{__('Size')}}</th>
                            <th>{{__('Created')}}</th>
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
