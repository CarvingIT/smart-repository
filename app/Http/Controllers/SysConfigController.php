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
	$storage_disks =  config('filesystems.disks');
	foreach($config_details as $details){
        	$sysconfig[$details['param']] = $details['value'];
	}
        return view('sysconfig',['sysconfig'=>$sysconfig,'size_limit'=>$size_limit,'storage_disks'=>$storage_disks]);
    }

    public function save(Request $request){

	if($request->hasFile('logo_url')){
            $filename = $request->file('logo_url')->getClientOriginalName();
            $new_filename = \Auth::user()->id.'_'.time().'_'.$filename;
	    $local_filepath = $request->file('logo_url')
                                ->storeAs('public/',$new_filename,'local');
	}
	if($request->hasFile('favicon_url')){
            $fa_filename = $request->file('favicon_url')->getClientOriginalName();
            $fa_new_filename = \Auth::user()->id.'_'.time().'_'.$fa_filename;
	    $local_filepath = $request->file('favicon_url')
                                ->storeAs('public/',$fa_new_filename,'local');
	}
	foreach ($request->except('_token') as $key => $part) {
    	$c = Sysconfig::all();
	foreach($c as $config){
		if(($config->param == 'logo_url' && empty($request->file('logo_url'))) || ($config->param == 'favicon_url' && !empty($request->file('favicon_url')))){ 
			continue; 
		}
		else{
		$c_param = Sysconfig::where('param',$key)->first();
		if(!empty($c_param)){
		$c_param->delete();	
		}

    		$c = new \App\Sysconfig;
		$c->param = $key;
		if($key == 'logo_url'){ 
		$c->value = $new_filename;
		}
		elseif($key == 'favicon_url'){
		$c->value = $fa_new_filename;
		}
		else{
		$c->value = $part;
		}
		$c->save();
		}
	}
	}## config_key foreach ends

        Session::flash('alert-success', 'System Configuration saved successfully!');
        return redirect('/admin/sysconfig');
    }
} // Class ends
