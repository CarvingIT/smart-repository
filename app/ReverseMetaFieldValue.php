<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReverseMetaFieldValue extends Model
{
	public $timestamps = false;

	public function document(){
		return $this->belongsTo('App\Document');
	}
}
