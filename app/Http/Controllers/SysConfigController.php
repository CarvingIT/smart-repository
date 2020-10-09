<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sysconfig;
use Session;

class SysConfigController extends Controller
{
    public function index(){
	$config_details = Sysconfig::all();
	$sysconfig = array();
	foreach($config_details as $details){
        	$sysconfig[$details['param']] = $details['value'];
	}
        return view('sysconfig',['sysconfig'=>$sysconfig]);
    }

    public function save(Request $request){
         $c = new \App\Sysconfig;
	foreach ($request->except('_token') as $key => $part) {
		if(!empty($part)){
			$c->param = $key;
			$c->value = $part;
		}
	}
         try{
            $c->save();
            Session::flash('alert-success', 'System Configuration saved successfully!');
         }
         catch(\Exception $e){
            Session::flash('alert-danger', $e->getMessage());
            return redirect('/admin/sysconfig');
         }
            return redirect('/admin/sysconfig');
    }
	
} // Class ends
