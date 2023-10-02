<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
	public function approvable(){
		return $this->morphTo();
	}
}
