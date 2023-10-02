<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BinshopsPost extends Model
{
	public function approvals(){
		return $this->morphMany('App\Approval', 'approvable');
	}
}
