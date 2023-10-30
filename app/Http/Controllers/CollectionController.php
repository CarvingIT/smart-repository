<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Collection;
use App\Role;
use Illuminate\Support\Facades\Auth;
use Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Elastic\Elasticsearch\ClientBuilder;
use App\StorageTypes;
use App\SpideredDomain;
use App\DesiredUrl;
use App\UrlSuppression;
use App\CollectionMailbox;
use App\UserPermission;
use Rap2hpoutre\FastExcel\FastExcel;
use App\DocumentApproval;
use App\Document;

class CollectionController extends Controller
{
    public function __construct()
    {
        //$this->middleware('collection_view');
    }

    public function index(){
        $collections = Collection::where('parent_id', null)->get();
        return view('collectionmanagement', ['collections'=>$collections, 'activePage'=>'Collections','titlePage'=>'Collections']);
    }

    public function add_edit_collection($collection_id){
        if($collection_id == 'new'){
            $collection = new \App\Collection();
        }
        else{
            $collection = \App\Collection::find($collection_id);
        }
	#$storage_types = StorageTypes::all();
	$storage_disks =  config('filesystems.disks');
        return view('collection-form', ['collection'=>$collection,'storage_disks'=>$storage_disks,'activePage'=>'Collection', 'titlePage'=>'Collection']);
    }

    public function userCollections($perms = ['VIEW', 'MAINTAINER']){
        /*
         Get all public collections 
         plus collections to which the current user has access.
         Access to members-only collection is determined by db_table:user_permissions 
        */
        $user_collections = [];
        $user_permissions = empty(Auth::user()) ? [] : Auth::user()->accessPermissions;
        foreach($user_permissions as $u_p){
            if(in_array($u_p->permission->name, $perms)
				&& !in_array($u_p->collection_id, $user_collections)){
                $user_collections[] = $u_p->collection_id;
            }
        }
        $collections = Collection::where('parent_id', null)
			->where(function($q) use($user_collections){
				$q->whereIn('id', $user_collections)
				->orWhere('type','=','Public');
			})
			->get();
	return $collections;
    }

    public function list(){
		$collections = $this->userCollections(['VIEW_OWN','VIEW','MAINTAINER']);
        return view('collections', ['title'=>'Smart Repository','activePage'=>'collections','titlePage'=>'Collections','collections'=>$collections]);
    }

    public function save(Request $request){
         if(empty($request->input('collection_id'))){
            $c = new \App\Collection;
         }
         else{
            $c = \App\Collection::find($request->input('collection_id'));
         }
         $c->name = $request->input('collection_name');
         $c->description = $request->input('description');
         $c->type = empty($request->input('collection_type'))?'Public':$request->input('collection_type');
         $c->storage_drive = $request->input('storage_drive');
         $c->content_type = $request->input('content_type');
         $c->require_approval = $request->input('require_approval');
         $c->user_id = Auth::user()->id;
         try{
            $c->save();
            Session::flash('alert-success', 'Collection saved successfully!');
         }
         catch(\Exception $e){
            Session::flash('alert-danger', $e->getMessage());
            return redirect('/admin/collectionmanagement');
         }
         // maintainer ID
         if(!empty($request->input('maintainer'))){
            $maintainer = \App\User::where('email', '=', $request->input('maintainer'))->first();
            $permission = \App\Permission::where('name','=','MAINTAINER')->first();
            $maintainer_permission = \App\UserPermission::where('collection_id','=',$c->id)->where('permission_id','=',$permission->id)->first();
            $maintainer_id = empty($maintainer->id)? null : $maintainer->id;
            if($maintainer_permission){
                $maintainer_permission->delete();
            }
            if($maintainer_id){
                $new_maintainer_permission = new \App\UserPermission();
                $new_maintainer_permission->permission_id = $permission->id;
                $new_maintainer_permission->collection_id = $c->id;
                $new_maintainer_permission->user_id = $maintainer_id;
                $new_maintainer_permission->save();
            }
            else{
                Session::flash('alert-warning', 'Maintainer was not found');
            }
         } 
         // create a storage dir for this collection if it does not exist
         if (!file_exists(storage_path().'/app/smartarchive_assets/'.$c->id.'/0')) {
            mkdir(storage_path().'/app/smartarchive_assets/'.$c->id.'/0', 0777, true);
         }
         return redirect('/admin/collectionmanagement');
    }

    public function collection($collection_id, Request $request){
        $collection = Collection::find($collection_id);
		$length = empty($request->length)? 10 : $request->limit;
		$start = empty($request->start)? 0 : $request->start;
        $documents = \App\Document::where('collection_id','=',$collection_id)
			 ->whereNotNull('approved_on')
			 ->orderby('updated_at','DESC');
		$total_count = $documents->count();
		$documents = $documents->limit($length)->offset($start)->get();
        return view('isa.collection', ['collection'=>$collection, 
			'filtered_results_count'=>$total_count,
			'results'=>$documents,'documents'=>$documents, 
			'activePage'=>'collection','titlePage'=>'Collections', 
			'title'=>'Smart Repository']);
    }

    public function collectionUsers($collection_id){
        $collection = Collection::find($collection_id);
        $user_permissions = \App\UserPermission::where('collection_id', '=', $collection_id)->get();
        //$user_permissions;
        $collection_users = array();
        foreach($user_permissions as $u_p){
            $collection_users[$u_p->user_id][] = $u_p;
        }
        return view('collection_users', ['collection'=>$collection, 'collection_users'=>$collection_users,'titlePage'=>'Collection Users','activePage'=>'Collection Users','title'=>'Collection Users']);
    }

    public function showCollectionUserForm($collection_id, $user_id=null){
        $user_permissions = array();
        $user = null;
	$has_approval=array();
	$has_approval = \App\Collection::where('id','=',$collection_id)->where('require_approval','=','1')->get();
        if(!empty($user_id)){
            $user = \App\User::find($user_id);
            $u_permissions = \App\UserPermission::where('user_id','=',$user_id)
                ->where('collection_id','=',$collection_id)->get();
            foreach($u_permissions as $u_p){
                $user_permissions['p'.$u_p->permission_id] = 1;
            }
        }
        return view('collection-user-form', ['collection'=>\App\Collection::find($collection_id), 
            'user'=>$user, 
            'user_permissions'=>$user_permissions,
	    'collection_has_approval'=>$has_approval,
            'title'=>'Collection User Form',
	    'activePage'=>'Collection User Form',
	    'titlePage'=> 'Collection User Form'				
	]);
    }

    public function saveUser(Request $request){
	/*
	// WHY IS THIS VALIDATION NEEDED ? 
	// Just check if the user exists in the database
	$request->validate([
    	'user_id' => 'email:rfc,dns'
	]);
	 */
        $user = \App\User::where('email','=',$request->user_id)->first();
        // first delete all permissions on the collection
	if($user){
	    \App\UserPermission::where('collection_id','=',$request->collection_id)
            ->where('user_id','=',$user->id)->delete(); 
          foreach($request->permission as $p){
            $user_permission = new \App\UserPermission;
            $user_permission->user_id = $user->id;
            $user_permission->collection_id = $request->collection_id;
            $user_permission->permission_id = $p; 
            $user_permission->save();
          }
	}
        return $this->collectionUsers($request->collection_id);
    }

