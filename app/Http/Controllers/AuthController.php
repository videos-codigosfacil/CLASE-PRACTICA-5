<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Mostrar inicio de sesión
    public function showLogin()
    {
        return view('auth.login');
    }

    // Procesar inicio de sesión
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->route('dashboard');
        }

        return back()
            ->withErrors([
                'email' => 'El correo o la contraseña son incorrectos.',
            ])
            ->onlyInput('email');
    }

    // Mostrar registro
    public function showRegister()
    {
        return view('auth.register');
    }

    // Registrar usuario
    public function register(Request $request)
    {
        $datos = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $rol = Role::where('nombre', 'Miembro del equipo')->firstOrFail();

        $usuario = User::create([
            'name' => $datos['name'],
            'email' => $datos['email'],
            'password' => Hash::make($datos['password']),
            'role_id' => $rol->id,
        ]);

        Auth::login($usuario);

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    // Cerrar sesión
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
