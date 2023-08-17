<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Taxonomy extends Model
{
    protected $table = "taxonomies";
    protected $fillable = ['parent_id', 'label'];


    public function children()
    {
        return $this->hasMany(Taxonomy::class, 'parent_id');
    }

}
