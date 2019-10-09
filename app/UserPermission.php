<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;

class UserPermission extends Model
{
    //use SoftDeletes;
    protected $table = 'user_permissions';

    public function permission(){
        return $this->belongsTo('App\Permission', 'permission_id');
    }

    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }
}
