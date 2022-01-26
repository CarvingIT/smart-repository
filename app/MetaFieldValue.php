<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class MetaFieldValue extends Model implements Auditable
{
	use \OwenIt\Auditing\Auditable;

    protected $table = 'meta_field_values';

    public function document(){
        return $this->belongsTo('App\Document');
    }
}
