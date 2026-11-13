<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // Comprobar que quien accede sea administrador.
    private function comprobarAdministrador(Request $request): void
    {
        abort_unless(
            $request->user()?->role?->nombre === 'Administrador',
            403,
            'No tienes permiso para acceder a esta sección.'
        );
    }

    // Mostrar todos los usuarios.
    public function index(Request $request)
    {
        $this->comprobarAdministrador($request);

        $usuarios = User::with('role')->orderBy('id')->get();
        $roles = Role::orderBy('id')->get();

        return view('usuarios.index', compact('usuarios', 'roles'));
    }

    // Registrar un usuario.
    public function store(Request $request)
    {
        $this->comprobarAdministrador($request);

        $datos = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        User::create($datos);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario registrado correctamente.');
    }

    // Actualizar los datos de un usuario.
    public function update(Request $request, User $user)
    {
        $this->comprobarAdministrador($request);

        $datos = $request->validate([
            'name' => 'required|string|max:100',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'role_id' => 'required|exists:roles,id',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $nuevoRol = Role::findOrFail($datos['role_id']);

        // No permitir que el administrador se quite su propio rol.
        if (
            $request->user()->id === $user->id &&
            $nuevoRol->nombre !== 'Administrador'
        ) {
            return back()->with('error', 'No puedes quitarte tu propio rol de administrador.');
        }

        // Evitar que el sistema se quede sin administradores.
        if (
            $user->role?->nombre === 'Administrador' &&
            $nuevoRol->nombre !== 'Administrador' &&
            User::whereHas('role', function ($query) {
                $query->where('nombre', 'Administrador');
            })->count() <= 1
        ) {
            return back()->with('error', 'Debe existir al menos un administrador.');
        }

        if (empty($datos['password'])) {
            unset($datos['password']);
        }

        $user->update($datos);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    // Eliminar un usuario.
    public function destroy(Request $request, User $user)
    {
        $this->comprobarAdministrador($request);

        // Evitar eliminar la cuenta que está usando el administrador.
        if ($request->user()->id === $user->id) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        // Evitar que el sistema se quede sin administradores.
        if (
            $user->role?->nombre === 'Administrador' &&
            User::whereHas('role', function ($query) {
                $query->where('nombre', 'Administrador');
            })->count() <= 1
        ) {
            return back()->with('error', 'No puedes eliminar al último administrador.');
        }

        // No eliminar gerentes que todavía tienen proyectos.
        if ($user->managedProjects()->exists()) {
            return back()->with('error', 'No puedes eliminar este usuario porque tiene proyectos asignados como gerente.');
        }

        $user->delete();

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }
}
