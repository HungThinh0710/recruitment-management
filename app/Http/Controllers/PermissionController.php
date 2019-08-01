<?php

namespace App\Http\Controllers;

use App\Permission;
use Illuminate\Http\Request;

/**
 * @group Permission management
 *
 *
 */
class PermissionController extends Controller
{
    /**
     * Display a listing of the permission
     * @bodyParam paginate numeric The count of item you want to paginate.
     */
    public function index(Request $request)
    {
        $this->validate($request,['paginate' => 'numeric']);
        $count = $request->input("paginate")?$request->input("paginate"):0;
        if ($count!=0)
            return response()->json(Permission::paginate($count));
        else return response()->json(Permission::all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function show(Permission $permission)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function edit(Permission $permission)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Permission $permission)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function destroy(Permission $permission)
    {
        //
    }
}
