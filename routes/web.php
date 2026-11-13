<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
Route::get('/', function () {
    return redirect()->route('login');
});

// Inicio de sesión
Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.process');

// Registro
Route::get('/register', [AuthController::class, 'showRegister'])
    ->name('register');

Route::post('/register', [AuthController::class, 'register'])
    ->name('register.process');

// Cerrar sesión
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');


// Panel principal protegido
Route::get('/dashboard', function () {

    $usuario = Auth::user();
    $rol = $usuario->role?->nombre;

    // Consultar proyectos según el rol
    $consultaProyectos = \App\Models\Project::query();

    if ($rol === 'Gerente de proyecto') {
        $consultaProyectos->where('gerente_id', $usuario->id);

    } elseif ($rol === 'Miembro del equipo') {
        $consultaProyectos->whereHas('tasks', function ($query) use ($usuario) {
            $query->where('assigned_user_id', $usuario->id);
        });
    }

    $totalProyectos = (clone $consultaProyectos)->count();

        $proyectos = (clone $consultaProyectos)
        ->with('gerente', 'tasks')
        ->withCount('tasks')
        ->withCount([
            'tasks as tareas_completadas' => function ($query) {
                $query->where('estado', 'Completada');
            }
        ])
        ->latest()
        ->take(5)
        ->get();

    // Consultar tareas según el rol
    $consultaTareas = \App\Models\Task::query();

    if ($rol === 'Gerente de proyecto') {
        $consultaTareas->whereHas('project', function ($query) use ($usuario) {
            $query->where('gerente_id', $usuario->id);
        });

    } elseif ($rol === 'Miembro del equipo') {
        $consultaTareas->where('assigned_user_id', $usuario->id);
    }

    $totalTareas = (clone $consultaTareas)->count();

    $tareasPendientes = (clone $consultaTareas)
        ->where('estado', 'Pendiente')
        ->count();

    $tareasCompletadas = (clone $consultaTareas)
        ->where('estado', 'Completada')
        ->count();

    $ultimasTareas = (clone $consultaTareas)
        ->with('project', 'assignedUser')
        ->latest()
        ->take(5)
        ->get();

    return view('dashboard', compact(
        'usuario',
        'rol',
        'totalProyectos',
        'totalTareas',
        'tareasPendientes',
        'tareasCompletadas',
        'proyectos',
        'ultimasTareas'
    ));

})->middleware('auth')->name('dashboard');

// Gestión de usuarios (solo administradores)
Route::middleware(['auth'])->group(function () {

    Route::get('/usuarios', [UserController::class, 'index'])
        ->name('usuarios.index');

    Route::post('/usuarios', [UserController::class, 'store'])
        ->name('usuarios.store');

    Route::put('/usuarios/{user}', [UserController::class, 'update'])
        ->name('usuarios.update');

    Route::delete('/usuarios/{user}', [UserController::class, 'destroy'])
        ->name('usuarios.destroy');

});
// Gestión de proyectos
Route::middleware(['auth'])->group(function () {

    Route::get('/proyectos', [ProjectController::class, 'index'])
        ->name('proyectos.index');

    Route::post('/proyectos', [ProjectController::class, 'store'])
        ->name('proyectos.store');

    Route::put('/proyectos/{project}', [ProjectController::class, 'update'])
        ->name('proyectos.update');

    Route::delete('/proyectos/{project}', [ProjectController::class, 'destroy'])
        ->name('proyectos.destroy');

});
// Gestión de tareas
Route::middleware(['auth'])->group(function () {

    Route::get('/tareas', [TaskController::class, 'index'])
        ->name('tareas.index');

    Route::post('/tareas', [TaskController::class, 'store'])
        ->name('tareas.store');

    Route::put('/tareas/{task}', [TaskController::class, 'update'])
        ->name('tareas.update');

    Route::patch('/tareas/{task}/estado', [TaskController::class, 'updateStatus'])
        ->name('tareas.estado');

    Route::delete('/tareas/{task}', [TaskController::class, 'destroy'])
        ->name('tareas.destroy');

});