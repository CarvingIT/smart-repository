<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;



class UserController extends Controller
{
    /**
     * Display a listing of the users
     *
     * @param  \App\User  $model
     * @return \Illuminate\View\View
     */
   #public function index(User $model)
    public function index(User $model)
    {
        #return view('users.index', ['users' => $model->paginate(15),'activePage'=>'Users']);		## This page is as it is.

	$records_all = DB::table('users')
            ->select('id','name','email','created_at');
	$users = $records_all->distinct()->get();
	$total_users = $users->count();
	$action_icons = '';
	foreach($users as $u){
	$action_icons .= '<a href="/admin/user/'.$u->id.'/edit"><img class="icon" src="/i/pencil-edit-button.png" /></a>';
        $action_icons .= '<a href="/admin/user/'.$u->id.'/delete"><img class="icon" src="/i/trash.png" /></a>';

	$results_data[] = array(
                        'name' => '<a href="/user/'.$u->id.'" target="_new">'.$u->name.'</a>',
                        'email' => '<a href="/user/'.$u->id.'" target="_new">'.$u->email.'</a>',
                        'created_at' => array('display'=>date('F d, Y', strtotime($u->created_at)), 'created_date'=>$u->created_at),
                        'actions' => $action_icons
			);
        }
	$results = array(
            'data'=>$results_data,
            'recordsTotal'=> $total_users,
            'error'=> '',
        );


	$users= json_encode($results);
        return view('users.index', ['users'=>$users, 'activePage'=>'Users','titlePage'=>'Users']);
    }

    /**
     * Show the form for creating a new user
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('users.create');
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

    /**
     * Remove the specified user from storage
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User  $user)
    {
        $user->delete();

        return redirect()->route('user.index')->withStatus(__('User successfully deleted.'));
    }
}
