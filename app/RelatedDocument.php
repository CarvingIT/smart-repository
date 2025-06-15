<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RelatedDocument extends Model
{
	public function document(){
	return $this->belongsTo('App\Document');
	}
	public function related_document(){
	return $this->belongsTo('App\Document', 'related_document_id');
	}
	public function related_to(){
	return $this->belongsTo('App\Document', 'document_id');
	}

}
