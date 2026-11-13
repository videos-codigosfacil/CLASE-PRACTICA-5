
@extends('layouts.app')

@section('title', 'Crear cuenta')

@section('content')

<div class="container min-vh-100 d-flex align-items-center justify-content-center py-5">

    <div class="row tarjeta overflow-hidden w-100" style="max-width: 950px;">

        <div class="col-md-6 p-5 d-flex flex-column justify-content-center"
             style="background: linear-gradient(145deg, #7655c9, #a58be8); color: white;">

            <h1 class="fw-bold mb-3">GiaFlow.</h1>

            <h2 class="fw-semibold mb-3">
                ¡Comienza a organizarte!
            </h2>

            <p style="color: #f0eaff;">
                Crea tu cuenta y empieza a gestionar tus tareas,
                colaborar con tu equipo y avanzar en tus proyectos.
            </p>

            <div class="mt-4">
                <p><i class="bi bi-check-circle-fill me-2"></i> Organiza tus actividades.</p>
                <p><i class="bi bi-check-circle-fill me-2"></i> Colabora con tu equipo.</p>
                <p><i class="bi bi-check-circle-fill me-2"></i> Consulta tus tareas asignadas.</p>
            </div>

        </div>

        <div class="col-md-6 p-4 p-lg-5">

            <h3 class="fw-bold">Crear una cuenta</h3>

            <p class="texto-suave mb-4">
                Completa tus datos para registrarte.
            </p>

            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('register.process') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nombre completo</label>
                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        placeholder="Tu nombre"
                        value="{{ old('name') }}"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Correo electrónico</label>
                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        placeholder="correo@ejemplo.com"
                        value="{{ old('email') }}"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Contraseña</label>
                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        placeholder="Mínimo 8 caracteres"
                        required
                    >
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Confirmar contraseña</label>
                    <input
                        type="password"
                        name="password_confirmation"
                        class="form-control"
                        placeholder="Repite tu contraseña"
                        required
                    >
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-gia">
                        Crear cuenta
                        <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </div>

            </form>

            <div class="text-center mt-4">
                <span class="texto-suave">¿Ya tienes una cuenta?</span>

                <a href="{{ route('login') }}"
                   class="text-decoration-none fw-semibold"
                   style="color: #7655c9;">
                    Inicia sesión
                </a>
            </div>

        </div>

    </div>

</div>

@endsection
