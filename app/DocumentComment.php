<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentComment extends Model
{
    //
    protected $table='document_comments';

    public function document(){
        return $this->belongsTo('App\Document', 'document_id');
    }
    public function user(){
        return $this->belongsTo('App\User', 'created_by');
    }

}