    public function removeUser($collection_id, $user_id){
        \App\UserPermission::where('collection_id','=',$collection_id)
            ->where('user_id','=',$user_id)->delete(); 
        return $this->collectionUsers($collection_id);
    }

    public function getTitleFilteredDocuments($request, $documents){
        //$title_filter = Session::get('title_filter');
        $title_filter = empty(Session::get('title_filter'))?$request->title_filter:Session::get('title_filter');
		if(!empty($request->title_filter)){
                        $documents = $documents->where('title','like','%'.$request->title_filter.'%');
                }
		else if(!empty($title_filter[$request->collection_id])){
			$documents = $documents->where('title','like','%'.$title_filter[$request->collection_id].'%');
		}
		return $documents;
	}

	public function getMetaFilters($request){
		// check if meta filters are present in the query
		$query_params = $request->query();
		$meta_filters_query = array();
		foreach($query_params as $p=>$v){
			if(preg_match('/^meta_(\d*)/', $p, $matches)){
				// currently, no support for operator in the query string parameters
				// default operator is '='
				$meta_filters_query[] = array('field_id'=>$matches[1], 'operator'=>'=', 'value'=>$v);
			}
		}
		$meta_filters = array();
		if(count($meta_filters_query)>0){
			$meta_filters = $meta_filters_query;
		}
		else{
			// else take from the session
        	$all_meta_filters = Session::get('meta_filters');
        	$meta_filters = empty($all_meta_filters[$request->collection_id])?[]:$all_meta_filters[$request->collection_id];
		}
		return $meta_filters;
	}

    public function getMetaFilteredDocuments($request, $documents){
		// check if meta filters are present in the query
		$query_params = $request->query();
		$meta_filters_query = array();
		foreach($query_params as $p=>$v){
			if(preg_match('/^meta_(\d*)/', $p, $matches)){
				// currently, no support for operator in the query string parameters
				// default operator is '='
				// find type of meta field
				$meta_field = \App\MetaField::where('id', $matches[1])->first();
				if($meta_field && $meta_field->type == 'Numeric' && is_array($v) && count($v)==2){ 
					// this is for range filters (numeric values). This condition needs to be refined.
					$meta_filters_query[] = array('field_id'=>$matches[1], 'operator'=>'>=', 'value'=>$v[0]);
					$meta_filters_query[] = array('field_id'=>$matches[1], 'operator'=>'<=', 'value'=>$v[1]);
				}
				else{
					$meta_filters_query[] = array('field_id'=>$matches[1], 'operator'=>'=', 'value'=>$v);
				}
			}
		}
		$meta_filters = array();
		if(count($meta_filters_query)>0){
			$meta_filters = $meta_filters_query;
		}
		else{
			// else take from the session
        	$all_meta_filters = Session::get('meta_filters');
        	$meta_filters = empty($all_meta_filters[$request->collection_id])?[]:$all_meta_filters[$request->collection_id];
		}
        foreach($meta_filters as $mf){
			if(!preg_match('/^\d*$/',$mf['field_id'])){// this is for default filteres like created_at, created_by
				if($mf['field_id'] == 'created_at'){
					$documents = $documents->where('created_at', $mf['operator'], $mf['value']);
				}	
			continue;// no need to proceed further
			}

            if($mf['operator'] == '='){
				//echo '--'.$mf['field_id'].'--'.$mf['value'].'--'; exit;
				if(is_array($mf['value'])){ 
					// this is for array of values passed through the query string 
					// e.g. &meta_10[]=somevalue&meta_10[]=someothervalue
					//print_r($mf['value']);exit;
					foreach($mf['value'] as $v){
                		$documents = $documents->whereHas('meta', function (Builder $query) use($mf, $v){
        					$query->where('meta_field_id',$mf['field_id'])->where('value', 'like', '%"'.$v.'"%');
                    	});
					}
				}
				else{
                	$documents = $documents->whereHas('meta', function (Builder $query) use($mf){
                    	    $query->where('meta_field_id',$mf['field_id'])->where('value', $mf['value']);
                    	}
                	);
				}
            }
            else if($mf['operator'] == '>='){
                $documents = $documents->whereHas('meta', function (Builder $query) use($mf){
                        $query->where('meta_field_id',$mf['field_id'])->where('value', '>=', $mf['value']);
                    }
                );
            }
            else if($mf['operator'] == '<='){
                $documents = $documents->whereHas('meta', function (Builder $query) use($mf){
                        $query->where('meta_field_id',$mf['field_id'])->where('value', '<=', $mf['value']);
                    }
                );
            }
            else if($mf['operator'] == 'contains'){
                $documents = $documents->whereHas('meta', function (Builder $query) use($mf){
                        $query->where('meta_field_id',$mf['field_id'])->where('value', 'like', '%'.$mf['value'].'%');
                    }
                );
            }
        }
        return $documents;
    }

    // wrapper function for search
    public function search(Request $request){
        if(!empty(env('SEARCH_MODE')) && env('SEARCH_MODE') == 'elastic'){
            $search_results = $this->searchElastic($request);
        }
        else{
            $search_results = $this->searchDB($request); 
        }

        // log search query
		$old_query = Session::get('search_query');

		if(!empty($request->search['value']) && $old_query != $request->search['value'] 
			&& !$request->is('api/*') && strlen($request->search['value'])>3){
			Session::put('search_query', $request->search['value']);
			$meta_query = json_encode($this->getMetaFilters($request));
        	$search_log_data = array('collection_id'=> $request->collection_id, 
                'user_id'=> empty(\Auth::user()->id) ? null : \Auth::user()->id,
                'search_query'=> $request->search['value'], 
                'meta_query'=> $meta_query,
				'ip_address' => $request->ip(),
                'results'=>$search_results['recordsFiltered']);
	    	if(!empty($request->collection_id)){
            	$this->logSearchQuery($search_log_data);
	    	}
        }

        return json_encode($search_results, JSON_UNESCAPED_UNICODE);
    }

