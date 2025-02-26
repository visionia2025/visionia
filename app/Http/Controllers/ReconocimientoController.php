<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Laravel\Sanctum\PersonalAccessToken;
use App\Models\UserService;
use App\Models\TokenServices;
use App\Models\Reconocimiento;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class ReconocimientoController extends Controller
{
    public function registrarReconocimiento(Request $request) {

        // Validar los tokens en la cabecera
        $tokenHeader = $request->header('Authorization');
        $authToken = str_replace('Bearer ', '', $tokenHeader);
        $secondToken = $request->header('Second-Authorization');

        if (!$authToken || !$secondToken) {
            return response()->json(['message' => 'Ambos tokens son requeridos', 'status' => 401], 401);
        }
        // Verificar el primer token
        $validFirstToken = TokenServices::where('token', hash('sha256', $authToken))
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$validFirstToken) {
            return response()->json(['message' => 'Primer token no válido o expirado', 'status' => 402], 402);
        }

        // Verificar el segundo token (login)
        $validSecondToken = PersonalAccessToken::where('token', hash('sha256',$secondToken))
            ->first();

        if (!$validSecondToken) {
            return response()->json(['message' => 'Segundo token no válido o expirado', 'status' => 403], 403);
        }

        // Obtener el ID del usuario
        $user = User::find($validSecondToken->tokenable_id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado', 'status' => 404], 404);
        }

        $validator = Validator::make($request->all(), [
            'resultado' => 'required|string',
            'tipoReconocimiento' => 'required|exists:tiporeconocimiento,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Error en los datos ingresados',
                'errors' => $validator->errors()
            ], 400);
        }

        // Guardar reconocimiento
        $reconocimiento = Reconocimiento::create([
            'userId' => $user->id,
            'tipoReconocimientoId' => $request->tipoReconocimiento,
            'resultado' => $request->resultado,
            'fecha' => now()
        ]);

        return response()->json([
            'message' => 'Registro exitoso',
            'status' => 200
        ]);

    }
}
