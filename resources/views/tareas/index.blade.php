@extends('layouts.app')

@section('title', 'Gestión de tareas')

@section('styles')
<style>
    .encabezado-tareas {
        background: linear-gradient(135deg, #7655c9, #a58ae8);
        color: white;
        border-radius: 18px;
        padding: 30px;
    }

    .estado-pendiente {
        background: #fff2d8;
        color: #9b6b12;
    }

    .estado-proceso {
        background: #e5efff;
        color: #3566aa;
    }

    .estado-completada {
        background: #e4f6eb;
        color: #21864b;
    }

    .etiqueta {
        display: inline-block;
        padding: 6px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .tarea-card {
        border: 1px solid #eeeaf7;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 16px;
        background: white;
    }

    .prioridad-alta {
        color: #c43c4b;
    }

    .prioridad-media {
        color: #a87916;
    }

    .prioridad-baja {
        color: #21864b;
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

    <div class="encabezado-tareas mb-4">
        <h2 class="fw-bold mb-2">
            <i class="bi bi-list-check me-2"></i>
            Gestión de tareas
        </h2>

        <p class="mb-0">
            Organiza actividades, asigna responsables y controla su cumplimiento.
        </p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
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

    {{-- Formulario para administradores y gerentes --}}
    @if(in_array($rol, ['Administrador', 'Gerente de proyecto']))

        <div class="tarjeta p-4 mb-4">

            <h5 class="fw-bold mb-3">
                <i class="bi bi-plus-circle me-2" style="color:#7655c9;"></i>
                Registrar nueva tarea
            </h5>

            <form action="{{ route('tareas.store') }}" method="POST">
                @csrf

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Nombre de la tarea</label>
                        <input
                            type="text"
                            name="titulo"
                            class="form-control"
                            value="{{ old('titulo') }}"
                            placeholder="Ej. Diseñar la página principal"
                            required
                        >
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Proyecto</label>
                        <select name="project_id" class="form-select" required>
                            <option value="">Seleccionar proyecto</option>

                            @foreach($proyectos as $proyecto)
                                <option
                                    value="{{ $proyecto->id }}"
                                    @selected(old('project_id') == $proyecto->id)
                                >
                                    {{ $proyecto->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Descripción</label>
                        <textarea
                            name="descripcion"
                            class="form-control"
                            rows="3"
                            placeholder="Describe las actividades que se deben realizar..."
                        >{{ old('descripcion') }}</textarea>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Miembro responsable</label>
                        <select name="assigned_user_id" class="form-select" required>
                            <option value="">Seleccionar miembro</option>

                            @foreach($miembros as $miembro)
                                <option
                                    value="{{ $miembro->id }}"
                                    @selected(old('assigned_user_id') == $miembro->id)
                                >
                                    {{ $miembro->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-select" required>
                            <option value="Pendiente" @selected(old('estado', 'Pendiente') == 'Pendiente')>
                                Pendiente
                            </option>
                            <option value="En proceso" @selected(old('estado') == 'En proceso')>
                                En proceso
                            </option>
                            <option value="Completada" @selected(old('estado') == 'Completada')>
                                Completada
                            </option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Prioridad</label>
                        <select name="prioridad" class="form-select" required>
                            <option value="Baja" @selected(old('prioridad') == 'Baja')>
                                Baja
                            </option>
                            <option value="Media" @selected(old('prioridad', 'Media') == 'Media')>
                                Media
                            </option>
                            <option value="Alta" @selected(old('prioridad') == 'Alta')>
                                Alta
                            </option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Fecha de vencimiento</label>
                        <input
                            type="date"
                            name="fecha_vencimiento"
                            class="form-control"
                            value="{{ old('fecha_vencimiento') }}"
                        >
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-gia">
                            <i class="bi bi-plus-circle me-2"></i>
                            Crear tarea
                        </button>
                    </div>

                </div>
            </form>
        </div>
    @endif

    {{-- Lista de tareas --}}
    <div class="tarjeta p-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="fw-bold mb-1">
                    {{ $rol === 'Miembro del equipo' ? 'Mis tareas asignadas' : 'Tareas registradas' }}
                </h5>

                <p class="texto-suave mb-0">
                    Total de tareas: {{ $tareas->count() }}
                </p>
            </div>

            <i class="bi bi-clipboard-check fs-2" style="color:#7655c9;"></i>
        </div>

        @forelse($tareas as $tarea)

            @php
                $claseEstado = match($tarea->estado) {
                    'Pendiente' => 'estado-pendiente',
                    'En proceso' => 'estado-proceso',
                    'Completada' => 'estado-completada',
                    default => 'estado-pendiente',
                };

                $clasePrioridad = match($tarea->prioridad) {
                    'Alta' => 'prioridad-alta',
                    'Media' => 'prioridad-media',
                    'Baja' => 'prioridad-baja',
                    default => '',
                };

                $puedeEditar = in_array($rol, ['Administrador', 'Gerente de proyecto']);
            @endphp

            <div class="tarea-card">

                @if($puedeEditar)
                    <form
                        id="editar-tarea-{{ $tarea->id }}"
                        action="{{ route('tareas.update', $tarea) }}"
                        method="POST"
                    >
                        @csrf
                        @method('PUT')
                    </form>
                @endif

                <div class="row g-3">

                    {{-- Nombre de la tarea --}}
                    <div class="col-lg-6">
                        <label class="form-label small texto-suave">
                            Nombre de la tarea
                        </label>

                        @if($puedeEditar)
                            <input
                                form="editar-tarea-{{ $tarea->id }}"
                                type="text"
                                name="titulo"
                                class="form-control fw-bold"
                                value="{{ $tarea->titulo }}"
                                required
                            >
                        @else
                            <h6 class="fw-bold">{{ $tarea->titulo }}</h6>
                        @endif

                        <small class="texto-suave">
                            Tarea #{{ $tarea->id }}
                        </small>
                    </div>

                    {{-- Proyecto --}}
                    <div class="col-lg-6">
                        <label class="form-label small texto-suave">
                            Proyecto
                        </label>

                        @if($puedeEditar)
                            <select
                                form="editar-tarea-{{ $tarea->id }}"
                                name="project_id"
                                class="form-select"
                                required
                            >
                                @foreach($proyectos as $proyecto)
                                    <option
                                        value="{{ $proyecto->id }}"
                                        @selected($tarea->project_id == $proyecto->id)
                                    >
                                        {{ $proyecto->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <p class="mb-0">
                                {{ $tarea->project?->nombre ?? 'Sin proyecto' }}
                            </p>
                        @endif
                    </div>

                    {{-- Descripción --}}
                    <div class="col-12">
                        <label class="form-label small texto-suave">
                            Descripción
                        </label>

                        @if($puedeEditar)
                            <textarea
                                form="editar-tarea-{{ $tarea->id }}"
                                name="descripcion"
                                class="form-control"
                                rows="2"
                            >{{ $tarea->descripcion }}</textarea>
                        @else
                            <p class="mb-0">
                                {{ $tarea->descripcion ?: 'Sin descripción' }}
                            </p>
                        @endif
                    </div>

                    {{-- Responsable --}}
                    <div class="col-md-4">
                        <label class="form-label small texto-suave">
                            Responsable
                        </label>

                        @if($puedeEditar)
                            <select
                                form="editar-tarea-{{ $tarea->id }}"
                                name="assigned_user_id"
                                class="form-select"
                                required
                            >
                                @foreach($miembros as $miembro)
                                    <option
                                        value="{{ $miembro->id }}"
                                        @selected($tarea->assigned_user_id == $miembro->id)
                                    >
                                        {{ $miembro->name }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <p class="mb-0">
                                {{ $tarea->assignedUser?->name ?? 'Sin responsable' }}
                            </p>
                        @endif
                    </div>

                    {{-- Estado --}}
                    <div class="col-md-4">
                        <label class="form-label small texto-suave">
                            Estado
                        </label>

                        @if($puedeEditar)
                            <select
                                form="editar-tarea-{{ $tarea->id }}"
                                name="estado"
                                class="form-select"
                                required
                            >
                                <option value="Pendiente" @selected($tarea->estado === 'Pendiente')>
                                    Pendiente
                                </option>
                                <option value="En proceso" @selected($tarea->estado === 'En proceso')>
                                    En proceso
                                </option>
                                <option value="Completada" @selected($tarea->estado === 'Completada')>
                                    Completada
                                </option>
                            </select>
                        @else
                            <form
                                action="{{ route('tareas.estado', $tarea) }}"
                                method="POST"
                            >
                                @csrf
                                @method('PATCH')

                                <select
                                    name="estado"
                                    class="form-select mb-2"
                                    required
                                >
                                    <option value="Pendiente" @selected($tarea->estado === 'Pendiente')>
                                        Pendiente
                                    </option>
                                    <option value="En proceso" @selected($tarea->estado === 'En proceso')>
                                        En proceso
                                    </option>
                                    <option value="Completada" @selected($tarea->estado === 'Completada')>
                                        Completada
                                    </option>
                                </select>

                                <button class="btn btn-gia btn-sm" type="submit">
                                    Actualizar estado
                                </button>
                            </form>
                        @endif
                    </div>

                    {{-- Prioridad --}}
                    <div class="col-md-4">
                        <label class="form-label small texto-suave">
                            Prioridad
                        </label>

                        @if($puedeEditar)
                            <select
                                form="editar-tarea-{{ $tarea->id }}"
                                name="prioridad"
                                class="form-select"
                                required
                            >
                                <option value="Baja" @selected($tarea->prioridad === 'Baja')>
                                    Baja
                                </option>
                                <option value="Media" @selected($tarea->prioridad === 'Media')>
                                    Media
                                </option>
                                <option value="Alta" @selected($tarea->prioridad === 'Alta')>
                                    Alta
                                </option>
                            </select>
                        @else
                            <p class="fw-semibold {{ $clasePrioridad }} mb-0">
                                {{ $tarea->prioridad }}
                            </p>
                        @endif
                    </div>

                    {{-- Fecha de vencimiento --}}
                    <div class="col-md-6">
                        <label class="form-label small texto-suave">
                            Fecha de vencimiento
                        </label>

                        @if($puedeEditar)
                            <input
                                form="editar-tarea-{{ $tarea->id }}"
                                type="date"
                                name="fecha_vencimiento"
                                class="form-control"
                                value="{{ $tarea->getRawOriginal('fecha_vencimiento') ?? '' }}"
                            >
                        @else
                            <p class="mb-0">
                                {{ $tarea->fecha_vencimiento
                                    ? \Illuminate\Support\Carbon::parse($tarea->fecha_vencimiento)->format('d/m/Y')
                                    : 'Sin fecha definida' }}
                            </p>
                        @endif
                    </div>

                    {{-- Acciones --}}
                    <div class="col-12 d-flex flex-wrap gap-2 align-items-center">

                        @if($puedeEditar)

                            <button
                                form="editar-tarea-{{ $tarea->id }}"
                                type="submit"
                                class="btn btn-gia btn-sm"
                            >
                                <i class="bi bi-save me-1"></i>
                                Guardar cambios
                            </button>

                            <form
                                action="{{ route('tareas.destroy', $tarea) }}"
                                method="POST"
                                onsubmit="return confirm('¿Deseas eliminar esta tarea?')"
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="btn btn-outline-danger btn-sm"
                                >
                                    <i class="bi bi-trash me-1"></i>
                                    Eliminar
                                </button>
                            </form>

                        @else

                            <span class="etiqueta {{ $claseEstado }}">
                                {{ $tarea->estado }}
                            </span>

                        @endif

                    </div>

                </div>
            </div>

        @empty

            <div class="text-center py-5">
                <i class="bi bi-clipboard2 fs-1" style="color:#a58ae8;"></i>

                <h6 class="fw-bold mt-3">Todavía no hay tareas</h6>

                <p class="texto-suave mb-0">
                    Cuando se asignen tareas, aparecerán en esta sección.
                </p>
            </div>

        @endforelse

    </div>

    <div class="text-center mt-4 texto-suave">
        GiaFlow. — Organiza. Asigna. Avanza.
    </div>

</div>

@endsection
