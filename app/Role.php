<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Role extends Model
{

	use Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = "roles";

    protected $fillable = ['name'];

    public function user_roles(){
        return $this->hasMany('App\UserRole');
    }

    public function routeNotificationForMail($notification){
        // get email addresses of users having this role
        $email_addresses = [];
        foreach($this->user_roles as $ur){
            $email_addresses[] = $ur->user->email;
        }
        return $email_addresses;
    }

}
