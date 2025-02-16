<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Error en los datos ingresados',
                'errors' => $validator->errors()
            ], 400);
        }
        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado', 'status'=>'404'], 404);
        }

        if ($user->password !== $request->password) {
            return response()->json(['message' => 'Credenciales incorrectas', 'status'=>'401'], 401);
        }       

        // Revoca tokens previos y genera uno nuevo
        $user->tokens()->delete();
        // Crear token con vencimiento de 24 horas
        $token = $user->createToken('auth_token', ['*'], now()->addDay())->plainTextToken;
        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'status' => 200,
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_at' => now()->addDay(),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Sesión cerrada']);
    }

    public function userInfo(Request $request)
    {
        return response()->json([
            'user' => $request->user()
        ]);
    }

    public function register(Request $request)
    {
        // Validaciones
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'birthdate' => 'required|date',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en los datos ingresados',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verificar si el usuario tiene al menos 18 años
        $birthdate = Carbon::parse($request->birthdate);
        if ($birthdate->diffInYears(Carbon::now()) < 18) {
            return response()->json([
                'message' => 'Debes tener al menos 18 años para registrarte'
            ], 403);
        }

        // Crear usuario
        $user = User::create([
            'name' => $request->name,
            'birthdate' => $request->birthdate,
            'email' => $request->email,
            'password' => $request->password // Llega ya encriptada con SHA-256
        ]);

        // Generar token de autenticación
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Usuario registrado exitosamente',
            'token' => $token
        ], 201);
    }
}
