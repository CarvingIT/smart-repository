<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Disk;

class DisksController extends Controller
{
    public function index(){
        $disks = Disk::all();
        return view('disksmanagement', ['disks'=>$disks, 'activePage'=>'Disks','titlePage'=>'Disks']);
    }

    public function add_edit_disk($disk_id){
        if($disk_id == 'new'){
            $disk = new \App\Disk();
        }
        else{
            $disk = \App\Disk::find($disk_id);
        }
        return view('disk-form', ['disk'=>$disk,'activePage'=>'Disks', 'titlePage'=>'Disks']);
    }
}
