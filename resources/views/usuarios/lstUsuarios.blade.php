@extends('layouts.app')

@section('content')
<x-menu />

    <!-- Contenido principal -->
    <div id="main-content" class="p-4" style="margin-left: 80px; transition: margin-left 0.3s; width: calc(100% - 80px);">

        <div class="d-flex justify-content-between align-items-center">
            <h2>Lista de Usuarios</h2>
            <a href="{{ route('usuarios.create') }}" class="btn btn-primary">
                <i class="bi bi-person-plus"></i> Crear Usuario
            </a>
        </div>

        <div class="table-responsive mt-3">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($usuarios as $usuario)
                    <tr>
                        <td>{{ $usuario->id }}</td>
                        <td>{{ $usuario->name }}</td>
                        <td>{{ $usuario->email }}</td>
                        <td>{{ $usuario->rol->nombre ?? 'Sin rol' }}</td>
                        <td>
                            <span class="badge bg-{{ $usuario->activo ? 'success' : 'danger' }}">
                                {{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('usuarios.edit', $usuario->id) }}" class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil"></i> Actualizar
                            </a>
                            <a href="{{ route('usuarios.inactivar', $usuario->id) }}" class="btn btn-danger btn-sm">
                                <i class="bi bi-x-circle"></i> Inactivar
                            </a>
                            <a href="{{ route('usuarios.logs', $usuario->id) }}" class="btn btn-info btn-sm">
                                <i class="bi bi-list-check"></i> Ver Registro
                            </a>
                            <a href="{{ route('usuarios.permisos', $usuario->id) }}" class="btn btn-secondary btn-sm">
                                <i class="bi bi-shield-lock"></i> Permisos
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
