<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
	protected $fillable = ['approved_by_role'];
	public function approvable(){
		return $this->morphTo();
	}

	public function approver(){
		return $this->belongsTo('App\User', 'approved_by', 'id');
	}
}
