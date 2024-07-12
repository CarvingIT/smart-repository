<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Disk;
use App\Collection;
use Session;

class DisksController extends Controller
{
    public function index(){
        $disks = Disk::all();
        return view('disksmanagement', ['disks'=>$disks, 'activePage'=>'Disks','titlePage'=>'Disks']);
    }

    public function add_edit_disk($disk_id){
        if($disk_id == 'new'){
            $disk = new Disk;
        }
        else{
            $disk = Disk::find($disk_id);
        }
        return view('disk-form', ['disk'=>$disk,'activePage'=>'Disks', 'titlePage'=>'Disks']);
    }

	public function save(Request $request){
		$disk_id = $request->disk_id;
		$driver = $request->driver;

		if(empty($disk_id)){
			$disk = new Disk;
		}
		else{
			$disk = Disk::find($disk_id);
		}
			$disk->name = $request->disk_name;
			$disk->driver = $request->driver;
			
			if($driver == 'ftp' || $driver == 'sftp'){
				$config = ['driver'=>$driver, 'host'=>$request->host, 'port'=>(int) $request->port,
				'username'=>$request->username, 'password'=>$request->password,
				'root'=>$request->root, 'timeout'=>(int) $request->timeout];
			}
			else if($driver == 's3'){
				$config = ['driver'=>$driver,'key'=>$request->key,'secret'=>$request->secret,
				'region'=>$request->region,'bucket'=>$request->bucket,'endpoint'=>$request->endpoint];
			}
			else if($driver == 'google'){
				$config = ['driver'=>$driver, 'clientId'=>$request->client_id, 'clientSecret' => $request->client_secret,
				'refreshToken'=> $request->refresh_token, 'folderId' => $request->folder_id];
			}
			$disk->config = json_encode($config);
			try{
				$disk->save();
			}
			catch(\Exception $e){
				abort(500, 'Could not save the disk. Perhaps, the disk-name exists.');
			}
		return redirect('/admin/storagemanagement');
	}

	public function delete(Request $request){
		$disk = Disk::find($request->disk_id);
		if(Collection::where('storage_drive', $disk->name)->count() > 0){
			Session::flash('alert-danger', 'The disk is in use; cannot delete.');
		}
		else{
			$disk->delete();
		}
		return redirect('/admin/storagemanagement');
	}
}