	public function getElasticClient(){
        $elastic_hosts = env('ELASTIC_SEARCH_HOSTS', 'localhost:9200');
        $hosts = explode(",",$elastic_hosts);
	return ClientBuilder::create()->setHosts($hosts)
	->setBasicAuthentication('elastic', env('ELASTIC_PASSWORD','some-default-password'))
        ->setCABundle('/etc/elasticsearch/certs/http_ca.crt')
	->build();
	}
    // elastic search
    public function searchElastic($request){
        $params = array();
        /*
        $params = [
            'index' => 'sr_documents',
            'body'  => [
                'query' => [
                    'bool'=>[
                        'filter' => [
                            'term'=> ['collection_id' => $request->collection_id]
                        ]
                    ]
                ]
            ]
        ];
        */
	if(!empty($request->collection_id)){
		$collection = \App\Collection::find($request->collection_id);
		if($collection->content_type == 'Uploaded documents'){
        	$elastic_index = 'sr_documents';
        	$documents = \App\Document::where('collection_id', $request->collection_id);
			if($collection->require_approval){
				$documents = $documents->whereNotNull('approved_on');
			}
			if(\Auth::user() && !\Auth::user()->hasPermission($request->collection_id, 'VIEW')){
				// user can not view any document; just their own
				$documents = $documents->where('created_by', \Auth::user()->id);
			}
		}
		else{
        	$elastic_index = 'sr_urls';
        	$documents = \App\Url::where('collection_id', $request->collection_id);
		}
	}
	else {
		// search all documents
		$collections = $this->userCollections();
		$collection_ids = array();
		foreach($collections as $c){
			$collection_ids[] = $c->id;
		}
		$collection_type = $request->collection_type;
		if($collection_type == 'Web resources'){
        		$documents = \App\Url::whereIn('collection_id', $collection_ids);
        		$elastic_index = 'sr_urls';
		}
		else{
        		$documents = \App\Document::whereIn('collection_id', $collection_ids);
				$documents = $documents->whereNotNull('approved_on');
				if(\Auth::user()->id){
					$documents = $documents->orWhere('created_by', \Auth::user()->id);
				}
        		$elastic_index = 'sr_documents';
		}
	}
        $total_count = $documents->count();

        if(!empty($request->search['value']) && strlen($request->search['value'])>3){
            $search_term = $request->search['value'];
            $words = explode(' ',$search_term);
			$search_mode = empty($request->search_mode)?'default':$request->search_mode;

			$synonym_search = 1; // put synonym search on if the value in session is ON
			$analyzer = ($synonym_search)?'synonyms_analyzer':null;
			
				$title_q_with_and = ['query'=>$search_term, 'operator'=>'and', 'boost'=>4, 'analyzer'=>$analyzer];
				$text_q_with_and = ['query'=>$search_term, 'operator'=>'and', 'boost'=>2, 'analyzer'=>$analyzer];
				$q_title_phrase = ['query'=>$search_term, 'boost'=>6, 'analyzer'=>$analyzer];
				$q_text_phrase = ['query'=>$search_term, 'boost'=>3, 'analyzer'=>$analyzer];
				$q_without_and = ['query'=>$search_term, 'analyzer'=>$analyzer];

				$params = [
					'index' => 'sr_documents',
					'body' => [
						'query' => [
							'bool' => [
								'should' => [
									[
										'match' => [
											'title' => $q_without_and,
										]
									],
									[
										'match' => [
											'text_content' => $q_without_and
										]
									],
									[
										'match' => [
											'title' => $title_q_with_and,
										]
									],
									[
										'match' => [
											'text_content' => $text_q_with_and
										]
									],
									[
										'match_phrase' => [
											'title' => $q_title_phrase,
										]
									],
									[
										'match_phrase' => [
											'text_content' => $q_text_phrase
										]
									],
								],
								'minimum_should_match' => 3
							],
						]
					]
				];
	    	if(!empty($request->collection_id)){
            	$params['body']['query']['bool']['must']['term']['collection_id']=$request->collection_id;
			}
			/*
            foreach($words as $w){
                $params['body']['query']['bool']['should'][]['wildcard']['title']=$w.'*';
                $params['body']['query']['bool']['should'][]['wildcard']['text_content']=$w.'*';
            }
            	$params['body']['query']['bool']['minimum_should_match']= count($words);
            	$params['body']['query']['bool']['boost']= 1.0;
			*/
            	//$params['body']['sort']= ['_score'];
				
			/*
                $params['body']['query']['combined_fields']['query']= $search_term;
                $params['body']['query']['combined_fields']['operator']= 'and';
                $params['body']['query']['combined_fields']['fields']= ['title','text_content'];
			*/
        }
        $columns = array('type', 'title', 'size', 'updated_at');
        if(!empty($params)){
	    $params['index'] = $elastic_index;
	    $params['size'] = 1000;// set a max size returned by ES
		try{
			$client = $this->getElasticClient();
            $response = $client->search($params);
		}
		catch(\Exception $e){
			// some error; switch to db search
			return $this->searchDB($request);
		}
            $document_ids = array();
            foreach($response['hits']['hits'] as $h){
                $document_ids[] = $h['_id'];
            }
            $documents = $documents->whereIn('id', $document_ids);
			$ordered_document_ids = implode(",", $document_ids);
        }
        // get title filtered documents
		if(!empty(Session::get('title_filter')) || !empty($request->title_filter)){
            $documents = $this->getTitleFilteredDocuments($request, $documents);
		}
        // get Meta filtered documents
        //$all_meta_filters = Session::get('meta_filters');
        //if(!empty($all_meta_filters[$request->collection_id])){
            $documents = $this->getMetaFilteredDocuments($request, $documents);
        //}

	// get approval exception 
	// the exceptions will be removed from the models with ->whereNotIn 
	//$approval_exceptions = $this->getApprovalExceptions($request, $documents);
	//$documents = $this->approvalFilter($request->collection_id, $documents);
	//$documents = $documents->get();
	$filtered_count = $documents->count(); 

	$sort_column = empty($columns[@$request->order[0]['column']])?'':$columns[@$request->order[0]['column']];
	$sort_direction = @empty($request->order[0]['dir'])?'desc':$request->order[0]['dir'];
	$length = empty($request->length)?10:$request->length;
	if(!empty($params) && !empty($ordered_document_ids) && empty($sort_column)){
	// initial sorting is by relevance
	$documents = $documents
		->orderByRaw("FIELD(id, $ordered_document_ids)")
             ->limit($length)->offset($request->start)->get();
	}
	else{
		$sort_column = empty($sort_column)?'updated_at':$sort_column;
		$documents = $documents
			->orderby($sort_column,$sort_direction)
        	->limit($length)->offset($request->start)->get();
	}

	$has_approval = \App\Collection::where('id','=',$request->collection_id)
		->where('require_approval','=','1')->get();

		if($request->is('api/*')){
			return 
        	 array(
            'data'=>$documents,
            'draw'=>(int) $request->draw,
            'recordsTotal'=> $total_count,
            'recordsFiltered' => $filtered_count,
            'error'=> '',
        	);
		}
		else{
        	$results_data = $this->datatableFormatResults(
               array('request'=>$request, 'documents'=>$documents, 'has_approval'=>$has_approval)
       		);
		}
        $results= array(
            'data'=>$results_data,
            'draw'=>(int) $request->draw,
            'recordsTotal'=> $total_count,
            'recordsFiltered' => $filtered_count,
            'error'=> '',
        );
        //return json_encode($results, JSON_UNESCAPED_UNICODE);
		return $results;
    }

