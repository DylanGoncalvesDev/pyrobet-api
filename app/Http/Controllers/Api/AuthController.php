<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Models\User;
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
         'message' => 'Usuario registrado con éxito',
         'user' => [
           'id' => $user->id,
           'name' => $user->name,
           'email' => $user->email,
           'role' => $user->role,
         ],
         'token' => $token
       ], 201);
    }
}
