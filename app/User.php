<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\UserRole;
use App\Role;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
	'created_at' => 'datetime',
	'updated_at' => 'datetime',
	'deleted_at' => 'datetime'
    ];

	protected $table = "users";

    public function roles(){
        return $this->hasMany('App\UserRole');
    }
    public function docApprovals(){
        return $this->hasMany('App\DocumentApproval','approved_by');
    }

    public function userrole($user_id){
	$role = UserRole::where('user_id',$user_id)->first();
	if(!empty($role))
	return $role->role_id;
    }
    public function userrolename($user_id){
	$role = UserRole::where('user_id',$user_id)->first();
	if(!empty($role)){
	$role_details = Role::find($role->role_id);
	return $role_details->name;
	}
    }


    public function hasRole($role_name){
        $roles = $this->roles()->get();
        $role = \App\Role::where('name',$role_name)->first();
        $role_id = null;
        if($role){
            $role_id = $role->id;
        }
        foreach($roles as $r){
            if($role_id == $r->role_id)
            return true;
        }
        return false;
    }

    public function accessPermissions(){
		return $this->hasMany(UserPermission::class, 'user_id');
    }

    public function hasPermission($collection_id, $permission_name){
        $user_permissions = $this->accessPermissions;
        $c = \App\Collection::find($collection_id);
        /*
        check VIEW access first
        If the collection is Public or if the user has any permission on the collection
        */ 
        if($permission_name == 'VIEW'){
            if($c->type == 'Public') return true;
            
            foreach($user_permissions as $u_p){
				if($u_p->permission->name == 'VIEW'
                 && $u_p->collection_id == $collection_id) return true;
            }
        }
        foreach($user_permissions as $u_p){
            if($u_p->collection_id == $collection_id && 
                (($u_p->permission)->name == $permission_name || ($u_p->permission)->name == 'MAINTAINER')){
                return true;
            }
        }
		// code to check if the collection allows authenticated users with some default permissions
		$collection_config = json_decode($c->column_config);
		$auth_user_permissions = !empty($collection_config->auth_user_permissions)?$collection_config->auth_user_permissions:[];
		if(in_array($permission_name, $auth_user_permissions)){
			return true;
		}
        return false;
    }

    public function canEditDocument($document_id){
        $document = \App\Document::find($document_id);
        $collection_id = $document->collection_id;
        if($this->hasPermission($collection_id, 'MAINTAINER') || 
            $this->hasPermission($collection_id, 'EDIT_ANY') ||
            ($this->hasPermission($collection_id, 'EDIT_OWN') && $document->created_by == $this->id)){
            return true;
        }
        return false;
    }

    public function canDeleteDocument($document_id){
        $document = \App\Document::find($document_id);
        $collection_id = $document->collection_id;
        if($this->hasPermission($collection_id, 'MAINTAINER') || 
            $this->hasPermission($collection_id, 'DELETE_ANY') ||
            ($this->hasPermission($collection_id, 'DELETE_OWN') && $document->created_by == $this->id)){
            return true;
        }
        return false;
    }

    public function canApproveDocument($document_id, $user_role=null){
        $document = \App\Document::find($document_id);
        $collection_id = $document->collection_id;
	$collection_details = \App\Collection::find($collection_id);
	$approval_roles = json_decode($collection_details->column_config);
	
        if($this->hasPermission($collection_id, 'MAINTAINER') || 
            ($this->hasPermission($collection_id, 'APPROVE') && $document->created_by == $this->id) || in_array($user_role,$approval_roles->approved_by)){
            return true;
        }
        return false;
    }


     /**
     * Enter your own logic (e.g. if ($this->id === 1) to
     *   enable this user to be able to add/edit blog posts
     *
     * @return bool - true = they can edit / manage blog posts,
     *        false = they have no access to the blog admin panel
     */
    public function canManageBinshopsBlogPosts()
    {
        // Enter the logic needed for your app.
        // Maybe you can just hardcode in a user id that you
        //   know is always an admin ID?

        if (       $this->id === 1
             && $this->email === "your_admin_user@your_site.com"
           ){

           // return true so this user CAN edit/post/delete
           // blog posts (and post any HTML/JS)

           return true;
        }

        // otherwise return false, so they have no access
        // to the admin panel (but can still view posts)

        //return false;
        return true;
    }

///// 
} // End of the class
