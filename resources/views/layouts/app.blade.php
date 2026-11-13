<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'GiaFlow') | Organiza. Asigna. Avanza.</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v=2">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="shortcut icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v=2">
    <style>
        :root {
            --morado: #7655c9;
            --morado-oscuro: #5638a5;
            --lavanda: #f1edfc;
            --fondo: #f8f7fc;
            --texto: #302b3d;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background: var(--fondo);
            color: var(--texto);
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
            margin: 0;
        }

        .marca {
            color: var(--morado);
            font-weight: 800;
            letter-spacing: -1px;
        }

        .btn-gia {
            background: var(--morado);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 11px 22px;
            font-weight: 600;
        }

        .btn-gia:hover {
            background: var(--morado-oscuro);
            color: white;
        }

        .form-control {
            border-radius: 10px;
            padding: 12px;
        }

        .form-control:focus {
            border-color: var(--morado);
            box-shadow: 0 0 0 .2rem rgba(118, 85, 201, .15);
        }

        .tarjeta {
            background: white;
            border: 1px solid #eeeaf7;
            border-radius: 18px;
            box-shadow: 0 8px 30px rgba(50, 35, 90, .06);
        }

        .texto-suave {
            color: #827d91;
        }

        /* Menú lateral */

        .barra-lateral {
            width: 255px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: linear-gradient(180deg, #7655c9, #5638a5);
            color: white;
            padding: 28px 18px;
            display: flex;
            flex-direction: column;
            z-index: 1000;
            transition: transform .3s ease;
        }

        .logo-lateral {
            color: white;
            font-size: 29px;
            font-weight: 800;
            text-decoration: none;
            letter-spacing: -1px;
            padding: 0 12px;
        }

        .subtitulo-menu {
            color: #d9cdf8;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.5px;
            margin: 38px 12px 15px;
            text-transform: uppercase;
        }

        .enlace-menu {
            display: flex;
            align-items: center;
            gap: 13px;
            color: #e9e2fa;
            text-decoration: none;
            padding: 13px 14px;
            border-radius: 11px;
            margin-bottom: 7px;
            font-size: 14px;
            font-weight: 500;
            transition: .2s;
        }

        .enlace-menu i {
            font-size: 19px;
        }

        .enlace-menu:hover,
        .enlace-menu.activo {
            background: rgba(255, 255, 255, .18);
            color: white;
        }

        .usuario-menu {
            margin-top: auto;
            padding: 17px 12px 5px;
            border-top: 1px solid rgba(255,255,255,.2);
        }

        .usuario-menu .nombre {
            font-weight: 600;
            font-size: 14px;
        }

        .usuario-menu .rol {
            color: #d9cdf8;
            font-size: 12px;
        }

        .btn-salir {
            background: rgba(255,255,255,.13);
            border: none;
            color: white;
            padding: 11px;
            border-radius: 10px;
            width: 100%;
            text-align: left;
            margin-top: 15px;
        }

        .btn-salir:hover {
            background: rgba(255,255,255,.25);
        }

        /* Contenido principal */

        .contenido-principal {
            margin-left: 255px;
            min-height: 100vh;
        }

        .barra-superior {
            background: white;
            padding: 16px 30px;
            border-bottom: 1px solid #eeeaf7;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .contenido-pagina {
            padding: 30px;
        }

        .boton-menu-movil {
            display: none;
            background: var(--lavanda);
            color: var(--morado);
            border: none;
            border-radius: 9px;
            padding: 8px 12px;
            font-size: 21px;
        }

        .fondo-menu {
            display: none;
        }

        @media (max-width: 768px) {
            .barra-lateral {
                transform: translateX(-100%);
            }

            .barra-lateral.mostrar {
                transform: translateX(0);
            }

            .contenido-principal {
                margin-left: 0;
            }

            .contenido-pagina {
                padding: 20px 15px;
            }

            .barra-superior {
                padding: 13px 16px;
            }

            .boton-menu-movil {
                display: inline-block;
            }

            .fondo-menu.mostrar {
                display: block;
                position: fixed;
                inset: 0;
                background: rgba(20, 15, 35, .45);
                z-index: 999;
            }
        }
    </style>

    @yield('styles')
</head>

<body>

    @auth
        @php
            $usuarioActual = Auth::user();
            $rolActual = $usuarioActual->role?->nombre ?? '';
            $esAdministrador = $rolActual === 'Administrador';
        @endphp

        <div class="fondo-menu" id="fondoMenu" onclick="cerrarMenu()"></div>

        <aside class="barra-lateral" id="barraLateral">

            <a href="{{ route('dashboard') }}" class="logo-lateral">
                GiaFlow.
            </a>

            <div class="subtitulo-menu">Menú principal</div>

            <a href="{{ route('dashboard') }}"
               class="enlace-menu {{ request()->routeIs('dashboard') ? 'activo' : '' }}">
                <i class="bi bi-grid-1x2"></i>
                Panel principal
            </a>

            <a href="{{ route('proyectos.index') }}"
               class="enlace-menu {{ request()->routeIs('proyectos.*') ? 'activo' : '' }}">
                <i class="bi bi-folder2-open"></i>
                Proyectos
            </a>

            <a href="{{ route('tareas.index') }}"
               class="enlace-menu {{ request()->routeIs('tareas.*') ? 'activo' : '' }}">
                <i class="bi bi-list-check"></i>
                Tareas
            </a>

            @if($esAdministrador)
                <a href="{{ route('usuarios.index') }}"
                   class="enlace-menu {{ request()->routeIs('usuarios.*') ? 'activo' : '' }}">
                    <i class="bi bi-people"></i>
                    Usuarios
                </a>
            @endif

            <div class="usuario-menu">
                <div class="nombre">
                    <i class="bi bi-person-circle me-2"></i>
                    {{ $usuarioActual->name }}
                </div>

                <div class="rol">
                    {{ $rolActual ?: 'Usuario' }}
                </div>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-salir">
                        <i class="bi bi-box-arrow-left me-2"></i>
                        Cerrar sesión
                    </button>
                </form>
            </div>

        </aside>
    @endauth

    <main class="{{ Auth::check() ? 'contenido-principal' : '' }}">

        @auth
            <header class="barra-superior">
                <div class="d-flex align-items-center gap-3">
                    <button class="boton-menu-movil" onclick="abrirMenu()">
                        <i class="bi bi-list"></i>
                    </button>

                    <div>
                        <span class="fw-semibold">@yield('title', 'Panel principal')</span>
                    </div>
                </div>

                <span class="texto-suave small d-none d-sm-inline">
                    Organiza. Asigna. Avanza.
                </span>
            </header>
        @endauth

        <div class="{{ Auth::check() ? 'contenido-pagina' : '' }}">
            @yield('content')
        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function abrirMenu() {
            document.getElementById('barraLateral').classList.add('mostrar');
            document.getElementById('fondoMenu').classList.add('mostrar');
        }

        function cerrarMenu() {
            document.getElementById('barraLateral').classList.remove('mostrar');
            document.getElementById('fondoMenu').classList.remove('mostrar');
        }
    </script>

    @yield('scripts')

</body>
</html>
