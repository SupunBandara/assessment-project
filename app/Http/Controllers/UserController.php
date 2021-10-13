<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

use Illuminate\Support\Facades\Hash;
use Validator;
use Auth;
use Arr;

class UserController extends Controller
{
    
    /**
     * register user
     */
    public function create(Request $request)
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);

        $collection = collect();
        $collection->push($request->role);
        $user->role = $collection;

        $user->save();
        $user->refresh();
        if($user){
            return response()->json([
                'status' => 201,
                'message' => 'Created Successfully',
                'data' => $user
            ], 201);
        }else{
            return response()->json([
                'message' => 'Operation Failed'
            ]);
        }


    }


    /**
     * login user
     */
    public function login(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required|min:8'
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        if(!Auth::attempt($request->only('email', 'password'))){
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized'
            ], 401);
        };

        $user = User::where('email', $request->email)->select('id', 'name', 'email', 'role')->first();
        $token = $user->createToken('user-token', $user->role)->plainTextToken;
        Arr::add($user, 'token', $token);
        return response()->json($user);

        
    }



    /**
     * get logged user
     */
    public function getUser(Request $request)
    {
        return response()->json(['user'=>$request->user()]);
    }



     /**
     * logout user
     */
    public function logoutUser(Request $request)
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();

        return response()->json([
            'status'=> 200,
            'message' => 'User logout'
        ], 200);
    }


}
