<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(){
        $users = User::all();
        return view('usermanagement', ['users'=>$users]);
    }

    public function save(Request $request){
         $id = empty($request->input('id'))?'':$request->input('id');
         $u = empty($id)? new User():User::find($id)->get();
         $u->email = $request->input('email');
         $u->name = $request->input('name');
         $u->password = empty($u->password)? bcrypt($this->generatePassword(8)) : $u->password;
         $u->save();
         return redirect('/admin/usermanagement');
    }

    public function generatePassword($length) {
        $pswd = "";
        $possible = "0123456789abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $i = 0;
        while ($i < $length) {
            $char = substr($possible, mt_rand(0, strlen($possible) - 1), 1);
            if (!strstr($pswd, $char)) {
                $pswd .= $char;
                $i++;
            }
        }
        return $pswd;
    }

}
