<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    private function comprobarPermiso(Request $request): void
    {
        $rol = $request->user()->role?->nombre;

        abort_unless(
            in_array($rol, ['Administrador', 'Gerente de proyecto']),
            403
        );
    }

    public function index(Request $request)
    {
        $usuario = $request->user();
        $rol = $usuario->role?->nombre;

        if ($rol === 'Administrador') {
            $tareas = Task::with('project', 'assignedUser')->latest()->get();
            $proyectos = Project::orderBy('nombre')->get();

        } elseif ($rol === 'Gerente de proyecto') {
            $tareas = Task::with('project', 'assignedUser')
                ->whereHas('project', function ($query) use ($usuario) {
                    $query->where('gerente_id', $usuario->id);
                })->latest()->get();

            $proyectos = Project::where('gerente_id', $usuario->id)
                ->orderBy('nombre')->get();

        } else {
            $tareas = Task::with('project', 'assignedUser')
                ->where('assigned_user_id', $usuario->id)
                ->latest()->get();

            $proyectos = collect();
        }

        $miembros = User::whereHas('role', function ($query) {
            $query->where('nombre', 'Miembro del equipo');
        })->orderBy('name')->get();

        return view('tareas.index', compact(
            'tareas',
            'proyectos',
            'miembros',
            'rol'
        ));
    }

    public function store(Request $request)
    {
        $this->comprobarPermiso($request);

        $datos = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'assigned_user_id' => 'required|exists:users,id',
            'titulo' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:Pendiente,En proceso,Completada',
            'prioridad' => 'required|in:Baja,Media,Alta',
            'fecha_vencimiento' => 'nullable|date',
        ]);

        $usuario = $request->user();
        $proyecto = Project::findOrFail($datos['project_id']);

        if (
            $usuario->role?->nombre === 'Gerente de proyecto' &&
            $proyecto->gerente_id != $usuario->id
        ) {
            abort(403);
        }

        $esMiembro = User::where('id', $datos['assigned_user_id'])
            ->whereHas('role', function ($query) {
                $query->where('nombre', 'Miembro del equipo');
            })->exists();

        if (!$esMiembro) {
            return back()->withInput()->withErrors([
                'assigned_user_id' => 'Selecciona un miembro del equipo.'
            ]);
        }

        Task::create($datos);

        return redirect()->route('tareas.index')
            ->with('success', 'Tarea creada correctamente.');
    }

    public function update(Request $request, Task $task)
    {
        $this->comprobarPermiso($request);

        $usuario = $request->user();

        if (
            $usuario->role?->nombre === 'Gerente de proyecto' &&
            $task->project?->gerente_id != $usuario->id
        ) {
            abort(403);
        }

        $datos = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'assigned_user_id' => 'required|exists:users,id',
            'titulo' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:Pendiente,En proceso,Completada',
            'prioridad' => 'required|in:Baja,Media,Alta',
            'fecha_vencimiento' => 'nullable|date',
        ]);

        $proyecto = Project::findOrFail($datos['project_id']);

        if (
            $usuario->role?->nombre === 'Gerente de proyecto' &&
            $proyecto->gerente_id != $usuario->id
        ) {
            abort(403);
        }

        $esMiembro = User::where('id', $datos['assigned_user_id'])
            ->whereHas('role', function ($query) {
                $query->where('nombre', 'Miembro del equipo');
            })->exists();

        if (!$esMiembro) {
            return back()->withErrors([
                'assigned_user_id' => 'Selecciona un miembro del equipo.'
            ]);
        }

        $task->update($datos);

        return redirect()->route('tareas.index')
            ->with('success', 'Tarea actualizada correctamente.');
    }

    public function updateStatus(Request $request, Task $task)
    {
        $usuario = $request->user();

        abort_unless(
            $usuario->role?->nombre === 'Miembro del equipo' &&
            $task->assigned_user_id == $usuario->id,
            403
        );

        $datos = $request->validate([
            'estado' => 'required|in:Pendiente,En proceso,Completada',
        ]);

        $task->update($datos);

        return redirect()->route('tareas.index')
            ->with('success', 'Estado actualizado correctamente.');
    }

    public function destroy(Request $request, Task $task)
    {
        $this->comprobarPermiso($request);

        $usuario = $request->user();

        if (
            $usuario->role?->nombre === 'Gerente de proyecto' &&
            $task->project?->gerente_id != $usuario->id
        ) {
            abort(403);
        }

        $task->delete();

        return redirect()->route('tareas.index')
            ->with('success', 'Tarea eliminada correctamente.');
    }
}
