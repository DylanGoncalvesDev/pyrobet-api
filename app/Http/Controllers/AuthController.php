<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
      #[OA\Post(
        path: "/api/register",
        tags: ["Autenticación"],
        summary: "Registrar un nuevo usuario competidor"
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name", "email", "password", "password_confirmation"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "Dylan Bet"),
                new OA\Property(property: "email", type: "string", format: "email", example: "dylan@example.com"),
                new OA\Property(property: "password", type: "string", format: "password", example: "password123"),
                new OA\Property(property: "password_confirmation", type: "string", format: "password", example: "password123")
            ]
        )
    )]
    #[OA\Response(response: 201, description: "Usuario registrado con éxito")]
    #[OA\Response(response: 422, description: "Formato inválido o correo duplicado")]
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

       $token = $user->createToken('PyroBetToken')->accessToken;

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

    #[OA\Post(
        path: "/api/login",
        tags: ["Autenticación"],
        summary: "Iniciar sesión y obtener Bearer Token"
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["email", "password"],
            properties: [
                new OA\Property(property: "email", type: "string", format: "email", example: "dylan@example.com"),
                new OA\Property(property: "password", type: "string", format: "password", example: "password123")
            ]
        )
    )]
    #[OA\Response(response: 200, description: "Login exitoso, retorna el token")]
    #[OA\Response(response: 401, description: "Credenciales incorrectas")]
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

     #[OA\Post(
        path: "/api/logout",
        tags: ["Autenticación"],
        summary: "Cerrar sesión destruyendo el token activo",
        security: [["bearerAuth" => []]]
    )]
    #[OA\Response(response: 200, description: "Sesión cerrada correctamente")]
    public function logout(Request $request): JsonResponse
    {
        $request->user()->token()->revoke();

        return response()->json([
           'message' => 'Successfully logged out'
        ], 200);
    }
}
