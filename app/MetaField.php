<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MetaField extends Model
{
    use SoftDeletes;
    protected $table = 'meta_fields';

    public function series(){
        return $this->hasOne('App\\MetaFieldSeries', 'meta_field_id');
    }
}
