<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Synonym extends Model
{
    protected $fillable = ['synonyms'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

}





