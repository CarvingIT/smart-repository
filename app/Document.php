<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use SoftDeletes;

    public function icon(){
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
        //get extension
        $path_fields = explode(".", $this->path);
        $cnt = count($path_fields);
        $extn = strtolower($path_fields[$cnt-1]);
        if(!empty($file_type_icons[$extn])) return $file_type_icons[$extn];
        else return 'file';
    }
}
