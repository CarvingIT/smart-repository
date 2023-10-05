<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentApproval extends Model
{
    //
    public function document(){
        return $this->belongsTo('App\Document', 'document_id');
    }
    public function user(){
        return $this->belongsTo('App\User', 'approved_by');
    }

    public function post(){
        return $this->belongsTo('App\BinshopsPost', 'post_id');
    }

}
