<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $connection = 'sa_master';
    protected $table='organizations';
}
