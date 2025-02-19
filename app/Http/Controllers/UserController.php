<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function lstUsuarios ()
    {
        $usuarios = User::all(); // Obtener todos los usuarios
        return view('usuarios.lstUsuarios', compact('usuarios'));
    }

    public function create()
    {
        return view('usuarios.create');
    }

    public function edit($id)
    {
        $usuario = User::findOrFail($id);
        return view('usuarios.edit', compact('usuario'));
    }

    public function permisos($id)
    {
        $usuario = User::findOrFail($id);
        return view('usuarios.permisos', compact('usuario'));
    }

    public function inactivar($id)
    {
        $usuario = User::findOrFail($id);
        $usuario->update(['activo' => false]);
        return redirect()->route('usuarios.lstUsuarios')->with('success', 'Usuario inactivado');
    }

    public function logs($id)
    {
        // Lógica para mostrar el registro de inicios de sesión
        return view('usuarios.logs', compact('id'));
    }
}
