<?php

namespace App\Http\Controllers;
use App\Http\Requests\UserRequest;
use App\Http\Requests\CreateUserRequest;
use App\User;
use App\Role;
use Hash;
use Illuminate\Http\Request;
/**
 * @group User management
 *
 *
 */
class UserController extends Controller
{
    /**
     * Display a listing of the user.
     */
    public function index()
    {
        $users = User::paginate(10);
        return response()->json($users);
    }

    public function create()
    {
        //
    }

    /**
     * Store a newly created user in storage.
     * @bodyParam name string required The name of the user.
     * @bodyParam fullname string required The fullname of the user.
     * @bodyParam email string required The email of the user.
     * @bodyParam phone string required The phone of the user.
     * @bodyParam address string The address of the user.
     * @bodyParam password string required The password of the user.
     * @bodyParam password_confirmation string required The confirmed password.
     * @bodyParam roles array required The list id of the role.
     */
    public function store(CreateUserRequest $request)
    {
        $data = $request->only("name","fullname","email","phone","address","password");
        $data["password"] = Hash::make($data["password"]);
        $user = User::create($data);
        $user->roles()->attach(request('roles'));
        return response()->json([
            'message'=>'Created an user successfully']);
    }

    /**
     * Show the profile's information.
     */
    public function showCurrentInfoUser(Request $request)
    {
        $request->user()->roles;
        return response()->json($request->user());
    }


    /**
     * Display a user by id.
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        $user->roles;
        return response()->json($user);
    }

    public function edit(User $user){
        //
    }


    /**
     * Update the user by id.
     * @bodyParam fullname string required The fullname of the user.
     * @bodyParam email string required The email of the user.
     * @bodyParam phone string required The phone of the user.
     * @bodyParam address string The address of the user.
     * @bodyParam roles array required The string contains role's ID. Example: [1,2]
     */
    public function update(CreateUserRequest $request,$id)
    {
        $user = User::findOrFail($id);
        $user->update($request->only("fullname","email","phone","address"));
        $user->roles()->sync(request('roles'));
        return response()->json([
            'message' => 'Updated user successfully!'
        ], 200);
    }

    /**
     * Update the current profile.
     * Update the profile.
     * @bodyParam fullname string required The fullname of the user.
     * @bodyParam email string required The email of the user.
     * @bodyParam phone string required The phone of the user.
     * @bodyParam address string The address of the user.
     */
    public function updateCurrentProfile(UserRequest $request)
    {
        // Param need: 'fullname,email,phone,address'
        User::findOrFail($request->user()->id)->update($request->only("fullname","email","phone","address"));

        return response()->json([
            'message' => 'Information has been updated successfully!'
        ], 200);
    }

    /**
     * Delete the user
     *
     * @bodyParam userId array required list id of user. Example: [1,2,3,4,5]
     */
    public function destroy(CreateUserRequest $request)
    {
        $user_arr = request("userId");
        $exists = User::whereIn('id', $user_arr)->pluck('id');
        $notExists = collect($user_arr)->diff($exists);
        $idsNotFound = "";
        foreach ($notExists as $key => $value) {
            $idsNotFound .= $value.",";
        }
        if($notExists->isNotEmpty()){
            return response()->json([
                'message'=>'Not found id: '.substr($idsNotFound,0,strlen($idsNotFound)-1)],404);
        }
        User::destroy($exists);
        return response()->json([
           'message'=>'Deleted users successfully']);
    }
}
