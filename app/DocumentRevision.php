<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Events\DocumentRevisionCreated;

class DocumentRevision extends Model
{
    protected $table='document_revisions';
	protected $hidden = ['text_content'];

    protected $dispatchesEvents = [
        'created' => DocumentRevisionCreated::class,
    ];

    public function document(){
        return $this->belongsTo('App\Document', 'document_id');
    }

    public function user(){
        return $this->belongsTo('App\User', 'created_by');
    }
}
