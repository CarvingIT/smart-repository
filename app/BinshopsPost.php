<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Events\BinshopsPostSaved;

class BinshopsPost extends Model
{

	protected $dispatchesEvents = [
        'saved' => BinshopsPostSaved::class,
        ];

	//protected $table = 'binshops_post_translations';

	public function approvals(){
		return $this->morphMany('App\Approval', 'approvable');
	}

    public function publish(){
	    $this->user_id = auth()->user()->id;
            $this->is_published = 1;
            $this->save();
    }

	public function approval(){
        return $this->hasMany('App\DocumentApproval');
    }


}
