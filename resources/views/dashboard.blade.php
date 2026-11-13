@extends('layouts.app')

@section('title', 'Panel principal')

@section('styles')
<style>
    .saludo h2 {
        font-weight: 750;
        color: #302b3d;
    }

    .estadistica {
        background: white;
        border: 1px solid #eeeaf7;
        border-radius: 17px;
        padding: 22px;
        height: 100%;
        box-shadow: 0 5px 20px rgba(50,35,90,.04);
    }

    .icono-estadistica {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 13px;
        font-size: 23px;
        margin-bottom: 17px;
    }

    .fondo-morado { background: #f0eafd; color: #7655c9; }
    .fondo-azul { background: #e8f1ff; color: #4285d4; }
    .fondo-naranja { background: #fff1e5; color: #e69543; }
    .fondo-verde { background: #e5f7ed; color: #38a169; }

    .numero-estadistica {
        font-size: 29px;
        font-weight: 750;
    }

    .titulo-seccion {
        font-size: 19px;
        font-weight: 700;
        margin-bottom: 20px;
    }

    .proyecto-panel {
        background: white;
        border: 1px solid #eeeaf7;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 15px;
    }

    .barra-progreso {
        height: 9px;
        background: #eeeaf7;
        border-radius: 20px;
        overflow: hidden;
    }

    .barra-progreso div {
        height: 100%;
        background: linear-gradient(90deg, #7655c9, #a58ae8);
        border-radius: 20px;
    }

    .tabla-panel {
        background: white;
        border: 1px solid #eeeaf7;
        border-radius: 16px;
        overflow: hidden;
    }

    .tabla-panel th {
        background: #faf9fd;
        color: #827d91;
        font-size: 12px;
        text-transform: uppercase;
        padding: 15px;
        white-space: nowrap;
    }

    .tabla-panel td {
        padding: 15px;
        vertical-align: middle;
        font-size: 13px;
    }

    .etiqueta {
        padding: 6px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 650;
        display: inline-block;
    }

    .estado-pendiente { background: #fff1df; color: #c47b25; }
    .estado-proceso { background: #e7f0ff; color: #3976c6; }
    .estado-completada { background: #e4f6eb; color: #32945b; }

    .prioridad-alta { color: #dc5555; }
    .prioridad-media { color: #d18b35; }
    .prioridad-baja { color: #32945b; }

    .acceso-rapido {
        background: white;
        border: 1px solid #eeeaf7;
        border-radius: 15px;
        padding: 18px;
        text-decoration: none;
        color: #302b3d;
        display: flex;
        align-items: center;
        gap: 13px;
        transition: .2s;
        height: 100%;
    }

    .acceso-rapido:hover {
        border-color: #bba9e9;
        transform: translateY(-3px);
        color: #7655c9;
    }

    .icono-acceso {
        background: #f1edfc;
        color: #7655c9;
        width: 43px;
        height: 43px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .vacio {
        padding: 35px 15px;
        text-align: center;
        color: #827d91;
    }
</style>
@endsection

@section('content')

<div class="container-fluid">

    {{-- Saludo --}}
    <div class="mb-4">
        <div class="saludo">
            <h2>¡Hola, {{ $usuario->name }}!</h2>
            <p class="texto-suave mb-2">
                Bienvenido a tu espacio de organización y trabajo en equipo.
            </p>
            <span class="badge" style="background:#f0eafd;color:#7655c9;">
                {{ $rol }}
            </span>
        </div>
    </div>

    {{-- Estadísticas --}}
    <div class="row g-3 mb-5">

        <div class="col-6 col-xl-3">
            <div class="estadistica">
                <div class="icono-estadistica fondo-morado">
                    <i class="bi bi-folder2-open"></i>
                </div>
                <div class="numero-estadistica">{{ $totalProyectos }}</div>
                <div class="texto-suave small">Mis proyectos</div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="estadistica">
                <div class="icono-estadistica fondo-azul">
                    <i class="bi bi-list-check"></i>
                </div>
                <div class="numero-estadistica">{{ $totalTareas }}</div>
                <div class="texto-suave small">Total de tareas</div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="estadistica">
                <div class="icono-estadistica fondo-naranja">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div class="numero-estadistica">{{ $tareasPendientes }}</div>
                <div class="texto-suave small">Tareas pendientes</div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="estadistica">
                <div class="icono-estadistica fondo-verde">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="numero-estadistica">{{ $tareasCompletadas }}</div>
                <div class="texto-suave small">Tareas completadas</div>
            </div>
        </div>

    </div>

    {{-- Progreso de proyectos --}}
    <div class="mb-5">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="titulo-seccion">Progreso de proyectos</h5>

            <a href="{{ route('proyectos.index') }}"
               class="text-decoration-none small fw-semibold"
               style="color:#7655c9;">
                Ver proyectos <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        @forelse($proyectos->take(4) as $proyecto)

            @php
                $totalProyecto = $proyecto->tasks->count();

                $completadasProyecto = $proyecto->tasks
                    ->where('estado', 'Completada')->count();

                $progreso = $totalProyecto > 0
                    ? round(($completadasProyecto / $totalProyecto) * 100)
                    : 0;
            @endphp

            <div class="proyecto-panel">

                <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                    <div>
                        <h6 class="fw-bold mb-1">{{ $proyecto->nombre }}</h6>
                        <small class="texto-suave">
                            Gerente: {{ $proyecto->gerente?->name ?? 'Sin asignar' }}
                        </small>
                    </div>

                    <span class="etiqueta estado-proceso">
                        {{ $proyecto->estado }}
                    </span>
                </div>

                <div class="d-flex justify-content-between small mb-2">
                    <span class="texto-suave">
                        {{ $completadasProyecto }} de {{ $totalProyecto }} tareas completadas
                    </span>
                    <strong>{{ $progreso }}%</strong>
                </div>

               <progress
                class="barra-progreso"
                max="100"
                value="{{ $progreso }}">
                {{ $progreso }}%
                </progress>

            </div>

        @empty
            <div class="tarjeta vacio">
                <i class="bi bi-folder-x fs-1"></i>
                <p class="mt-2 mb-0">Todavía no tienes proyectos para mostrar.</p>
            </div>
        @endforelse

    </div>

    {{-- Tareas recientes --}}
    <div class="mb-5">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="titulo-seccion">Tareas recientes</h5>

            <a href="{{ route('tareas.index') }}"
               class="text-decoration-none small fw-semibold"
               style="color:#7655c9;">
                Ver todas <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="tabla-panel">
            <div class="table-responsive">

                <table class="table table-hover mb-0">

                    <thead>
                        <tr>
                            <th>Tarea</th>
                            <th>Proyecto</th>
                            <th>Responsable</th>
                            <th>Prioridad</th>
                            <th>Estado</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($ultimasTareas as $tarea)

                            <tr>
                                <td class="fw-semibold">{{ $tarea->titulo }}</td>

                                <td>{{ $tarea->project?->nombre ?? 'Sin proyecto' }}</td>

                                <td>{{ $tarea->assignedUser?->name ?? 'Sin asignar' }}</td>

                                <td>
                                    @if($tarea->prioridad === 'Alta')
                                        <span class="prioridad-alta fw-semibold">Alta</span>
                                    @elseif($tarea->prioridad === 'Media')
                                        <span class="prioridad-media fw-semibold">Media</span>
                                    @else
                                        <span class="prioridad-baja fw-semibold">Baja</span>
                                    @endif
                                </td>

                                <td>
                                    @if($tarea->estado === 'Pendiente')
                                        <span class="etiqueta estado-pendiente">Pendiente</span>
                                    @elseif($tarea->estado === 'En proceso')
                                        <span class="etiqueta estado-proceso">En proceso</span>
                                    @else
                                        <span class="etiqueta estado-completada">Completada</span>
                                    @endif
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="vacio">
                                        <i class="bi bi-clipboard-x fs-1"></i>
                                        <p class="mt-2 mb-0">No tienes tareas registradas.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>

            </div>
        </div>
    </div>

    {{-- Accesos rápidos --}}
    <div class="mb-4">

        <h5 class="titulo-seccion">Accesos rápidos</h5>

        <div class="row g-3">

            <div class="col-md-6 col-xl-4">
                <a href="{{ route('proyectos.index') }}" class="acceso-rapido">
                    <div class="icono-acceso">
                        <i class="bi bi-folder-plus"></i>
                    </div>
                    <div>
                        <strong>Proyectos</strong>
                        <div class="texto-suave small">Consultar proyectos</div>
                    </div>
                    <i class="bi bi-arrow-right ms-auto"></i>
                </a>
            </div>

            <div class="col-md-6 col-xl-4">
                <a href="{{ route('tareas.index') }}" class="acceso-rapido">
                    <div class="icono-acceso">
                        <i class="bi bi-list-task"></i>
                    </div>
                    <div>
                        <strong>Tareas</strong>
                        <div class="texto-suave small">Consultar actividades</div>
                    </div>
                    <i class="bi bi-arrow-right ms-auto"></i>
                </a>
            </div>

            @if($rol === 'Administrador')
                <div class="col-md-6 col-xl-4">
                    <a href="{{ route('usuarios.index') }}" class="acceso-rapido">
                        <div class="icono-acceso">
                            <i class="bi bi-people"></i>
                        </div>
                        <div>
                            <strong>Usuarios</strong>
                            <div class="texto-suave small">Administrar usuarios</div>
                        </div>
                        <i class="bi bi-arrow-right ms-auto"></i>
                    </a>
                </div>
            @endif

        </div>
    </div>

    <div class="text-center texto-suave small mt-5">
        GiaFlow. — Organiza. Asigna. Avanza.
    </div>

</div>

@endsection
