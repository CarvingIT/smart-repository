<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use SoftDeletes;
    use FullTextSearch;

    /**
     * The columns of the full text index
     */
    protected $searchable = [
        'title',
        'text_content'
    ];

    public function icon($path = null){
        $file_type_icons = array(
            'doc' => 'doc',
            'docx' => 'doc',
            'ppt' => 'ppt',
            'pptx' => 'ppt',
            'txt' => 'txt',
            'pdf' => 'pdf',
            'png' => 'png',
            'jpg' => 'jpg',
            'jpeg' => 'jpg',
            'xml' => 'xml',
            'zip' => 'zip',
            'gz'=>'zip',
            'xls'=>'xls',
            'xlsx'=>'xls',
            'css'=>'css',
            'exe'=>'exe',
            'mp3'=>'mp3',
            'mp4'=>'mp4',
        );
        $path = empty($path) ? $this->path : $path;
        //get extension
        $path_fields = explode(".", $path);
        $cnt = count($path_fields);
        $extn = strtolower($path_fields[$cnt-1]);
        if(!empty($file_type_icons[$extn])) return $file_type_icons[$extn];
        else return 'file';
    }

    public function human_filesize($bytes=null, $decimals = 2) {
        $bytes = empty($bytes)?$this->size:$bytes;
        $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) .' '. @$size[$factor];
    }

    public function meta_value($meta_field_id){
        $meta_value = \App\MetaFieldValue::where('document_id','=', $this->id)
            ->where('meta_field_id','=',$meta_field_id)->first();
        if($meta_value){
            return $meta_value->value;
        }
        return null;
    }

    public function meta(){
        return $this->hasMany('App\MetaFieldValue');
    }

    public function revisions(){
        return $this->hasMany('App\DocumentRevision');
    }

    public function owner(){
        return $this->belongsTo('App\User', 'created_by');
    }

}
