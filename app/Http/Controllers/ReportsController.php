<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function downloads(){
        return view('download-report',[]);
    }
}
