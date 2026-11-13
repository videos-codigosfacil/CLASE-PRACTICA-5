@extends('layouts.app')

@section('title', 'Iniciar sesión')

@section('content')

<div class="container min-vh-100 d-flex align-items-center justify-content-center py-5">

    <div class="row tarjeta overflow-hidden w-100" style="max-width: 950px;">

        <div class="col-md-6 p-5 d-flex flex-column justify-content-center"
             style="background: linear-gradient(145deg, #7655c9, #a58be8); color: white;">

            <h1 class="fw-bold mb-3">GiaFlow.</h1>

            <h2 class="fw-semibold mb-3">
                Organiza. Asigna. Avanza.
            </h2>

            <p class="mb-4" style="color: #f0eaff;">
                Gestiona tus proyectos, organiza tus tareas y trabaja
                en equipo desde un solo lugar.
            </p>

            <div class="mt-3">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-check-circle-fill me-3 fs-5"></i>
                    <span>Organización de proyectos</span>
                </div>

                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-check-circle-fill me-3 fs-5"></i>
                    <span>Asignación de tareas</span>
                </div>

                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill me-3 fs-5"></i>
                    <span>Seguimiento de avances</span>
                </div>
            </div>

        </div>

        <div class="col-md-6 p-4 p-lg-5">

            <div class="mb-4">
                <h3 class="fw-bold">¡Bienvenido!</h3>
                <p class="texto-suave">
                    Ingresa tus datos para continuar.
                </p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Correo electrónico
                    </label>

                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        placeholder="correo@ejemplo.com"
                        value="{{ old('email') }}"
                        required
                        autocomplete="email"
                    >
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        Contraseña
                    </label>

                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        placeholder="Ingresa tu contraseña"
                        required
                        autocomplete="current-password"
                    >
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-gia">
                        Iniciar sesión
                        <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </div>

            </form>

            <div class="text-center mt-4">
                <span class="texto-suave">
                    ¿No tienes una cuenta?
                </span>

                <a href="{{ route('register') }}"
                   class="text-decoration-none fw-semibold"
                   style="color: #7655c9;">
                    Regístrate
                </a>
            </div>

        </div>

    </div>

</div>

@endsection
