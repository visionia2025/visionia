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
use App\Models\InicioSesion;
use Laravel\Sanctum\PersonalAccessToken;


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

        if (!$user) {
            return back()->withErrors(['email' => 'El email no se encuentra registrado en el sistema'])->withInput();
        }

        $failedAttempts = $this->countIntentos($user->id);
        if ($failedAttempts >= 3) {
            return back()->withErrors(['email' => 'Cuenta bloqueada por intentos fallidos. Intente más tarde.'])->withInput();
        }

        // Si la contraseña en la BD aún está en SHA-256, la convierte a bcrypt antes de comparar
        if (strlen($user->password) == 64 && ctype_xdigit($user->password)) {
            $user->password = Hash::make($user->password);
            $user->save(); // Guarda la contraseña en bcrypt
        }

        // Intentar autenticación con bcrypt
        if (Auth::attempt(['email' => $email, 'password' => $request->password])) {
            $this->registerSesion($user->id, $ip);
            return redirect()->intended('/usuarios');
        } else {
            $this->registerIntentFail($user->id, $ip);
            return back()->withErrors(['password' => 'Contraseña incorrecta'])->withInput();
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

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function registerWeb(Request $request)
    {
        // Validación de datos
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'birthdate' => 'required|date',
            'password' => 'required|string|min:6',
        ]);

        // Creación del usuario
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'birthdate' => $request->birthdate,
            'idRol' => 1,
            'password' => Hash::make($request->password),
        ]);
        return redirect()->route('login')->with('success', 'Registro exitoso. Ahora puedes iniciar sesión.');
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
        if ($failedAttempts >= 4) {
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

        $newToken = explode('|',$user->createToken('auth_token', ['*'], now()->addDay())->plainTextToken)[1];
        
        // Obtener el ID del token
        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'status' => 200,
            'token' => $newToken,
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
            'birthdate' => 'required|date|before_or_equal:' . now()->subYears(18)->format('Y-m-d'),
            'email' => 'required|string|email|max:255|unique:users',
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
            'password' => Hash::make($request->password)
        ]);

        // Generar token de autenticación
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Usuario registrado exitosamente',
            'status'=>200,
            'token' => $token
        ], 200);
    }

    public function update(Request $request)
    {
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

        // Validar los datos recibidos
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Error en los datos ingresados',
                'errors' => $validator->errors()
            ], 400);
        }

        // Actualizar los datos del usuario
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }
        
        if ($request->has('birthdate')) {
            $user->birthdate = $request->birthdate;
        }
        $user->save();

        return response()->json([
            'message' => 'Usuario actualizado correctamente',
            'status' => 200,
            'user' => $user
        ]);
    }
}
