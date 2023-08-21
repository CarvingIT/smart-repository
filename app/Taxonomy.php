<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Taxonomy extends Model
{
    protected $table = "taxonomies";
    protected $fillable = ['parent_id', 'label'];

    public function childs() {
        return $this->hasMany('App\Taxonomy','parent_id','id') ;
    }
}
