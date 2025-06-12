<?php

namespace App\Traits;
use Illuminate\Http\Request;
use Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\Facades\Log;
use App\Collection;
use App\MetaField;
use App\MetaFieldValue;

trait Search{
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
                    			});
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
		// find the type of meta field 
		$m_field = MetaField::find($mf['field_id']);
		if ($m_field->type == 'TaxonomyTree'){
                	$documents = $documents->whereHas('meta', function (Builder $query) use($mf){
                        $query->where('meta_field_id',$mf['field_id'])->where('value', 'like', '%"'.$mf['value'].'"%');
                    	}
                	);
		}
		else{
                	$documents = $documents->whereHas('meta', function (Builder $query) use($mf){
                        $query->where('meta_field_id',$mf['field_id'])->where('value', 'like', '%'.$mf['value'].'%');
                    	}
                	);
            	}
            }
	}
        return $documents;
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
		$collection_ids = [];
        $collections_requiring_approval = [];
        $collections_without_approval = [];
		foreach($collections as $c){
			$collection_ids[] = $c->id;
            if($c->require_approval == 1){
                $collections_requiring_approval[] = $c->id;
            }
            else{
                $collections_without_approval[] = $c->id;
            }
		}
        Log::debug(json_encode($collection_ids));
		$collection_type = $request->collection_type;
		if($collection_type == 'Web resources'){
        	$documents = \App\Url::whereIn('collection_id', $collection_swithout_approval);
        	$elastic_index = 'sr_urls';
		}
		else{
            Log::debug('Not requiring approval'.json_encode($collections_without_approval));
        	$documents = \App\Document::whereIn('collection_id', $collections_without_approval);
            Log::debug('Requiring approval'.json_encode($collections_requiring_approval));
			$documents = $documents->orWhere(function($query) use($collections_requiring_approval){
                $query->whereIn('collection_id', $collections_requiring_approval)
                    ->whereNotNull('approved_on');
			});
      		$elastic_index = 'sr_documents';
		}
		//Log::debug($elastic_index.' - '.implode(",", $collection_ids));
	}
        $total_count = $documents->count();
	Log::debug('Total Count: '.$total_count);

		$highlights = [];
        if(!empty($request->search['value']) && strlen($request->search['value'])>1){
            $search_term = $request->search['value'];
	    Log::debug('Search term: '.$search_term);
            $words = explode(' ',$search_term);
			//$search_mode = empty($request->search_mode)?'default':$request->search_mode;

	    		//$analyzer = 'standard';
	    		$analyzer = 'synonyms_analyzer';
	
				$title_q_with_and = ['query'=>$search_term, 'operator'=>'and', 'boost'=>4, 'analyzer'=>$analyzer];
				$title_q_with_and_ps = ['query'=>$search_term, 'operator'=>'and', 'boost'=>2, 'analyzer'=>'porter_stem_analyzer'];
				$text_q_with_and = ['query'=>$search_term, 'operator'=>'and', 'boost'=>2, 'analyzer'=>$analyzer];
				$text_q_with_and_ps = ['query'=>$search_term, 'operator'=>'and', 'boost'=>2, 'analyzer'=>'porter_stem_analyzer'];
				$q_title_phrase = ['query'=>$search_term, 'boost'=>6, 'analyzer'=>$analyzer];// just standard analyzer should be enough here
				$q_without_and = ['query'=>$search_term, 'fuzziness'=>'AUTO', 'analyzer'=>$analyzer];
				$q_without_and_ps = ['query'=>$search_term, 'analyzer'=>'porter_stem_analyzer'];
				$q_text_phrase = ['query'=>$search_term, 'boost'=>3, 'analyzer'=>$analyzer];// just standard analyzer should be enough here

				$params = [
					'index' => 'sr_documents',
					'body' => [
						'query' => [
							'bool' => [
								'should' => [
									[
										'match_phrase' => [
											'title' => $q_title_phrase,
										]
									],
									[
										'match' => [
											'title' => $title_q_with_and,
										]
									],
									[
										'match' => [
											'text_content' => $text_q_with_and,
										]
									],
									[
										'match_phrase' => [
											'text_content' => $q_text_phrase
										]
									],
									[
										'match' => [
											'title' => $q_without_and,
										]
									],
									[
										'match' => [
											'text_content' => $q_without_and,
										]
									],
									[
										'match' => [
											'title.porter_stem' => $title_q_with_and_ps,
										]
									],
									[
										'match' => [
											'text_content.porter_stem' => $text_q_with_and_ps,
										]
									],
									[
										'match' => [
											'title.porter_stem' => $q_without_and_ps,
										]
									],
									[
										'match' => [
											'text_content.porter_stem' => $q_without_and_ps
										]
									],
								],
								//'minimum_should_match' => 1
							],
						],
						'highlight' => [
							'fields' => [
								'text_content' => [ 'type' => 'unified'],
								'text_content.porter_stem' => [ 'type' => 'unified'],
								'title' => [ 'type' => 'unified'],
								'title.porter_stem' => [ 'type' => 'unified']
							],
							'max_analyzed_offset'=>100000
						]
					]
				];

			// add must match clause 
			if(!empty($request->must_match) && count($request->must_match) > 0){
				Log::debug('Adding must match clause.');
				foreach($request->must_match as $must_keyword){
					$params['body']['query']['bool']['must'][] = 
										['match' => [
											'text_content' => $must_keyword
										]];
				}
			}

	    	if(!empty($request->collection_id)){
				// this is currently done at the db level
            	//$params['body']['query']['bool']['must']['term']['collection_id']=$request->collection_id;
			}
        }
        $columns = array('type', 'title', 'size', 'updated_at');
	$ordered_document_ids = '';
        $scores = [];
        if(!empty($params)){
	    $params['index'] = $elastic_index;
	    $params['size'] = 1000;// set a max size returned by ES
        //Log::debug(json_encode($params));
        $document_ids = array();
		try{
			$client = $this->getElasticClient();
           	$response = $client->search($params);
            foreach($response['hits']['hits'] as $h){
                //$document_ids[] = $h['_id'];
		        $highlights[$h['_id']] = @$h['highlight'];
		        $scores[$h['_id']] = $h['_score'];
            }
		}
		catch(\Exception $e){
			// some error; switch to db search
			Log::debug($e->getMessage());	
            Log::debug('Switching to DB search');
			return $this->searchDB($request);
		}
	    //Log::debug(json_encode($response['hits']));
	    $document_ids = array_keys($scores);
	    $ordered_document_ids = implode(",", $document_ids);
        }
	//Log::debug('Ordered IDs: '.$ordered_document_ids);
        // get title filtered documents
        /*
		if(!empty(Session::get('title_filter')) || !empty($request->title_filter)){
            $documents = $this->getTitleFilteredDocuments($request, $documents);
		}
         */
        // get Meta filtered documents
        $documents = $this->getMetaFilteredDocuments($request, $documents);

	if($request->search_type == 'chatbot'){
		$documents = $documents->where('type','<>','url');
	}

	//Log::debug(json_encode($document_ids));
	//Log::debug('Count before wherein: '.$documents->count());
    if(!empty($document_ids)){
	    Log::debug('Found: '.@count($document_ids));
    }
	//if(isset($document_ids) && count($document_ids) > 0){
	if(isset($document_ids)){
        Log::debug(json_encode($document_ids));        
       	//$documents = $documents->whereIn('id', $document_ids);
        // There's no meta filtering of documents under common-search 
        // since meta information can be different for different collections
       	$documents = \App\Document::whereIn('id', $document_ids);
	}
	//$query = $documents->toSql();
	//Log::debug($query);
	Log::debug('Count: '.$documents->count());

	$filtered_count = $documents->count(); 

	$sort_column = empty($columns[@$request->order[0]['column']])?'':$columns[@$request->order[0]['column']];
	$sort_direction = @empty($request->order[0]['dir'])?'desc':$request->order[0]['dir'];
	$length = empty($request->length)?10:$request->length;
	$start = empty($request->start)?0:$request->start;
	if(!empty($params) && !empty($ordered_document_ids) && empty($sort_column)){
	// initial sorting is by relevance
	Log::debug('Collection count: '.$documents->count().' -- Ordered array count: '.count($document_ids));
	if(!empty($ordered_document_ids)){
		$documents = $documents->orderByRaw("FIELD(id, $ordered_document_ids)");
	}
	$documents = $documents
	     ->offset($start)
	     ->limit($length)
	     ->get();

		$doc_ids = [];
		foreach($documents as $d){
			$doc_ids[] = $d->id;
		}
		Log::debug('Doc ids in result: '.implode(",", $doc_ids));	
		//exit;
	}
	else{
		if(env('DEFAULT_META_SORT_FIELD',false)){
			$sort_direction = (env('DEFAULT_META_SORT_DIRECTION','') == 'desc') ? 'desc' : 'asc';
			$mf = MetaField::where('label',env('DEFAULT_META_SORT_FIELD',''))->first();

			$meta_values = MetaFieldValue::where('meta_field_id', $mf->id)
				->orderBy('value', $sort_direction)
				->orderBy('document_id', 'desc')
				->get();	
			$ordered_document_ids = [];
			foreach($meta_values as $mv){
				$ordered_document_ids[] = $mv->document_id;
			}
			$doc_id_str = implode(",", $ordered_document_ids);

			$documents = $documents->whereIn('id', $ordered_document_ids);
			$filtered_count = $documents->count();
			$documents = $documents
				->orderByRaw("FIELD(id, $doc_id_str)")
        			->limit($length)->offset($request->start)->get();
		}
		else{
		$sort_column = empty($sort_column)?'updated_at':$sort_column;
		$documents = $documents
			->orderby($sort_column,$sort_direction)
        	->limit($length)->offset($request->start)->get();
		}
	}

	$has_approval = \App\Collection::where('id','=',$request->collection_id)
		->where('require_approval','=','1')->get();

		if($request->is('api/*') || $request->return_format == 'raw'){
			return 
        	 array(
            	'data'=>$documents,
		        'highlights'=>$highlights,
		        'scores'=>$scores,
            	'draw'=>(int) $request->draw,
            	'recordsTotal'=> $total_count,
            	'recordsFiltered' => $filtered_count,
            	'error'=> '',
        	);
		}
		else{
            //Log::debug(count($documents));
        	$results_data = $this->datatableFormatResults(
                array('request'=>$request, 
                'documents'=>$documents, 
		        'highlights'=>$highlights,
		        'scores'=>$scores,
                'has_approval'=>$has_approval)
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
		$collection_ids = [];
        $collections_requiring_approval = [];
        $collections_without_approval = [];
		foreach($collections as $c){
			$collection_ids[] = $c->id;
            if($c->require_approval == 1){
                $collections_requiring_approval[] = $c->id;
            }
            else{
                $collections_without_approval[] = $c->id;
            }
		}
        Log::debug(json_encode($collection_ids));
		$collection_type = $request->collection_type;
		if($collection_type == 'Web resources'){
        		$documents = \App\Url::whereIn('collection_id', $collection_ids);
        		$elastic_index = 'sr_urls';
		}
		else{
            Log::debug('Not requiring approval'.json_encode($collections_without_approval));
        	$documents = \App\Document::whereIn('collection_id', $collections_without_approval);
            Log::debug('Requiring approval'.json_encode($collections_requiring_approval));
			$documents = $documents->orWhere(function($query) use($collections_requiring_approval){
                $query->whereIn('collection_id', $collections_requiring_approval)
                    ->whereNotNull('approved_on');
			});
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
		if($request->is('api/*') || $request->return_format == 'raw'){
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
        $highlights = empty($data['highlights'])?[]:$data['highlights'];

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
			// commented the line below since the video/audio can be played on the details page.
			//$action_icons .= '<a class="btn btn-primary btn-link" title="Play" href="/collection/'.$d->collection_id.'/document/'.$d->id.'/media-player" target="_blank"><i class="material-icons">play_arrow</i></a>';
		}
		else{
			if(!empty($d->path) && $d->path != 'N/A'){
			$action_icons .= '<a class="btn btn-primary btn-link" title="Download" href="/collection/'.$d->collection_id.'/document/'.$d->id.'" target="_blank"><i class="material-icons">cloud_download</i></a>';
			}
		}
	    }
  	    else if ($content_type == 'Web resources'){		
		$action_icons .= '<a class="btn btn-primary btn-link" href="'.$d->url.'" target="_blank"><i class="material-icons">link</i></a>';
	    }

		if(env('ENABLE_INFO_PAGE') == 1){
	    $action_icons .= '<a class="btn btn-primary btn-link" title="Information and more" href="/collection/'.$d->collection_id.'/document/'.$d->id.'/details"><i class="material-icons">forward</i></a>';
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
                    if($d->locked != 1){
                $action_icons .= '<a class="btn btn-success btn-link" href="/document/'.$d->id.'/edit" title="Create a new revision"><i class="material-icons">edit</i></a>';
                    }
                }
                if(Auth::user()->canDeleteDocument($d->id)){
                    if($d->locked != 1){
                $action_icons .= '<span class="btn btn-danger btn-link confirmdelete" onclick="showDeleteDialog('.$d->id.');" title="Delete document"><i class="material-icons">delete</i></span>';
                    }
                }
            }
	    } // if collection's content-type == Uploaded documents
	    //$title = $d->title.': '. substr($d->text_content, 0, 100).' ...';
	    $title = $d->title;
	    $title = mb_convert_encoding($title, 'UTF-8', 'UTF-8');
	    //$title = $d->title;
        $result = array(
                'type' => array('display'=>'<img class="file-icon" src="/i/file-types/'.$d->icon().'.png" />', 'filetype'=>$d->icon()),
                'title' => $title,
                'size' => array('display'=>$d->human_filesize(), 'bytes'=>$d->size),
                'updated_at' => array('display'=>date('Y-M-d', strtotime($d->updated_at)), 'updated_date'=>$d->updated_at),
                'highlights'=>array_shift($highlights),
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

    public function logSearchQuery($data){
        if(env('LOG_SEARCH_QUERY')==1){
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
        }// if
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

	//public function isaCollectionDocumentSearch(Request $request){
	public function searchResults(Request $request){
		$collection_id = $request->collection_id;
		$collection = \App\Collection::find($collection_id);
		//$analyzer = $request->analyzer;
		$keywords = $request->isa_search_parameter;
		$request->merge(['search'=>['value'=>$keywords], 'return_format'=>'raw']);
		
		$search_results = $this->search($request);
		//print_r($search_results); exit;
		$search_results = json_decode($search_results);
		$total_results_count = $search_results->recordsTotal;
		$filtered_results_count = $search_results->recordsFiltered;
        // log search query
        $old_query = Session::get('search_query');
        if(!empty($request->isa_search_parameter) && $old_query != $request->isa_search_parameter &&
            strlen($request->isa_search_parameter)>3){
            Session::put('search_query', $request->isa_search_parameter);
            $meta_query = json_encode($this->getMetaFilters($request));
			$user_id = empty(\Auth::user()->id)?null:\Auth::user()->id;
            $search_log_data = array('collection_id'=> $request->collection_id,
                'user_id'=> $user_id,
                'search_query'=> $keywords,
                'meta_query'=> $meta_query,
                'ip_address' => $request->ip(),
                'results'=>$total_results_count);
            if(!empty($request->collection_id)){
                $this->logSearchQuery($search_log_data);
            }
        }
		$highlights = json_decode(json_encode(@$search_results->highlights, true), true);
		//Log::debug($highlights);exit;
		return view('search-results',['collection'=>$collection, 
			'results'=>$search_results->data,
			'highlights'=> $highlights,  
			'filtered_results_count'=>$filtered_results_count,
			'total_results_count'=>$total_results_count,
			'activePage'=>'Documents',
            'search_query'=> $keywords,
			'titlePage'=>'Documents']);
	}
}
