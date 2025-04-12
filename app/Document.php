<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Events\DocumentSaved;
use App\Events\DocumentDeleted;
use OwenIt\Auditing\Contracts\Auditable;
use Carbon\Carbon;
use App\Taxonomy;

class Document extends Model implements Auditable
{
    use SoftDeletes;
    use FullTextSearch;
	use \OwenIt\Auditing\Auditable;

	protected $auditExclude = [
        'text_content',
		'id',
		'created_by',
		'path',
		'collection_id'
    ];
    /**
     * The columns of the full text index
     */
    protected $searchable = [
        'title',
        'text_content'
    ];

    protected $dispatchesEvents = [
        'saved' => DocumentSaved::class,
        'deleted' => DocumentDeleted::class,
    ];

	protected $hidden = ['text_content'];

    public function icon($path = null){
        $file_type_icons = array(
            'doc' => 'doc',
            'docx' => 'doc',
            'ppt' => 'ppt',
            'pptx' => 'ppt',
            'txt' => 'txt',
            'pdf' => 'pdf',
            'png' => 'png',
            'jpg' => 'jpg',
            'jpeg' => 'jpg',
            'xml' => 'xml',
            'zip' => 'zip',
            'gz'=>'zip',
            'xls'=>'xls',
            'xlsx'=>'xls',
            'css'=>'css',
            'exe'=>'exe',
            'mp3'=>'mp3',
            'mp4'=>'mp4',
			'url'=>'url',
	    'm4a'=>'m4a',
        );
        $path = empty($path) ? $this->path : $path;
        //get extension
        $path_fields = explode(".", $path);
        $cnt = count($path_fields);
        $extn = strtolower($path_fields[$cnt-1]);
        if(!empty($file_type_icons[$extn])) return $file_type_icons[$extn];
        else return 'file';
    }

    public function human_filesize($bytes=null, $decimals = 2) {
        $bytes = empty($bytes)?$this->size:$bytes;
        $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) .' '. @$size[$factor];
    }

    public function meta_value($meta_field_id, $raw=false){
        $meta_value = \App\MetaFieldValue::where('document_id','=', $this->id)
            ->where('meta_field_id','=',$meta_field_id)->first();
		if(!$meta_value) return null;
		if($raw !== false){// return the json representation of array as is without getting label values
			return $meta_value->value;
		}

		$meta_field_type = $meta_value->meta_field->type;
        if($meta_value){
			if(preg_match('/^\[.*\]$/',$meta_value->value)){
				if($meta_field_type == 'TaxonomyTree'){
					$taxonomy_models = Taxonomy::whereIn('id', @json_decode($meta_value->value))->get();
					$terms = [];
					foreach($taxonomy_models as $t){
                        if(empty($t->parent_id) || $t->childs->count() > 0) continue;
						if(strtolower($t->label) == 'all') return $t->label; // special value (ALL)
						$terms[] = $t->label;
					}
					return implode(', ',$terms);
				}
				else{
					return @implode(', ',@json_decode($meta_value->value));
				}
			}
			else{
            	return $meta_value->value;
			}
        }
        return null;
    }

    public function meta(){
        return $this->hasMany('App\MetaFieldValue');
    }

    public function revisions(){
        return $this->hasMany('App\DocumentRevision');
    }

    public function owner(){
        return $this->belongsTo('App\User', 'created_by');
    }
    public function approver(){
        return $this->belongsTo('App\User', 'approved_by');
    }

    public function collection(){
	 return $this->belongsTo('App\Collection','collection_id');
    }

    public function approval(){
	return $this->hasMany('App\DocumentApproval');
    }
	
	public function related_documents(){
	return $this->hasMany('App\RelatedDocument');
	}

    public function related_to(){
	return $this->hasMany('App\RelatedDocument','related_document_id');
    }

    public function approvals(){
	return $this->morphMany('App\Approval', 'approvable');
    }
    
    public function publish(){
	    $this->approved_by = auth()->user()->id;
	    $this->approved_on = Carbon::now()->toDateTimeString();
        $this->locked = 1; // lock after publishing
	    $this->save();
    }

    public function lock(){
        $this->locked = 1;
        $this->save();
    }
    public function unlock(){
        $this->locked = 0;
        $this->save();
    }
    
    public function isLocked(){
        return ($this->locked == 1) ? true: false;
    }
}
