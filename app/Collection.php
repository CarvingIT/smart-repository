<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Collection extends Model
{
    use SoftDeletes;

    public function documents(){
        return $this->hasMany('App\Document');
    }

    public function urls(){
        return $this->hasMany('App\Url');
    }

    public function meta_fields(){
        return $this->hasMany('App\MetaField')->orderBy('display_order');
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

	public function children(){
		return $this->hasMany('App\Collection','parent_id');
	}

	public function parent(){
		return $this->belongsTo('App\Collection', 'parent_id');
	}
}
