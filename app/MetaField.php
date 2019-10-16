<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MetaField extends Model
{
    use SoftDeletes;
    protected $table = 'meta_fields';
}
