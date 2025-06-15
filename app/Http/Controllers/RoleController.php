<?php

namespace App\Http\Controllers;

use App\Role;
use App\Http\Requests\RoleRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Session;



class RoleController extends Controller
{
    /**
     * Display a listing of the Role
     *
     * @param  \App\Role  $model
     * @return \Illuminate\View\View
     */
     public function index(Role $model)
    {
        #return view('Role.index', ['Role' => $model->paginate(15),'activePage'=>'Role']);		## This page is as it is.

        return view('rolesmanagement', ['role'=>$model->all(), 'activePage'=>'role-management', 'titlePage' => 'Roles']);
    }

    /**
     * Show the form for creating a new Role
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('roles.form');
    }

    /**
     * Store a newly created Role in storage
     *
     * @param  \App\Http\Requests\RoleRequest  $request
     * @param  \App\Role  $Role
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(RoleRequest $request, Role $role)
    {
	$role->create($request->post());
	Session::flash('alert-success', 'Role successfully created.');
	return redirect()->route('roles.index')->withStatus(__('Role successfully created.'));
    }

    /**
     * Show the form for editing the specified Role
     *
     * @param  \App\Role  $Role
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
 	$role = Role::find($id);
        return view('roles.edit', compact('role'));
    }
    /**
     * Update the specified Role in storage
     *
     * @param  \App\Http\Requests\RoleRequest  $request
     * @param  \App\Role  $Role
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(RoleRequest $request, $id)
    {
	$role = Role::find($id);
	$role->name = $request->name;
	$role->save();

	Session::flash('alert-success', 'Role successfully updated.');
        return redirect()->route('roles.index')->withStatus(__('Role successfully updated.'));
    }

    /**
     * Remove the specified Role from storage
     *
     * @param  \App\Role  $Role
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
	$Role = \App\Role::findOrFail($request->role_id);
	if(!empty($request->delete_captcha) &&
                $request->delete_captcha == $request->delete_captcha){
        	$Role->delete();
		Session::flash('alert-success', 'Role successfully deleted.');
        	return redirect()->route('roles.index');
        }
	else{
		Session::flash('alert-danger', 'Please fill Captcha');
        	return redirect('/roles');
        }
    }
}
