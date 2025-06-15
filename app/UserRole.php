<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    protected $table = 'user_roles';
	public $timestamps = false;

	public function role(){
		return $this->belongsTo('App\Role');
	}

	public function user(){
		return $this->belongsTo('App\User');
	}

}
