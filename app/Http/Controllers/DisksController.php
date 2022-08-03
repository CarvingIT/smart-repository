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
		// add a check to see if a disk-name already exists in config/filesystems
		// disallow use of the same name
		$disks = array_keys(config('filesystems.disks'));
			if(in_array($request->disk_name, $disks)){
				abort(500, 'This disk exists');
			}
			// duplicate disk name check ends 
			$disk->name = $request->disk_name;
			$disk->driver = $request->driver;
			
			if($driver == 'ftp' || $driver == 'sftp'){
				$config = ['driver'=>$driver, 'host'=>$request->host, 'port'=>$request->port,
				'username'=>$request->username, 'password'=>$request->password,
				'root'=>$request->root, 'timeout'=>$request->timeout];
			}
			else if($driver == 's3'){
				$config = ['driver'=>$driver,'key'=>$request->key,'secret'=>$request->secret,
				'region'=>$request->region,'bucket'=>$request->bucket,'endpoint'=>$request->endpoint];
			}
			$disk->config = json_encode($config);
			$disk->save();
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
