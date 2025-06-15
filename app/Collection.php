<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\UserPermission;
use App\User;
use Illuminate\Notifications\Notifiable;

class Collection extends Model
{
    use SoftDeletes;
	use Notifiable;

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

	public function getUsers(){
		$user_permissions = UserPermission::where('collection_id', $this->id)->get();
		$user_ids = [];
		foreach($user_permissions as $u_p){
			if(!in_array($u_p->user_id, $user_ids)){
				$user_ids[] = $u_p->user_id;
			}
		}
		$user_models = User::whereIn('id', $user_ids)->get();
		return $user_models;
	}

    	public function routeNotificationForSlack($notification){
		$config = json_decode($this->column_config);
        	return $config->slack_webhook;
    	}

    	public function routeNotificationForMail($notification){
		$config = json_decode($this->column_config);
        	return explode(",", $config->notify_email);
    	}

    	public function getCollectionConfig(){
		$config = json_decode($this->column_config);
        	return $config;
	}
}
