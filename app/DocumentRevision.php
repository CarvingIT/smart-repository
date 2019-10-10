<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentRevision extends Model
{
    protected $table='document_revisions';

    public function document(){
        return $this->belongsTo('App\Document', 'document_id');
    }

    public function user(){
        return $this->belongsTo('App\User', 'created_by');
    }
}
