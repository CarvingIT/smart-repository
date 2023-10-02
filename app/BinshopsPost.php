<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BinshopsPost extends Model
{
    //
	protected $table = 'binshops_posts';

	public function approvals(){
		return $this->morphMany('App\Approval', 'approvable');
	}
}
