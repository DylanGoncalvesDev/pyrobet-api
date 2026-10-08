<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    //
    public function register(Request $request): JsonResponse 
    {
       $validatedData = $request->validate([
         'name' => [
           'required',
           'string',
           'max:255',
           'regex:/^[\pL0-9\s]+$/u'
         ],
         'email' => 'required|string|email|max:255|unique:users',
         'password' => 'required|string|min:8|confirmed'
       ]);

       $user = User::create([
         'name' => $validatedData['name'],
         'email' => $validatedData['email'] ,
         'password' => Hash::make($validatedData['password'])
       ]);

       $token = $user->createToken('PersonalAccessToken')->accessToken;

       return response()->json([
         'message' => 'Authorized: successful registration',
         'user' => [
           'id' => $user->id,
           'name' => $user->name,
           'email' => $user->email,
           'role' => $user->role,
         ],
         'token' => $token
       ], 201);
    }

    public function login(Request $request): JsonResponse
    {
       $validatedData = $request->validate([
         'email' => 'required|string|email|max:255',
         'password' => 'required|string|min:8',
       ]);

       if(!Auth::attempt($validatedData))
       {
         return response()->json([
            'message' => 'Unauthorized: Invalid Credentials'
         ], 401);
       } 

       $user = Auth::user();

       $token = $user->createToken('PyroBetToken')->accessToken;

       return response()->json([
          'message' => 'Authorized: successful login',
          'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
          ],
          'token' => $token
       ], 200);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->token()->revoke();

        return response()->json([
           'message' => 'Successfully logged out'
        ], 200);
    }
}
