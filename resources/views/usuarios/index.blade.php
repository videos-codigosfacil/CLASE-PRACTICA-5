@extends('layouts.app')

@section('title', 'Gestión de usuarios')

@section('styles')
<style>
    .encabezado-usuarios {
        background: linear-gradient(135deg, #7655c9, #a58ae8);
        color: white;
        border-radius: 18px;
        padding: 30px;
    }

    .tabla-usuarios th {
        color: #706782;
        font-size: 13px;
        white-space: nowrap;
    }

    .tabla-usuarios td {
        vertical-align: middle;
        padding: 14px 10px;
    }

    .btn-gia-secundario {
        border: 1px solid #7655c9;
        color: #7655c9;
        border-radius: 9px;
    }

    .btn-gia-secundario:hover {
        background: #7655c9;
        color: white;
    }

    .etiqueta-rol {
        background: #f1edfc;
        color: #6849b6;
        padding: 6px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
</style>
@endsection

@section('content')

<nav class="navbar bg-white shadow-sm px-4 py-3">
    <div class="container-fluid">
        <a class="navbar-brand marca fs-3" href="{{ route('dashboard') }}">
            GiaFlow.
        </a>

        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>
            Volver al panel
        </a>
    </div>
</nav>

<div class="container py-5">

    <div class="encabezado-usuarios mb-4">
        <h2 class="fw-bold mb-2">
            <i class="bi bi-people me-2"></i>
            Gestión de usuarios
        </h2>
        <p class="mb-0">
            Administra las cuentas, los datos y los roles de tu equipo.
        </p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Revisa los siguientes datos:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Formulario para registrar usuarios -->
    <div class="tarjeta p-4 mb-4">
        <h5 class="fw-bold mb-3">
            <i class="bi bi-person-plus me-2" style="color:#7655c9;"></i>
            Registrar nuevo usuario
        </h5>

        <form action="{{ route('usuarios.store') }}" method="POST">
            @csrf

            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Nombre completo</label>
                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        value="{{ old('name') }}"
                        placeholder="Nombre del usuario"
                        required
                    >
                </div>

                <div class="col-md-6">
                    <label class="form-label">Correo electrónico</label>
                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        value="{{ old('email') }}"
                        placeholder="correo@ejemplo.com"
                        required
                    >
                </div>

                <div class="col-md-4">
                    <label class="form-label">Contraseña</label>
                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        minlength="8"
                        required
                    >
                </div>

                <div class="col-md-4">
                    <label class="form-label">Confirmar contraseña</label>
                    <input
                        type="password"
                        name="password_confirmation"
                        class="form-control"
                        minlength="8"
                        required
                    >
                </div>

                <div class="col-md-4">
                    <label class="form-label">Rol del usuario</label>
                    <select name="role_id" class="form-select" required>
                        <option value="">Seleccionar rol</option>
                        @foreach($roles as $rol)
                            <option value="{{ $rol->id }}">
                                {{ $rol->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-gia">
                        <i class="bi bi-plus-circle me-2"></i>
                        Registrar usuario
                    </button>
                </div>

            </div>
        </form>
    </div>

    <!-- Tabla de usuarios -->
    <div class="tarjeta p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="fw-bold mb-1">Usuarios registrados</h5>
                <p class="texto-suave mb-0">
                    Total de usuarios: {{ $usuarios->count() }}
                </p>
            </div>

            <i class="bi bi-people fs-2" style="color:#7655c9;"></i>
        </div>

        <div class="table-responsive">
            <table class="table tabla-usuarios align-middle">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Contraseña nueva (opcional)</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($usuarios as $usuario)
                        <tr>
                            <td colspan="5" class="p-0 border-0">
                                <form
                                    id="editar-usuario-{{ $usuario->id }}"
                                    action="{{ route('usuarios.update', $usuario) }}"
                                    method="POST"
                                >
                                    @csrf
                                    @method('PUT')
                                </form>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <input
                                    form="editar-usuario-{{ $usuario->id }}"
                                    type="text"
                                    name="name"
                                    class="form-control"
                                    value="{{ $usuario->name }}"
                                    required
                                >
                                <small class="texto-suave">
                                    ID: {{ $usuario->id }}
                                </small>
                            </td>

                            <td>
                                <input
                                    form="editar-usuario-{{ $usuario->id }}"
                                    type="email"
                                    name="email"
                                    class="form-control"
                                    value="{{ $usuario->email }}"
                                    required
                                >
                            </td>

                            <td>
                                @if(auth()->id() === $usuario->id)
                                    <select class="form-select" disabled>
                                        <option>{{ $usuario->role?->nombre }}</option>
                                    </select>
                                    <input
                                        form="editar-usuario-{{ $usuario->id }}"
                                        type="hidden"
                                        name="role_id"
                                        value="{{ $usuario->role_id }}"
                                    >
                                    <small class="texto-suave">Tu cuenta</small>
                                @else
                                    <select
                                        form="editar-usuario-{{ $usuario->id }}"
                                        name="role_id"
                                        class="form-select"
                                        required
                                    >
                                        @foreach($roles as $rol)
                                            <option
                                                value="{{ $rol->id }}"
                                                @selected($usuario->role_id == $rol->id)
                                            >
                                                {{ $rol->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                            </td>

                            <td>
                                <input
                                    form="editar-usuario-{{ $usuario->id }}"
                                    type="password"
                                    name="password"
                                    class="form-control"
                                    placeholder="Dejar vacío"
                                    minlength="8"
                                >

                                <input
                                    form="editar-usuario-{{ $usuario->id }}"
                                    type="password"
                                    name="password_confirmation"
                                    class="form-control mt-2"
                                    placeholder="Confirmar nueva"
                                >
                            </td>

                            <td>
                                <div class="d-flex flex-column gap-2">
                                    <button
                                        form="editar-usuario-{{ $usuario->id }}"
                                        type="submit"
                                        class="btn btn-gia btn-sm"
                                    >
                                        <i class="bi bi-save me-1"></i>
                                        Guardar
                                    </button>

                                    @if(auth()->id() !== $usuario->id)
                                        <form
                                            action="{{ route('usuarios.destroy', $usuario) }}"
                                            method="POST"
                                            onsubmit="return confirm('¿Estás seguro de eliminar este usuario?')"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn btn-outline-danger btn-sm w-100"
                                            >
                                                <i class="bi bi-trash me-1"></i>
                                                Eliminar
                                            </button>
                                        </form>
                                    @else
                                        <span class="badge text-bg-secondary">
                                            Sesión actual
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 texto-suave">
                                Todavía no hay usuarios registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="text-center mt-4 texto-suave">
        GiaFlow. — Organiza. Asigna. Avanza.
    </div>

</div>

@endsection