    // db search (default)
    public function searchDB(Request $request){
	if(!empty($request->collection_id)){
		$collection = \App\Collection::find($request->collection_id);
		if($collection->content_type == 'Uploaded documents'){
        	$documents = \App\Document::where('collection_id', $request->collection_id);
			if($collection->require_approval){
        		$documents = $documents->whereNotNull('approved_on');
			}
			if(\Auth::user() && !\Auth::user()->hasPermission($request->collection_id, 'VIEW')){
				// user can not view any document; just their own
				$documents = $documents->where('created_by', \Auth::user()->id);
			}
		}
		else{
        	$documents = \App\Url::where('collection_id', $request->collection_id);
		}
	}
	else {
		// search all documents
		$collections = $this->userCollections();
		$collection_ids = array();
		foreach($collections as $c){
			$collection_ids[] = $c->id;
		}
		$collection_type = $request->collection_type;
		if($collection_type == 'Web resources'){
        		$documents = \App\Url::whereIn('collection_id', $collection_ids);
        		$elastic_index = 'sr_urls';
		}
		else{
        		$documents = \App\Document::whereIn('collection_id', $collection_ids);
				if(!empty(\Auth::user()->id)){
					$documents = $documents->orWhere('created_by', \Auth::user()->id);
				}
        		$elastic_index = 'sr_documents';
		}
	}
        $columns = array('type', 'title', 'size', 'updated_at');
	$has_approval = \App\Collection::where('id','=',$request->collection_id)->where('require_approval','=','1')->get();
	//
        // total number of viewable records
        $total_documents = $documents->count(); 

        // get title filtered documents
		if(!empty(Session::get('title_filter')) || !empty($request->title_filter)){
            $documents = $this->getTitleFilteredDocuments($request, $documents);
		}
        // get Meta filtered documents
        //$all_meta_filters = Session::get('meta_filters');
        //if(!empty($all_meta_filters[$request->collection_id])){
            $documents = $this->getMetaFilteredDocuments($request, $documents);
        //}

        // content search
        if(!empty($request->search['value']) && strlen($request->search['value'])>3){
            $documents = $documents->search($request->search['value']);
        }
	// get approval exception 
	// the exceptions will be removed from the models with ->whereNotIn 
	//$approval_exceptions = $this->getApprovalExceptions($request, $documents);
	$documents = $this->approvalFilter($request->collection_id, $documents);
	$filtered_count = $documents->count(); //- count($approval_exceptions);

    if(!empty($request->embedded)){ 		
		$documents = $documents
		->limit($request->length)->offset($request->start)->get();
       	$results_data = $this->datatableFormatResultsEmbedded(
		array('request'=>$request, 
		'documents'=>$documents, 
		'has_approval'=>$has_approval));
	}
	else{
		$sort_column = @empty($columns[$request->order[0]['column']])?'':$columns[$request->order[0]['column']];
		$sort_direction = @empty($request->order[0]['dir'])?'desc':$request->order[0]['dir'];
		$length = empty($request->length)?10:$request->length;
		if(!empty($sort_column)){
		$documents = $documents
			->orderBy($sort_column,$sort_direction)
   	        ->limit($length)->offset($request->start)->get();
		}
		else{// initial sorting is by relevance (or whatever order the database returns)
		$documents = $documents
   	        ->limit($length)->offset($request->start)->get();
		}
		if($request->is('api/*')){
			return 
        		array(
            	    	'data'=>$documents,
            	    	'draw'=>(int) $request->draw,
            	    	'recordsTotal'=> \App\Document::where('collection_id',$request->collection_id)->count(),
            	    	'recordsFiltered' => $filtered_count,
            	    	'error'=> '',
        		);
		}
		else{
        	$results_data = $this->datatableFormatResults(
				array('request'=>$request, 
				'documents'=>$documents, 
				'has_approval'=>$has_approval));
		}
	}
        
        $results= array(
            'data'=>$results_data,
            'draw'=>(int) $request->draw,
            'recordsTotal'=> $total_documents,
            'recordsFiltered' => $filtered_count,
            'error'=> '',
        );

        //return json_encode($results, JSON_UNESCAPED_UNICODE);
		return $results;
    }

    private function approvalFilter($collection_id, $documents){
		if(empty($collection_id)){ // this is a search within all documents
			// approved where approval is needed
			$documents = $documents
			->where(function($query){
				$query->where(function ($Q){
					$Q->whereNotNull('approved_on')
					->whereHas('collection',function ($q){
							$q->where('require_approval',1);
					});
				});
				$query->orWhere(function($Q){
					$Q->whereHas('collection', function($q){
						$q->where('require_approval', 0)
						->orWhereNull('require_approval');
					});
				});
			});
			return $documents;
		}
		$collection = Collection::find($collection_id);
		if($collection->content_type == 'Web resources'){
			// all
			return $documents;
		}
		else if($collection->content_type == 'Uploaded documents'){
			if($collection->require_approval == 1){ 
				/*
				if(Auth::user() && Auth::user()->hasPermission($collection->id, 'APPROVE')){ // return all
					return $documents;
				}
				else{ // return only approvedSKK
				*/
					$documents = $documents->whereNotNull('approved_on');	
					return $documents;
				//}
			}
			else{ 
				return $documents;
			}
		}
		/*
	    $doc_col = $documents->get();
	    $filtered_docs = $doc_col->filter(function($d, $key){
		    if($d->collection->require_approval == 1 && 
			    !Auth::user()->hasPermission($d->collection->id, 'APPROVE') &&
		    	    empty($d->approved_on)){
			    return true;
		    }
	    });
	    $filtered_ids = array();
	    foreach($filtered_docs as $d){
		    $filtered_ids[] = $d->id;
	    }
	    return $filtered_ids;
		*/
    }

