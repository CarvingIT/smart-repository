<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    public function documents(){
        return $this->hasMany('App\Document');
    }
}
