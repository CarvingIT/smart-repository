<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Session;



class UserController extends Controller
{
    /**
     * Display a listing of the users
     *
     * @param  \App\User  $model
     * @return \Illuminate\View\View
     */
     public function index(User $model)
    {
        #return view('users.index', ['users' => $model->paginate(15),'activePage'=>'Users']);		## This page is as it is.

        return view('usermanagement', ['users'=>$model->all(), 'activePage'=>'user-management', 'titlePage' => 'Users']);
    }

    /**
     * Show the form for creating a new user
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
		if(!auth()->user()->hasRole('admin')){
			return abort(403);
		}
        return view('users.form');
    }

    /**
     * Store a newly created user in storage
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @param  \App\User  $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(UserRequest $request, User $model)
    {
        $model->create($request->merge(['password' => Hash::make($request->get('password'))])->all());
        return redirect()->route('user.index')->withStatus(__('User successfully created.'));
    }

    /**
     * Show the form for editing the specified user
     *
     * @param  \App\User  $user
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UserRequest $request, User  $user)
    {
        $hasPassword = $request->get('password');
        $user->update(
            $request->merge(['password' => Hash::make($request->get('password'))])
                ->except([$hasPassword ? '' : 'password']
        ));

        return redirect()->route('user.index')->withStatus(__('User successfully updated.'));
    }

    public function autoComplete(Request $request){
	$users = \App\User::where("email","LIKE","%{$request->input('term')}%")->get();

	$results = array();
	foreach($users as $u){
		$results[] = ['value' => $u->email];
	}
	if(count($results)){
        return response()->json($results);
	}
	else{
	return ['value'=>'No Result Found'];
	}
    }



    /**
     * Remove the specified user from storage
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
	$user = \App\User::findOrFail($request->user_id);
	if(!empty($request->delete_captcha) &&
                $request->delete_captcha == $request->delete_captcha){
        	$user->delete();
		Session::flash('alert-success', 'User successfully deleted.');
        	return redirect()->route('user.index');
        }
	else{
		Session::flash('alert-danger', 'Please fill Captcha');
        	return redirect('/user');
        }
    }
}