    private function datatableFormatResults($data){
        $documents = $data['documents'];
        $request = $data['request'];
        $has_approval = $data['has_approval'];

		if(!empty($request->collection_id)){
			$collection = \App\Collection::find($request->collection_id);
			$content_type = $collection->content_type;
			$column_config = json_decode($collection->column_config);	
		}
		else{
			// default content type 
			$content_type = 'Uploaded documents';
		}

        $results_data = array();
        foreach($documents as $d){
            $action_icons = '';

	    if($content_type == 'Uploaded documents'){
            	$revisions = $d->revisions;
            	$r_count = count($revisions);
            	if($r_count > 1){
               		$filter_count = ($r_count > 9) ? '' : '_'.$r_count;
                	$action_icons .= '<a class="btn btn-primary btn-link" href="/document/'.$d->id.'/revisions" title="'.$r_count.' revisions"><i class="material-icons">filter'.$filter_count.'</i></a>';
            	}
		if($d->type == 'application/pdf'){
			$action_icons .= '<a class="btn btn-primary btn-link" title="Read online" href="/collection/'.$d->collection_id.'/document/'.$d->id.'/pdf-reader" target="_blank"><i class="material-icons">open_in_browser</i></a>';
		}
		else if(preg_match('/^audio/',$d->type) || preg_match('/^video/',$d->type)){
			$action_icons .= '<a class="btn btn-primary btn-link" title="Play" href="/collection/'.$d->collection_id.'/document/'.$d->id.'/media-player" target="_blank"><i class="material-icons">play_arrow</i></a>';
		}
		else{
			$action_icons .= '<a class="btn btn-primary btn-link" title="Download" href="/collection/'.$d->collection_id.'/document/'.$d->id.'" target="_blank"><i class="material-icons">cloud_download</i></a>';
		}
	    }
  	    else if ($content_type == 'Web resources'){		
		$action_icons .= '<a class="btn btn-primary btn-link" href="'.$d->url.'" target="_blank"><i class="material-icons">link</i></a>';
	    }

		if(env('ENABLE_INFO_PAGE') == 1){
	    $action_icons .= '<a class="btn btn-primary btn-link" title="Information and more" href="/collection/'.$d->collection_id.'/document/'.$d->id.'/details"><i class="material-icons">info</i></a>';
		}
	    if($content_type == 'Uploaded documents'){
            if(Auth::user()){
                if(Auth::user()->canApproveDocument($d->id,Auth::user()->userrole(Auth::user()->id)) && !$has_approval->isEmpty()){
			//if(!empty($d->approved_on)){
			if(!empty($d->approval()->approval_status)){
                //$action_icons .= '<a class="btn btn-primary btn-link" href="/document/'.$d->id.'/approval" title="UnApprove document"><i class="material-icons">done</i></a>';
			}
			else{
                //$action_icons .= '<a class="btn btn-primary btn-link" href="/document/'.$d->id.'/approval" title="Approve document"><i class="material-icons">close</i></a>';
			}
		}
                if(Auth::user()->canEditDocument($d->id)){
                $action_icons .= '<a class="btn btn-success btn-link" href="/document/'.$d->id.'/edit" title="Create a new revision"><i class="material-icons">edit</i></a>';
                }
                if(Auth::user()->canDeleteDocument($d->id)){
                $action_icons .= '<span class="btn btn-danger btn-link confirmdelete" onclick="showDeleteDialog('.$d->id.');" title="Delete document"><i class="material-icons">delete</i></span>';
                }
            }
	    } // if collection's content-type == Uploaded documents
	    //$title = $d->title.': '. substr($d->text_content, 0, 100).' ...';
	    $title = $d->title;
	    $title = mb_convert_encoding($title, 'UTF-8', 'UTF-8');
	    //$title = $d->title;
        $result = array(
                //'type' => array('display'=>'<a href="/collection/'.$d->collection_id.'/document/'.$d->id.'/details"><img class="file-icon" src="/i/file-types/'.$d->icon().'.png" /></a>', 'filetype'=>$d->icon()),
                'type' => array('display'=>'<img class="file-icon" src="/i/file-types/'.$d->icon().'.png" />', 'filetype'=>$d->icon()),
                'title' => $title,
                'size' => array('display'=>$d->human_filesize(), 'bytes'=>$d->size),
                //'updated_at' => array('display'=>date('d-m-Y', strtotime($d->updated_at)), 'updated_date'=>$d->updated_at),
                'updated_at' => array('display'=>date('Y-M-d', strtotime($d->updated_at)), 'updated_date'=>$d->updated_at),
                'actions' => $action_icons
			);
		if(!empty($collection)){
			foreach($collection->meta_fields as $m){
			$column_config_meta_fields = empty($column_config->meta_fields)?[]:$column_config->meta_fields;
			if(!in_array($m->id, $column_config_meta_fields)) continue;
				if(is_array(json_decode($d->meta_value($m->id)))){ // applies to fields of type Select and MultiSelect
					$result['meta_'.$m->id] = implode(",",json_decode($d->meta_value($m->id)));
				}
				else{
					if($m->type == 'Date' && !empty($d->meta_value($m->id))){
						$date = strtotime($d->meta_value($m->id));
						$result['meta_'.$m->id] = date("Y-M-d",$date);
					}
					else{
						$result['meta_'.$m->id] = $d->meta_value($m->id);
					}
				}
			}
		}
		$results_data[] = $result;
		} // foreach ends
        return $results_data;
    }

    public function addMetaFilter(Request $request){
        // set filters in session and return to the collection view 
        $meta_filters = Session::get('meta_filters');
        if(!empty($request->meta_value)){
            $meta_filters[$request->collection_id][] = array(
                'filter_id'=>\Uuid::generate()->string,
                'field_id'=>$request->meta_field,
                'operator'=>$request->operator,
                'value'=>$request->meta_value
            );
        }
        Session::put('meta_filters', $meta_filters);
        return redirect('/collection/'.$request->collection_id.'/metafilters');
    }

    public function replaceMetaFilter(Request $request){
/*
echo "Meta Value: ";print_r($request->meta_value); echo "<hr />";
echo "Meta Fields: "; print_r($request->meta_field); echo "<hr />";
echo "Meta Type: ";print_r($request->meta_type); echo "<hr />";
echo "Meta Operator: ";print_r($request->operator); echo "<hr />";
$j=0;
foreach($request->meta_field as $field){
foreach($request->meta_value as $key=>$value){
	echo "Key= ".$key."<br />";
		echo $j."<br />";
		if(count($value) > 0 && !empty($value[0])){
		print_r($request->meta_value[$key]);
		}
		echo "<br />";
		if($request->meta_field[$j] == $key && !empty($value[0])){
		echo "Meta Field: ".$request->meta_field[$j]."<br />";
		echo "Meta Type: ".$request->meta_type[$j]."<br />";
		echo "Operator: ".$request->operator[$j]."<br />";
		for($i=0;$i<count($value);$i++){
			echo $value[$i]."<br />";
		}
		}
}
	$j++;
	echo "<hr />";
}
exit;
*/
        // set filters in session and return to the collection view 
        $meta_filters = Session::get('meta_filters');
	$new_meta_filters = array();
	$multi_meta_field = $multi_meta_value = array();

        if(!empty($request->meta_value)){
			if($meta_filters && is_array($meta_filters[$request->collection_id])){
			foreach($meta_filters[$request->collection_id] as $m){
				if($m['field_id'] != $request->meta_field){
					$new_meta_filters[$request->collection_id][] = $m;
				}
			}
			}
			/*
			if($request->operator == 'between'){
				$range_parts = explode(' to ',ltrim(rtrim($request->meta_value)));
            			$new_meta_filters[$request->collection_id][] = array(
                			'filter_id'=>\Uuid::generate()->string,
                			'field_id'=>$request->meta_field,
		                	'operator'=>'>=',
		                	'value'=> $range_parts[0]
            			);
		            	$new_meta_filters[$request->collection_id][] = array(
               		 	'filter_id'=>\Uuid::generate()->string,
                			'field_id'=>$request->meta_field,
		                	'operator'=>'<=',
		                	'value'=> $range_parts[1]
            			);
			}
			else{
			*/
$j=0;
foreach($request->meta_field as $field){
foreach($request->meta_value as $key=>$value){
	if($request->meta_field[$j] == $key && !empty($value[0])){
	    	for($i=0;$i<count($value);$i++){
		if($request->operator[$j] == 'between'){
				$range_parts = explode(' to ',ltrim(rtrim($value[$i])));
				/*
				if($range_parts[0] == $range_parts[1]){
            			$new_meta_filters[$request->collection_id][] = array(
                			'filter_id'=>\Uuid::generate()->string,
                			'field_id'=>$request->meta_field[$j],
		                	'operator'=>'==',
		                	'value'=> $range_parts[0]
            			);
				}	
				else{
				*/
            			$new_meta_filters[$request->collection_id][] = array(
                			'filter_id'=>\Uuid::generate()->string,
                			'field_id'=>$request->meta_field[$j],
		                	'operator'=>'>=',
		                	'value'=> $range_parts[0]
            			);
		            	$new_meta_filters[$request->collection_id][] = array(
               		 	'filter_id'=>\Uuid::generate()->string,
                			'field_id'=>$request->meta_field[$j],
		                	'operator'=>'<=',
		                	'value'=> $range_parts[1]
            			);
				//}	
			}
		else{
	    		//for($i=0;$i<count($value);$i++){
				$new_meta_filters[$request->collection_id][] = array(
                     		'filter_id'=>\Uuid::generate()->string,
                     		'field_id'=>$request->meta_field[$j],
                     		'operator'=>$request->operator[$j],
                     		'value'=>$value[$i]
                 		);
	    		//}
		}##else for 'contains or matches' ends
	    	}
	}
} // foreach $request->meta_value ends
$j++;
} // foreach $request->meta_fields ends
//exit;
        }
	//print_r($new_meta_filters); exit;
        Session::put('meta_filters', $new_meta_filters);
        return redirect('/collection/'.$request->collection_id);
    }

