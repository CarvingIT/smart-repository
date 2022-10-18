<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sysconfig;
use Session;

class SysConfigController extends Controller
{
    public function index(){
	$size_limit = ini_get("upload_max_filesize");
	$config_details = Sysconfig::all();
	$sysconfig = array();
	foreach($config_details as $details){
        	$sysconfig[$details['param']] = $details['value'];
	}
        return view('sysconfig',['sysconfig'=>$sysconfig,'size_limit'=>$size_limit]);
    }

    public function save(Request $request){
	\DB::table('sysconfig')->delete();
	if($request->hasFile('logo_url')){
            $filename = $request->file('logo_url')->getClientOriginalName();
            $new_filename = \Auth::user()->id.'_'.time().'_'.$filename;
	    $local_filepath = $request->file('logo_url')
                                ->storeAs('/public/sysconfig/'.\Auth::user()->id,$new_filename);
	}
	foreach ($request->except('_token') as $key => $part) {
    	$c = new \App\Sysconfig;
		$c->param = $key;
		if($key == 'logo_url'){
		$c->value = $new_filename;
		}
		else{
		$c->value = $part;
		}
		$c->save();
	}

        Session::flash('alert-success', 'System Configuration saved successfully!');
        return redirect('/admin/sysconfig');
    }
} // Class ends
