<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Paytm extends Model
{
	protected $fillable = ['name','mobile','email','status','fee','order_id','transaction_id'];
}
