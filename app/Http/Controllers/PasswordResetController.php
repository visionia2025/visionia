<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\PasswordReset;
use App\Models\PasswordChange;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    /**
     * Generar un token de recuperación.
     */
    public function requestReset(Request $request)
    {
        // Validar entrada
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Correo no válido','status'=>400], 400);
        }

        // Eliminar tokens previos
        PasswordReset::where('email', $request->email)->delete();

        // Generar token
        $token = bin2hex(random_bytes(32)); // Token aleatorio

        // Guardar en BD con 30 min de validez
        PasswordReset::create([
            'email' => $request->email,
            'token' => $token,
            'expires_at' => Carbon::now()->addMinutes(30)
        ]);

        return response()->json([
            'status' => 'success',
            'status'=>200,
            'message' => 'Token generado',
            'token' => $token
        ], 200);
    }

    /**
     * Resetear la contraseña.
     */
    public function resetPassword(Request $request)
    {
        // Validar entrada
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Datos inválidos','status'=>400], 400);
        }


        // Buscar el token en BD
        $passwordReset = PasswordReset::where('token', $request->token)
            ->first();

        if (!$passwordReset || $passwordReset->expires_at < Carbon::now()) {
            return response()->json(['status' => 'error', 'message' => 'Token inválido o expirado','status'=>401], 401);
        }

        if ($passwordReset && $passwordReset->email != $request->email) {
            return response()->json(['status' => 'error', 'message' => 'Token no es válido para el correo indicado','status'=>402], 402);
        }
               
       
        // Actualizar contraseña (ya viene en SHA-256)
        $user = User::where('email', $request->email)->first();
        $user->password = $request->password;
        $user->save();

        // Guardar en historial
        PasswordChange::create([
            'user_id' => $user->id,
            'changed_at' => now(),
            'ip_address' => $request->ip()
        ]);

        // Eliminar el token usado
        $passwordReset->delete();

        return response()->json(['status' => 'success', 'message' => 'Contraseña actualizada','status'=>200], 200);
    }
}

