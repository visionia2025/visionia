<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\User;
use Carbon\Carbon;
use App\Models\UserService;
use App\Models\TokenServices;
use App\Models\UsuarioIntento;

class AuthController extends Controller
{
/**
 * Inicio de sesión para la parte web, que sera usada por administradores*/
    public function loginWeb(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $email = $request->email;
        $ip = $request->ip();

        $user = User::where('email', $email)->first();
      
        if($user){
            $failedAttempts = $this->countIntentos($user->id);
            if ($failedAttempts >= 3) {
                return back()->withErrors(['email' => 'Cuenta bloqueada por intentos fallidos. Intente más tarde.'])->withInput();
            }
            if (hash('sha256', $request->password) == $user->password) {
                $this->registerSesion($user->id,$ip);
                return redirect()->intended('/dashboard');
            }else{
                $this->registerIntentFail($user->id,$ip);
            }    
        }else{
            return back()->withErrors(['email' => 'El email no se encuentra registrado en el sistema'])->withInput();
        }
    }
/**
 * Contar los intentos de inicio de sesión
 */
    public function countIntentos($id){
        return  UsuarioIntento::where('userId', $id)
        ->where('fecha', '>=', now()->subMinutes(10))
        ->count();
    }
/**
 * Registrar los intentos fallidos de inicio de sesión
 */
    public function registerIntentFail($user_id,$ip){
        return UsuarioIntento::create([
            'userId' => $user_id,
            'ip' => $ip,
            'fecha' => now()
        ]);
    }
/**
 * Registrar el inicio de sesión exitoso
 */
    public function registerSesion($user_id,$ip){
       return InicioSesion::create([
            'userId' => $user_id,
            'ip' => $ip,
            'fecha' => now()
        ]);
    }

/**
 * Funcionalidad para finalizar la sesión en la parte web
 */
    public function logoutWeb()
    {
        Auth::logout();
        return redirect('/login')->with('success', 'Sesión cerrada correctamente.');
    }

    public function generateToken(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string'
        ]);

        $user = UserService::where('login', $request->login)->first();
             
        if (!$user || hash('sha256', $request->password) !== $user->password) {
            return response()->json(['message' => 'Credenciales incorrectas','status'=>401], 401);
        }

        if ($user->status !== 1) {
            return response()->json(['message' => 'Usuario inactivo','status'=>403], 403);
        }
        // Verificar si el usuario ya tiene un token activo
        $existingToken = TokenServices::where('user_id', $user->id)
        ->where('expires_at', '>', Carbon::now())
        ->first();

        if ($existingToken) {
            // Eliminar el token anterior
            $existingToken->delete();
        }

        // Generar token
        $token = Str::random(60);
        $expiresAt = Carbon::now()->addHours(24);
        // Guardar en la base de datos
        TokenServices::create([
            'user_id' => $user->id,
            'token' => hash('sha256', $token),
            'expires_at' => $expiresAt
        ]);

        // Responder con el token en la cabecera
        return response()->json(['message' => 'Token generado exitosamente','status'=>200,'token'=>$token]);
    }

    public function login(Request $request)
    {
        // Validar token en cabecera
        $tokenHeader = $request->header('Authorization');
        $token = str_replace('Bearer ', '', $tokenHeader);

        $validToken = TokenServices::where('token', hash('sha256', $token))
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$validToken) {
            return response()->json(['message' => 'Token no válido o expirado', 'status'=>401], 401);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Error en los datos ingresados',
                'errors' => $validator->errors()
            ], 400);
        }
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado', 'status'=>'404'], 404);
        }
        
        $failedAttempts = $this->countIntentos($user->id);
        if ($failedAttempts >= 3) {
            return response()->json(['message' => 'Cuenta bloqueada por intentos fallidos. Intente más tarde.', 'status'=>'405'], 405);
        }
        $ip = '127.0.0.1';
        if ($user->password !== $request->password) {
            $this->registerIntentFail($user->id,$ip);
            //si genero error a registrar los intentos fallidos, si tiene 3 intentos fallidos inactivar la sesión por 10 minutos
            return response()->json(['message' => 'Credenciales incorrectas', 'status'=>'402'], 402);
        }       

        //registrar en log el inicio de sesión
        $this->registerSesion($user->id,$ip);
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
        // Validar token en cabecera
        $tokenHeader = $request->header('Authorization');
        $token = str_replace('Bearer ', '', $tokenHeader);

        $validToken = TokenServices::where('token', hash('sha256', $token))
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$validToken) {
            return response()->json(['message' => 'Token no válido o expirado', 'status'=>401], 401);
        }

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
                'status'=>402,
                'errors' => $validator->errors()
            ], 402);
        }

        // Verificar si el usuario tiene al menos 18 años
        $birthdate = Carbon::parse($request->birthdate);
        if ($birthdate->diffInYears(Carbon::now()) < 18) {
            return response()->json([
                'message' => 'Debes tener al menos 18 años para registrarte',
                'status'=>403
            ], 403);
        }

        // Crear usuario
        $user = User::create([
            'name' => $request->name,
            'birthdate' => $request->birthdate,
            'email' => $request->email,
            'idRol' => 1,
            'password' => $request->password // Llega ya encriptada con SHA-256
        ]);

        // Generar token de autenticación
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Usuario registrado exitosamente',
            'status'=>200,
            'token' => $token
        ], 200);
    }
}
