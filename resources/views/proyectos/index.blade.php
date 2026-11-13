@extends('layouts.app')

@section('title', 'Gestión de proyectos')

@section('styles')
<style>
    .encabezado-proyectos {
        background: linear-gradient(135deg, #7655c9, #a58ae8);
        color: white;
        border-radius: 18px;
        padding: 30px;
    }

    .tabla-proyectos th {
        color: #706782;
        font-size: 13px;
        white-space: nowrap;
    }

    .tabla-proyectos td {
        vertical-align: middle;
        padding: 14px 10px;
    }

    .estado-activo {
        background: #e4f6eb;
        color: #21864b;
    }

    .estado-pausa {
        background: #fff2d8;
        color: #9b6b12;
    }

    .estado-finalizado {
        background: #e8e9ef;
        color: #55596b;
    }

    .etiqueta-estado {
        display: inline-block;
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

    <div class="encabezado-proyectos mb-4">
        <h2 class="fw-bold mb-2">
            <i class="bi bi-folder2-open me-2"></i>
            Gestión de proyectos
        </h2>
        <p class="mb-0">
            Organiza proyectos, asigna responsables y controla sus avances.
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

    @if(in_array($rol, ['Administrador', 'Gerente de proyecto']))

        <div class="tarjeta p-4 mb-4">
            <h5 class="fw-bold mb-3">
                <i class="bi bi-plus-circle me-2" style="color:#7655c9;"></i>
                Registrar nuevo proyecto
            </h5>

            <form action="{{ route('proyectos.store') }}" method="POST">
                @csrf

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Nombre del proyecto</label>
                        <input
                            type="text"
                            name="nombre"
                            class="form-control"
                            value="{{ old('nombre') }}"
                            placeholder="Ej. Desarrollo de GiaFlow"
                            required
                        >
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-select" required>
                            <option value="Activo">Activo</option>
                            <option value="En pausa">En pausa</option>
                            <option value="Finalizado">Finalizado</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Descripción</label>
                        <textarea
                            name="descripcion"
                            class="form-control"
                            rows="3"
                            placeholder="Describe brevemente el proyecto..."
                        >{{ old('descripcion') }}</textarea>
                    </div>

                    @if($rol === 'Administrador')
                        <div class="col-md-4">
                            <label class="form-label">Gerente responsable</label>
                            <select name="gerente_id" class="form-select" required>
                                <option value="">Seleccionar gerente</option>
                                @foreach($gerentes as $gerente)
                                    <option
                                        value="{{ $gerente->id }}"
                                        @selected(old('gerente_id') == $gerente->id)
                                    >
                                        {{ $gerente->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <div class="col-md-4">
                            <label class="form-label">Gerente responsable</label>
                            <input
                                type="text"
                                class="form-control"
                                value="{{ auth()->user()->name }}"
                                disabled
                            >
                            <small class="texto-suave">
                                Tú serás el gerente del proyecto.
                            </small>
                        </div>
                    @endif

                    <div class="col-md-4">
                        <label class="form-label">Fecha de inicio</label>
                        <input
                            type="date"
                            name="fecha_inicio"
                            class="form-control"
                            value="{{ old('fecha_inicio') }}"
                        >
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Fecha de finalización</label>
                        <input
                            type="date"
                            name="fecha_fin"
                            class="form-control"
                            value="{{ old('fecha_fin') }}"
                        >
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-gia">
                            <i class="bi bi-plus-circle me-2"></i>
                            Crear proyecto
                        </button>
                    </div>

                </div>
            </form>
        </div>

    @endif

    <div class="tarjeta p-4">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="fw-bold mb-1">Proyectos registrados</h5>
                <p class="texto-suave mb-0">
                    Total de proyectos: {{ $proyectos->count() }}
                </p>
            </div>

            <i class="bi bi-folder2-open fs-2" style="color:#7655c9;"></i>
        </div>

        @forelse($proyectos as $proyecto)

            @php
                $puedeEditar = $rol === 'Administrador' ||
                    ($rol === 'Gerente de proyecto' &&
                    $proyecto->gerente_id === auth()->id());

                $claseEstado = match($proyecto->estado) {
                    'Activo' => 'estado-activo',
                    'En pausa' => 'estado-pausa',
                    'Finalizado' => 'estado-finalizado',
                    default => 'estado-finalizado',
                };
            @endphp

            <div class="border rounded-4 p-3 mb-3">

                @if($puedeEditar)

                    <form
                        id="editar-proyecto-{{ $proyecto->id }}"
                        action="{{ route('proyectos.update', $proyecto) }}"
                        method="POST"
                    >
                        @csrf
                        @method('PUT')
                    </form>

                @endif

                <div class="row g-3 align-items-start">

                    <div class="col-lg-4">
                        @if($puedeEditar)
                            <label class="form-label small texto-suave">
                                Nombre del proyecto
                            </label>

                            <input
                                form="editar-proyecto-{{ $proyecto->id }}"
                                type="text"
                                name="nombre"
                                class="form-control fw-bold"
                                value="{{ $proyecto->nombre }}"
                                required
                            >
                        @else
                            <h6 class="fw-bold mb-2">
                                {{ $proyecto->nombre }}
                            </h6>
                        @endif

                        <small class="texto-suave">
                            Proyecto #{{ $proyecto->id }}
                        </small>
                    </div>

                    <div class="col-lg-4">
                        <label class="form-label small texto-suave">
                            Descripción
                        </label>

                        @if($puedeEditar)
                            <textarea
                                form="editar-proyecto-{{ $proyecto->id }}"
                                name="descripcion"
                                class="form-control"
                                rows="2"
                            >{{ $proyecto->descripcion }}</textarea>
                        @else
                            <p class="mb-0">
                                {{ $proyecto->descripcion ?: 'Sin descripción' }}
                            </p>
                        @endif
                    </div>

                    <div class="col-lg-4">
                        <label class="form-label small texto-suave">
                            Estado
                        </label>

                        @if($puedeEditar)
                            <select
                                form="editar-proyecto-{{ $proyecto->id }}"
                                name="estado"
                                class="form-select"
                                required
                            >
                                <option value="Activo"
                                    @selected($proyecto->estado === 'Activo')>
                                    Activo
                                </option>

                                <option value="En pausa"
                                    @selected($proyecto->estado === 'En pausa')>
                                    En pausa
                                </option>

                                <option value="Finalizado"
                                    @selected($proyecto->estado === 'Finalizado')>
                                    Finalizado
                                </option>
                            </select>
                        @else
                            <span class="etiqueta-estado {{ $claseEstado }}">
                                {{ $proyecto->estado }}
                            </span>
                        @endif
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small texto-suave">
                            Gerente responsable
                        </label>

                        @if($puedeEditar && $rol === 'Administrador')
                            <select
                                form="editar-proyecto-{{ $proyecto->id }}"
                                name="gerente_id"
                                class="form-select"
                                required
                            >
                                @foreach($gerentes as $gerente)
                                    <option
                                        value="{{ $gerente->id }}"
                                        @selected($proyecto->gerente_id == $gerente->id)
                                    >
                                        {{ $gerente->name }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <p class="mb-0">
                                {{ $proyecto->gerente?->name ?? 'Sin gerente' }}
                            </p>
                        @endif
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small texto-suave">
                            Fecha de inicio
                        </label>

                        @if($puedeEditar)
                            <input
                                form="editar-proyecto-{{ $proyecto->id }}"
                                type="date"
                                name="fecha_inicio"
                                class="form-control"
                                value="{{ $proyecto->fecha_inicio }}"
                            >
                        @else
                            <p class="mb-0">
                                {{ $proyecto->fecha_inicio ?? 'No definida' }}
                            </p>
                        @endif
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small texto-suave">
                            Fecha de finalización
                        </label>

                        @if($puedeEditar)
                            <input
                                form="editar-proyecto-{{ $proyecto->id }}"
                                type="date"
                                name="fecha_fin"
                                class="form-control"
                                value="{{ $proyecto->fecha_fin }}"
                            >
                        @else
                            <p class="mb-0">
                                {{ $proyecto->fecha_fin ?? 'No definida' }}
                            </p>
                        @endif
                    </div>

                    <div class="col-12 d-flex flex-wrap gap-2">

                        @if($puedeEditar)

                            <button
                                form="editar-proyecto-{{ $proyecto->id }}"
                                type="submit"
                                class="btn btn-gia btn-sm"
                            >
                                <i class="bi bi-save me-1"></i>
                                Guardar cambios
                            </button>

                            <form
                                action="{{ route('proyectos.destroy', $proyecto) }}"
                                method="POST"
                                onsubmit="return confirm('¿Deseas eliminar este proyecto y sus tareas?')"
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

                        @endif

                        <span class="badge text-bg-light">
                            Tareas: {{ $proyecto->tasks->count() }}
                        </span>

                    </div>

                </div>
            </div>

        @empty

            <div class="text-center py-5">
                <i class="bi bi-folder2 fs-1" style="color:#a58ae8;"></i>
                <h6 class="fw-bold mt-3">Todavía no hay proyectos</h6>
                <p class="texto-suave mb-0">
                    Cuando registres un proyecto aparecerá aquí.
                </p>
            </div>

        @endforelse

    </div>

    <div class="text-center mt-4 texto-suave">
        GiaFlow. — Organiza. Asigna. Avanza.
    </div>

</div>

@endsection