	public function replaceTitleFilter(Request $request){
		$title_filter = Session::get('title_filter');
		$title_filter[$request->collection_id] = $request->title_filter;
		Session::put('title_filter', $title_filter);
        return redirect('/collection/'.$request->collection_id);
	}
    
    public function metaInformation($collection_id, $meta_field_id=null){
        $collection = \App\Collection::find($collection_id);
        if(empty($meta_field_id)){
            $edit_field = new \App\MetaField;
        }
        else{
            $edit_field = \App\MetaField::find($meta_field_id);
        }
        $meta_fields = $collection->meta_fields;
	$permissions = \App\Permission::all();
        return view('metainformation', ['collection'=>$collection, 'permissions'=>$permissions,
                'edit_field'=>$edit_field, 
                'meta_fields'=>$meta_fields,
		'activePage' =>'Collections Meta Data',
		'titlePage'=>'Collections Metadata Fields']);
    }

    public function saveMeta(Request $request){
        $collection = \App\Collection::find($request->input('collection_id'));
        if(empty($request->input('meta_field_id'))){
            $meta_field = new \App\MetaField;
        }
        else{
            $meta_field = \App\MetaField::find($request->input('meta_field_id'));
        }
        $meta_field->collection_id = $request->input('collection_id');
        $meta_field->label = $request->input('label');
        $meta_field->placeholder = $request->input('placeholder');
	if(!empty($request->input('available_to'))){
	$meta_field->available_to = implode(",",$request->input('available_to'));
	}
        $meta_field->type = $request->input('type');
	if($request->type == 'TaxonomyTree'){
        	$meta_field->options = $request->input('treeoptions');
	}
	else{
        	$meta_field->options = $request->input('options');
	}
        $meta_field->display_order = $request->input('display_order');
        $meta_field->is_required = $request->input('is_required');
        $meta_field->save();
        return $this->metaInformation($request->input('collection_id'));
    }

    public function deleteMetaField($collection_id,$meta_field_id){
        $meta_field = \App\MetaField::find($meta_field_id);
        $collection_id = $meta_field->collection_id;
        $meta_field->delete();
        return redirect('/collection/'.$collection_id.'/meta');
    }

    public function metaFiltersForm($collection_id){
        $collection = \App\Collection::find($collection_id);
        return view('metasearch', ['collection'=>$collection, 
            'activePage'=>'Set Meta Filters',
            //'titlePage'=>'Set Meta Filters',
            'title'=>'Smart Repository'
            ]
            );
    }
    
    public function removeMetaFilter($collection_id, $filter_id){
        $all_meta_filters = Session::get('meta_filters');
        $new_collection_filters = array();
        foreach($all_meta_filters[$collection_id] as $mf){
            if($mf['filter_id'] == $filter_id) continue;
            $new_collection_filters[] = $mf;
        }
        $all_meta_filters[$collection_id] = $new_collection_filters;
        Session::put('meta_filters', $all_meta_filters);
        return redirect('/collection/'.$collection_id);
    }

	public function removeTitleFilter($collection_id){
		$title_filter = Session::get('title_filter');
		$title_filter[$collection_id] = null;
        Session::put('title_filter', $title_filter);
        return redirect('/collection/'.$collection_id);
	}

    public function removeAllMetaFilters($collection_id){
        $all_meta_filters = Session::get('meta_filters');
        $all_meta_filters[$collection_id] = null;
        Session::put('meta_filters', $all_meta_filters);
        return redirect('/collection/'.$collection_id);
    }

	public function removeAllFilters($collection_id){
		$title_filter = Session::get('title_filter');
		$title_filter[$collection_id] = null;
        Session::put('title_filter', $title_filter);
        $all_meta_filters = Session::get('meta_filters');
        $all_meta_filters[$collection_id] = null;
        Session::put('meta_filters', $all_meta_filters);
        return redirect('/collection/'.$collection_id);
	}

    public function logSearchQuery($data){
		$meta_query = [];
		foreach(json_decode($data['meta_query']) as $m){
			unset($m->filter_id);
			$meta_query[] = $m;
		}
		try{
        $search_log_entry = new \App\Searches;
        $search_log_entry->collection_id = $data['collection_id']; 
        $search_log_entry->meta_query = json_encode($meta_query); 
        $search_log_entry->search_query = $data['search_query']; 
        $search_log_entry->user_id = $data['user_id']; 
        $search_log_entry->ip_address = $data['ip_address']; 
        $search_log_entry->results = $data['results']; 
        $search_log_entry->save();
		}
		catch(\Exception $e){
			print $e->getMessage();
			exit;
		}
    }

    public function deleteCollection(Request $request){
        $collection = \App\Collection::find($request->collection_id);

    	if ($collection != null) {
	if(!empty($request->delete_captcha) &&
                $request->delete_captcha == $request->delete_captcha){
       	 	if($collection->delete()){
            	Session::flash('alert-success', 'Collection deleted successfully!');
       	 	return redirect('/admin/collectionmanagement');
		}
        }
        else{
                Session::flash('alert-danger', 'Please fill Captcha');
                return redirect('/admin/collectionmanagement');
        }
    	}

    }

