<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    public function documents(){
        return $this->hasMany('App\Document');
    }

    public function maintainer(){
        $maintainer_permission = \App\Permission::where('name','=','MAINTAINER')->first();
        $user_permission = \App\UserPermission::where('permission_id','=',$maintainer_permission->id)->where('collection_id','=', $this->id)->first();
        if(!empty($user_permission->user_id)){
            return \App\User::find($user_permission->user_id);
        }
        else{
            return null;
        }
    }
}
