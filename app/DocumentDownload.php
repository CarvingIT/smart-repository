<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentDownload extends Model
{
	public function document(){
		return $this->belongsTo('App\Document');
	}
}