    private function datatableFormatResultsEmbedded($data){
        $documents = $data['documents'];
        $request = $data['request'];

	$collection = \App\Collection::find($request->collection_id);

        $results_data = array();

        foreach($documents as $d){
	    $title = $d->title.'<br />'. substr($d->text_content, 0, 100).' ...';
            $results_data[] = array(
                //'type' => array('display'=>'<a href="/collection/'.$request->collection_id.'/document/'.$d->id.'/details"><img class="file-icon" src="'.env('APP_URL').'/i/file-types/'.$d->icon().'.png" style="width:20px;"/></a>', 'filetype'=>$d->icon()),
                'type' => array('display'=>'<img class="file-icon" src="'.env('APP_URL').'/i/file-types/'.$d->icon().'.png" style="width:20px;"/>', 'filetype'=>$d->icon()),
                'title' => '<a href="'.env('APP_URL').'/collection/'.$request->collection_id.'/document/'.$d->id.'/details" target="_blank">'.$title.'</a>',
                'size' => array('display'=>$d->human_filesize(), 'bytes'=>$d->size),
                'updated_at' => array('display'=>date('d-m-Y', strtotime($d->updated_at)), 'updated_date'=>$d->updated_at),
                );
        }
        return $results_data;
    }

    public function collectionUrls($collection_id){
        if($collection_id == 'new'){
            $collection = new \App\Collection();
			$spidered_domains = null;
			$desired = null;
			$excluded = null;
        }
        else{
            $collection = \App\Collection::find($collection_id);
			$spidered_domains = \App\SpideredDomain::where('collection_id', $collection_id)->get();
			$desired = \App\DesiredUrl::where('collection_id', $collection_id)->get();
			$excluded = \App\UrlSuppression::where('collection_id', $collection_id)->get();
        }
        return view('save_exclude_sites', 
			['collection'=>$collection,
			'activePage'=>'Collection', 
			'titlePage'=>'Collection',
			'spidered_domains'=>$spidered_domains,
			'desired'=>$desired,
			'excluded'=>$excluded,
			]);
    }

    public function saveCollectionUrls(Request $request){
	$domain_link = $request->spidered_domain;
	$existing_domains = array();
	$sd = SpideredDomain::all();
	foreach($sd as $domain){
		$existing_domains[] = $domain->web_address;
	}		
	if(!in_array($domain_link,$existing_domains)){
		$sd = new \App\SpideredDomain;
		$sd->collection_id = $request->input('collection_id');
		$sd->web_address = $domain_link;
       	try{
			if(!empty($domain_link)){
		   		$sd->save();
				$domain_id = $sd->id;
			}
			if(!empty($request->input('save_urls'))){
				$this->saveDesiredUrls($request);		
			}
			if(!empty($request->input('exclude_urls'))){
				$this->excludeUrls($request);		
			}
           	Session::flash('alert-success', 'Site URLs saved successfully!');
           	return redirect('/collection/'.$request->collection_id.'/save_exclude_sites');
        }
       	catch(\Exception $e){
           	Session::flash('alert-danger', $e->getMessage());
           	return redirect('/collection/'.$request->collection_id.'/save_exclude_sites');
       	}
	} ##if ends for existing domains check
	else{
            	Session::flash('alert-danger', 'Domain already spidered.');
            	return redirect('/collection/'.$request->collection_id.'/save_exclude_sites');
	}
/*
use App\SpideredDomain;
use App\DesiredUrl;
use App\UrlSuppression;
*/
    }

    public function saveDesiredUrls($request){
	$url_start_patterns = explode("\n",$request->input('save_urls'));
	$collection_id = $request->input('collection_id');
#DB::enableQueryLog();
	foreach($url_start_patterns as $url){
		if(empty($url)) continue;
		$su = new \App\DesiredUrl;	
		$su->collection_id = $collection_id;
		$su->url_start_pattern = rtrim(ltrim($url));
    	$su->save();
	}
    }	

    public function excludeUrls($request){
	$url_start_patterns = explode("\n",$request->input('exclude_urls'));
	$collection_id = $request->input('collection_id');
	foreach($url_start_patterns as $url){
		if(empty($url)) continue;
		$su = new \App\UrlSuppression;
		$su->collection_id = $collection_id;
		$su->url_start_pattern = rtrim(ltrim($url));
    	$su->save();
	}
    }	

	public function removeSpideredDomain(Request $request){
		\App\SpideredDomain::find($request->domain_id)->delete();
		return redirect('/collection/'.$request->collection_id.'/save_exclude_sites');
	}
	public function removeDesiredLink(Request $request){
		\App\DesiredUrl::find($request->link_id)->delete();
		return redirect('/collection/'.$request->collection_id.'/save_exclude_sites');
	}
	public function removeExcludedLink(Request $request){
		\App\UrlSuppression::find($request->link_id)->delete();
		return redirect('/collection/'.$request->collection_id.'/save_exclude_sites');
	}

	// column-config
	public function showSettingsForm(Request $request){
		$collection = Collection::find($request->collection_id);
		$roles = Role::all();
        return view('collection-settings', ['collection'=>$collection, 'roles'=>$roles,
			'mailbox'=>CollectionMailbox::where('collection_id', $collection->id)->first()]);
	}
	
	public function saveSettings(Request $request){
		$collection = Collection::find($request->collection_id);
		$col_config = $request->all();
		$collection->column_config = json_encode($col_config);
		$collection->save();
		// configuration of mapping of mailbox
		$mailbox = CollectionMailbox::where('collection_id', $collection->id)->first();
		if(empty($mailbox)){
			$mailbox = new CollectionMailbox;
		}
		if(!empty($request->input('email_address'))){
			$cred_array = array(
				'server_type'=>'IMAP',
				'server_address'=>$request->input('imap_server'),
				'server_port'=>$request->input('server_port'),
				'security'=>$request->input('security'),
				'username'=>$request->input('username'),
				'password'=>$request->input('password'),
			);
			$mailbox->address = $request->input('email_address');
			$mailbox->collection_id = $request->input('collection_id');
			$mailbox->credentials = json_encode($cred_array);
			$mailbox->save();
		}
		return redirect('/collection/'.$collection->id);
	}

	public function export(Request $request, $collection_id){
		$documents = \App\Document::where('collection_id', $collection_id);
		$documents = $this->getTitleFilteredDocuments($request, $documents);
		$documents = $this->getMetaFilteredDocuments($request, $documents);

		$collection = \App\Collection::find($collection_id);
		$filename = $collection->name;
		$meta_fields = $collection->meta_fields;
		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename={$filename}.tsv");
		header("Pragma: no-cache");
		header("Expires: 0");
		echo 'id'."\t".'title'."\t";
		foreach($meta_fields as $m){
			echo $m->label."\t";
		}
		echo "\n";
		$documents->chunk(10, function($documents){
		foreach($documents as $d){
			// remove tab spaces from the title, if present
			echo $d->id."\t".preg_replace("/\t/", " ", $d->title)."\t";
			$meta_fields = $d->collection->meta_fields;
			foreach($meta_fields as $m){
				if(!empty($d->meta_value($m->id))){
					echo preg_replace("/\t/"," ", $d->meta_value($m->id));
				}
				else{
					echo " - ";
				}
				echo "\t";
			}
			echo "\n";
		}
		});// chunking ends
		exit;
	}

