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
	\DB::table('sysconfig')->delete();
	foreach ($request->except('_token') as $key => $part) {
    	$c = new \App\Sysconfig;
		$c->param = $key;
		$c->value = $part;
		$c->save();
	}
        Session::flash('alert-success', 'System Configuration saved successfully!');
        return redirect('/admin/sysconfig');
    }
} // Class ends
