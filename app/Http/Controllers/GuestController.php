<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GuestController extends Controller
{
    //
	public function welcome()
    {
        $is_demo = env('IS_DEMO');
        if($is_demo == 0){
                return redirect('/collections');
        }
        else{
                return redirect('/welcome');
        }

    }

}
