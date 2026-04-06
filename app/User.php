<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\UserRole;
use App\Role;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;
    use SoftDeletes;
    use HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'organization', 'occupation',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
	'two_factor_enabled_at' => 'datetime',
	'created_at' => 'datetime',
	'updated_at' => 'datetime',
	'deleted_at' => 'datetime'
    ];

    public function hasTwoFactorEnabled(): bool
    {
        return !empty($this->two_factor_secret) && !empty($this->two_factor_enabled_at);
    }

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
		// if this collection is a child collection, the permissions should be inherited from the parent
		// go upward the parents till parent_id is null
		while(!empty($c->parent_id)){
			$c = \App\Collection::find($c->parent_id);	
		}

        /*
        check VIEW access first
        If the collection is Public or if the user has any permission on the collection
        */ 
        if($c->type == 'Public' && $permission_name == 'VIEW') return true;

        foreach($user_permissions as $u_p){
            if($u_p->collection_id == $c->id && 
                (($u_p->permission)->name == $permission_name || ($u_p->permission)->name == 'MAINTAINER')){
                if(empty($u_p->till_date)) return true;
                else if(date('Y-m-d') <= $u_p->till_date) return true;
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

    public function canShareDocument($document_id){
        $document = \App\Document::find($document_id);
        $collection_id = $document->collection_id;
        if($this->hasPermission($collection_id, 'MAINTAINER') || 
            $this->hasPermission($collection_id, 'CAN_SHARE') ||
            ($this->hasPermission($collection_id, 'VIEW') && $document->created_by == $this->id)){
            return true;
        }
        return false;
    }

    public function canApproveDocument($document_id, $user_role=null){
        $document = \App\Document::find($document_id);
        if (!$document) return false;
        $collection_id = $document->collection_id;
	$collection_details = \App\Collection::find($collection_id);
	if (!$collection_details) return false;
	$approval_roles = json_decode($collection_details->column_config);
	if (!$approval_roles) return false;

	// collect all roles for this user to support multi-role users
	$all_user_role_ids = $this->roles->pluck('role_id')->toArray();
	$user_role_to_check = $user_role ?? (count($all_user_role_ids) ? $all_user_role_ids[0] : null);

        if($this->hasPermission($collection_id, 'MAINTAINER') ||
            ($collection_details->require_approval && !empty($approval_roles->approved_by) &&
            (in_array($user_role_to_check, $approval_roles->approved_by) ||
             count(array_intersect($all_user_role_ids, (array)$approval_roles->approved_by)) > 0))){
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
		if(count($this->roles) > 0){
			return true;
		}
        return false;
    }

    public function favorites(){
        return $this->belongsToMany(Document::class, "user_favourite", "user_id", "document_id")
                    ->withTimestamps();
    }

    public function sharedLinks(){
        return $this->hasMany(SharedLink::class)->orderBy('created_at', 'desc');
    }

    public function savedSearches(){
        return $this->hasMany(SavedSearch::class)->orderBy('created_at','desc');
    }

    public function alerts(){
        return $this->hasMany(Alert::class, 'user_id');
    }

///// 
} // End of the class
