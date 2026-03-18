<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentApproval extends Model
{
    protected $fillable = ['document_id', 'approved_by', 'approved_by_role', 'approval_status', 'comments'];

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
