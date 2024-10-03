<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Events\ApprovalSaved;

class Approval extends Model
{
	protected $fillable = ['approved_by_role'];

    protected $dispatchesEvents = [
        'saved' => ApprovalSaved::class,
    ];

	public function approvable(){
		return $this->morphTo();
	}

	public function approver(){
		return $this->belongsTo('App\User', 'approved_by', 'id');
	}

	public function approver_role(){
		return $this->belongsTo('App\Role', 'approved_by_role', 'id');
	}
}
