<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SysConfigController extends Controller
{
    public function index(){
        return view('sysconfig');
    }
}
