<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    // Comprobar si el usuario puede administrar proyectos.
    private function comprobarAdministradorOGerente(Request $request): void
    {
        $rol = $request->user()?->role?->nombre;

        abort_unless(
            in_array($rol, ['Administrador', 'Gerente de proyecto']),
            403,
            'No tienes permiso para realizar esta acción.'
        );
    }

    // Mostrar los proyectos según el rol.
    public function index(Request $request)
    {
        $usuario = $request->user();
        $rol = $usuario->role?->nombre;

        if ($rol === 'Administrador') {
            $proyectos = Project::with('gerente', 'tasks')
                ->latest()
                ->get();
        } elseif ($rol === 'Gerente de proyecto') {
            $proyectos = Project::with('gerente', 'tasks')
                ->where('gerente_id', $usuario->id)
                ->latest()
                ->get();
        } else {
            $proyectos = Project::with('gerente', 'tasks')
                ->whereHas('tasks', function ($query) use ($usuario) {
                    $query->where('assigned_user_id', $usuario->id);
                })
                ->latest()
                ->get();
        }

        $gerentes = User::whereHas('role', function ($query) {
            $query->where('nombre', 'Gerente de proyecto');
        })->orderBy('name')->get();

        return view('proyectos.index', compact(
            'proyectos',
            'gerentes',
            'rol'
        ));
    }

    // Registrar un proyecto.
    public function store(Request $request)
    {
        $this->comprobarAdministradorOGerente($request);

        $datos = $request->validate([
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:Activo,En pausa,Finalizado',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'gerente_id' => 'nullable|exists:users,id',
        ]);

        if ($request->user()->role?->nombre === 'Gerente de proyecto') {
            $datos['gerente_id'] = $request->user()->id;
        } else {
            if (empty($datos['gerente_id'])) {
                return back()->withInput()
                    ->withErrors(['gerente_id' => 'Selecciona un gerente para el proyecto.']);
            }

            $esGerente = User::where('id', $datos['gerente_id'])
                ->whereHas('role', function ($query) {
                    $query->where('nombre', 'Gerente de proyecto');
                })->exists();

            if (!$esGerente) {
                return back()->withInput()
                    ->withErrors(['gerente_id' => 'El usuario seleccionado no tiene el rol de gerente.']);
            }
        }

        Project::create($datos);

        return redirect()->route('proyectos.index')
            ->with('success', 'Proyecto creado correctamente.');
    }

    // Actualizar un proyecto.
    public function update(Request $request, Project $project)
    {
        $this->comprobarAdministradorOGerente($request);

        $usuario = $request->user();
        $rol = $usuario->role?->nombre;

        if (
            $rol === 'Gerente de proyecto' &&
            $project->gerente_id !== $usuario->id
        ) {
            abort(403, 'No puedes modificar un proyecto que no está a tu cargo.');
        }

        $datos = $request->validate([
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:Activo,En pausa,Finalizado',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'gerente_id' => 'nullable|exists:users,id',
        ]);

        if ($rol === 'Gerente de proyecto') {
            $datos['gerente_id'] = $usuario->id;
        } else {
            if (empty($datos['gerente_id'])) {
                return back()->withErrors([
                    'gerente_id' => 'Selecciona un gerente para el proyecto.'
                ]);
            }

            $esGerente = User::where('id', $datos['gerente_id'])
                ->whereHas('role', function ($query) {
                    $query->where('nombre', 'Gerente de proyecto');
                })->exists();

            if (!$esGerente) {
                return back()->withErrors([
                    'gerente_id' => 'El usuario seleccionado no tiene el rol de gerente.'
                ]);
            }
        }

        $project->update($datos);

        return redirect()->route('proyectos.index')
            ->with('success', 'Proyecto actualizado correctamente.');
    }

    // Eliminar un proyecto.
    public function destroy(Request $request, Project $project)
    {
        $this->comprobarAdministradorOGerente($request);

        $usuario = $request->user();

        if (
            $usuario->role?->nombre === 'Gerente de proyecto' &&
            $project->gerente_id !== $usuario->id
        ) {
            abort(403, 'No puedes eliminar un proyecto que no está a tu cargo.');
        }

        $project->delete();

        return redirect()->route('proyectos.index')
            ->with('success', 'Proyecto eliminado correctamente.');
    }
}
