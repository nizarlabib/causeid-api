<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

     public function register(Request $request)
    {
        $cekUser = User::where('username', $request->username)->first();
        if ($cekUser) {
            return response()->json([
                'success' => true,
                'message' => 'Username already used',
            ]);
        }

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'user_firstname' => 'required|string|max:255',
            'user_lastname' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create([
            'username' => $request->username,
            'user_firstname' => $request->user_firstname,
            'user_lastname' => $request->user_lastname,
            'password' => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user','token'),201);
    }

     public function login(Request $request)
     {
         $request->validate([
             'username' => 'required',
             'password' => 'required',
         ]);
     
         $credentials = [
             'username' => $request->username,
             'password' => $request->password,
         ];

         $token = Auth::guard('api')->attempt($credentials);

         if (!$token) {
             return response()->json(['message' => 'Invalid credentials'], 401);
         }
         
         $user = Auth::guard('api')->user();
     
         return response()->json([
             'success' => true,
             'message' => 'Successfully logged in',
             'data' => [
                 'user' => $user,
                 'access_token' => $token,
                 'token_type' => 'Bearer',
             ],
         ]);
     }

    public function me()
    {
        $user = Auth::guard('api')->user();
        if(!$user){
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }

    public function refresh()
    {
        return $this->respondWithToken(Auth::guard()->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60
        ]);
    }

    public function logout()
    {
        Auth::guard('api')->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }
   
   
}