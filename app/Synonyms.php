<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Synonyms extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = "synonyms";

    protected $fillable = ['synonyms'];

}