	public function showChildCollectionForm(Request $req){
		if($req->child_collection_id == 'new'){
			$collection = new Collection;
		}
		else{
			$collection = Collection::find($req->child_collection_id);
		}
		return view('child-collection-form',['collection'=>$collection]);
	}

	public function saveChildCollection(Request $req){
		$collection = Collection::find($req->collection_id);
		$child = $collection->replicate();
		$child->name = $req->collection_name;
		$child->description = $req->description;
		$child->parent_id = $collection->id;
		$child->column_config = null;
		$child->save();
		// clone user permissions on the child collection
		$user_permissions = UserPermission::where('collection_id', $collection->id)->get();
		foreach($user_permissions as $u_p){
			$u_p_new = $u_p->replicate();
			$u_p_new->collection_id = $child->id;
			$u_p_new->save();
		}
		return redirect('/collection/'.$req->collection_id);
	}

	public function exportXlsx(Request $request, $collection_id){
		//echo $collection_id; exit;
		$list = $meta_details = [];
		$filename = '';
		$documents = \App\Document::where('collection_id', $collection_id);
		$documents = $this->getTitleFilteredDocuments($request, $documents);
		$documents = $this->getMetaFilteredDocuments($request, $documents);

		$collection = \App\Collection::find($collection_id);
		$filename = $collection->name;
		$meta_fields = $collection->meta_fields;
		$new_list  = $new_meta_details = [];
		
		$documents->chunk(10, function($documents) use (&$new_list){ // chunking starts
                foreach($documents as $d){
  			$list = ['ID'=>$d->id,'Title'=>$d->title];
			$meta_fields = $d->collection->meta_fields;
			foreach($meta_fields as $m){
				if($m->type=='MultiSelect' || $m->type == 'Select'){
					$details_select = trim($d->meta_value($m->id),'[]');
					$details_select = preg_replace('/"/',"",$details_select);
					//$meta_details = [$m->label => $d->meta_value($m->id)];
					$meta_details = [$m->label => $details_select];
				}
				else{
					//$meta_details = [$m->label => $d->meta_value($m->id)];
					$meta_details = [$m->label => html_entity_decode($d->meta_value($m->id))];
				}
				$list = array_merge($list,$meta_details);
			}
			//print_r($list);
			//echo "<hr />";
			//exit;
			$new_list[] = array_merge($list,$meta_details);
		}
		});// chunking ends
		//exit;
		return (new FastExcel($new_list))
    			->download($filename.'.xlsx');
	}

	public function autoSuggest(Request $request){
		// Suggestion are available only when search mode is elastic
		$term = $request->input('term');
		$params['index'] = 'sr_documents';
       	$params['body']['suggest']['title-suggestion']['text'] = $term;
       	$params['body']['suggest']['title-suggestion']['term']['field'] = 'title';
       	$params['body']['suggest']['content-suggestion']['text'] = $term;
       	$params['body']['suggest']['content-suggestion']['term']['field'] = 'text_content';

		try{
		$client = $this->getElasticClient();
        $response = $client->search($params);
		}
		catch(\Exception $e){
			// log errors
		}

		//print_r($response['suggest']); exit;
		$suggestions = empty($response['suggest'])?[]:$response['suggest'];
		$results[] = $term;
		if(!empty($suggestions['title-suggestion'])){
			foreach($suggestions['title-suggestion'] as $s){
			foreach($suggestions['title-suggestion'] as $s){
				foreach($s['options'] as $o){
					$suggested_term = str_replace($s['text'], $o['text'], $term);
					$results[] = $suggested_term;
					$term = $suggested_term;
				}
			}
			}
		}

		if(!empty($suggestions['content-suggestion'])){
			foreach($suggestions['content-suggestion'] as $s){
			foreach($suggestions['content-suggestion'] as $s){
				foreach($s['options'] as $o){
					$suggested_term = str_replace($s['text'], $o['text'], $term);
					$results[] = $suggested_term;
					$term = $suggested_term;
				}
			}
			}
		}

        	if(count($results)){
        		return response()->json(array_unique($results));
        	}
        	else{
        		return [];
        	}
	}

	//public function isaCollectionDocumentSearch(Request $request){
	public function searchResults(Request $request){
		$collection_id = $request->collection_id;
		$collection = \App\Collection::find($collection_id);
		$keywords = $request->isa_search_parameter;
		$meta_query = '';
		$query_params = $request->query();
		foreach($query_params as $p=>$v){
			if(preg_match('/^meta_(\d*)/', $p, $matches)){
				// currently, no support for operator in the query string parameters
				// default operator is '='
				//$meta_filters_query[] = array('field_id'=>$matches[1], 'operator'=>'=', 'value'=>$v);
				//print_r($matches); print_r($v); exit;
				if(is_array($v)){
					foreach($v as $x){
						$meta_query .= '&meta_'.$matches[1].'[]='.$x;
					}
				}
				else{
					$meta_query .= '&meta_'.$matches[1].'='.$v;
				}	
			}
		}
		$client = new \GuzzleHttp\Client();
                $http_host = request()->getHttpHost();
                $protocol = request()->getScheme();
		$length=10;
		$start = empty($request->start)? 0 : $request->start;
                $endpoint = $protocol.'://'.$http_host.'/api/collection/1/search?log_search=0&search[value]='.$keywords.$meta_query.'&start='.$start.'&length='.$length;
//echo $endpoint; exit;
                $res = $client->get($endpoint);
                $status_code = $res->getStatusCode();
                if($status_code == 200){
					$body = $res->getBody();
            		$documents_array = json_decode($body);
				}
		$total_results_count = $documents_array->recordsTotal;
		$filtered_results_count = $documents_array->recordsFiltered;
        // log search query
        $old_query = Session::get('search_query');
        if(!empty($request->isa_search_parameter) && $old_query != $request->isa_search_parameter &&
            strlen($request->isa_search_parameter)>3){
            Session::put('search_query', $request->isa_search_parameter);
            $meta_query = json_encode($this->getMetaFilters($request));
			$user_id = empty(\Auth::user()->id)?null:\Auth::user()->id;
            $search_log_data = array('collection_id'=> $request->collection_id,
                'user_id'=> $user_id,
                'search_query'=> empty($request->isa_search_parameter)?'':$request->isa_search_parameter,
                'meta_query'=> $meta_query,
                'ip_address' => $request->ip(),
                'results'=>$total_results_count);
            if(!empty($request->collection_id)){
                $this->logSearchQuery($search_log_data);
            }
        }

		return view('search-results',['collection'=>$collection, 
			'results'=>$documents_array->data,
			'filtered_results_count'=>$filtered_results_count,
			'total_results_count'=>$total_results_count,
			'activePage'=>'Documents',
            'search_query'=> empty($request->isa_search_parameter)?'':$request->isa_search_parameter,
			'titlePage'=>'Documents']);
	}

//Class Ends
}
