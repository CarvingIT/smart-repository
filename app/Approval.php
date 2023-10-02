<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
	protected $fillable = ['approved_by_role'];
	public function approvable(){
		return $this->morphTo();
	}
}
