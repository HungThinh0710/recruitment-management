<?php

namespace App\Http\Controllers;

use App\Role;
use App\Permission;
use Illuminate\Http\Request;
use App\Http\Requests\RoleRequest;
use App\Http\Requests\DeleteRoleRequest;

/**
 * @group Role management
 *
 *
 */
class RoleController extends Controller
{
    /**
     * Display a listing of the role.
     * 5 rows/request
     *
     */
    public function index()
    {
        $roles = Role::paginate(5);
        return response()->json($roles);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Create a role.
     *
     * @bodyParam name string required name of role.
     * @bodyParam permissions string required list id of permission for the role. Example: 1,2
     */
    public function store(RoleRequest $request)
    {
        $role = new Role();
        $role->name = request('name');
        $permissions = request('permissions');
        $permission_arr = explode (",", $permissions);
        $role->save();
        foreach ($permission_arr as $permission) {
            $role->permissions()->attach($permission);
        }
        return response()->json([
            'message'=>'Created role successfully']);
    }

    /**
     * Show a role by ID.
     *
     */
    public function show($id)
    {
        $role = Role::findOrFail($id);
        $permissions = $role->permissions;
        return response()->json($role);
    }

    /**
     * Show the form for editing the specified resource.
     *
     */
    public function edit(Role $role)
    {
        //
    }

    /**
     * Update the role by ID.
     *
     * @bodyParam name string required name of role.
     * @bodyParam permissions string required list id of permission for the role. Example: 1,2,3,4,5
     */
    public function update(RoleRequest $request, $id)
    {
        Role::findOrFail($id)->update($request->only("name"));
        $permission_arr = explode (",", request("permissions"));
        Role::findOrFail($id)->permissions()->sync($permission_arr);
        return response()->json([
           'message'=>'Updated role successfully']);
    }

    /**
     * Delete the role.
     * @bodyParam roles string required list id of role want to delete. Example: 1,2,3,4,5
     */
    public function destroy(DeleteRoleRequest $request)
    {
        $role_arr = explode (",", request("roles"));
        Role::destroy($role_arr);
        return response()->json([
           'message'=>'Deleted roles successfully']);
    }
}
