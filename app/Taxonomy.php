<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Taxonomy extends Model
{
    protected $table = "taxonomies";
    protected $fillable = ['parent_id', 'label', 'display_order'];

    public function childs() {
        return $this->hasMany('App\Taxonomy','parent_id','id')->orderBy('display_order');
    }

	public function parent(){
		return $this->belongsTo('App\Taxonomy','parent_id','id') ;
	}

	public function createFamily(){
		// returns all models that are children, grand-children or grand-grand-children of this model	
		$family[] = $this; // add self first
		$i = 0;
		while(isset($family[$i])){
			$children = $family[$i]->childs;
			if($children){
				foreach($children as $child){
					$family[] = $child;
				}
			}
			$i++;
		}
		return $family;
	}
}
