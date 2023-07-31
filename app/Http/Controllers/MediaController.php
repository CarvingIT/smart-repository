<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sysconfig;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function loadImage($filename){
		$config_details = SysConfig::all();
		foreach($config_details as $details){
   			$sysconfig[$details['param']] = $details['value'];
   		}
		$storage_drive = empty($sysconfig['media_storage_drive'])?'local':$sysconfig['media_storage_drive'];

        $mime = Storage::disk($storage_drive)->getDriver()->getMimetype($filename);
        $size = Storage::disk($storage_drive)->getDriver()->getSize($filename);

        $response =  [
          'Content-Type' => $mime,
          'Content-Length' => $size,
          'Content-Description' => 'File Transfer',
          'Content-Disposition' => "attachment; filename={$filename}",
          'Content-Transfer-Encoding' => 'binary',
        ];

       return \Response::make(Storage::disk($storage_drive)->get($filename), 200, $response);
    }
}
