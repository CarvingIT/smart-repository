<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Url extends Model
{
    use FullTextSearch;
    protected $searchable = [
        'title',
        'text_content'
    ];

    public function icon(){
	$file_type_icons = array(
		'application/pdf'=>'pdf',
		'image/jpeg'=>'jpg',
		'image/jpg'=>'jpg',
		'image/png'=>'png',
		'text/html'=>'html',
		'text/plain'=>'txt',
	);
        if(!empty($file_type_icons[$this->type])) return $file_type_icons[$this->type];
        else return 'file';
    }

    public function human_filesize($bytes=null, $decimals = 2) {
        $bytes = empty($bytes)?$this->size:$bytes;
        $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) .' '. @$size[$factor];
    }

    public function collection(){
	 return $this->belongsTo('App\Collection','collection_id');
    }
}
