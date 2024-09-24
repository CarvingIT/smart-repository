<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SRTemplate extends Model
{
    //
	protected $table='sr_templates';
	
	public function collection(){
		return $this->belongsTo(Collection::class,'collection_id');
	}
}
