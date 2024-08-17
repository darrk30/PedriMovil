<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    // public function store(LoginRequest $request): RedirectResponse
    // {
    //     $request->authenticate();

    //     $request->session()->regenerate();

    //     // return redirect()->intended(route('dashboard', absolute: false));        

    //     return redirect()->route('mapa.index');
    // }
    public function store(LoginRequest $request): RedirectResponse
    {
        // Autenticar al usuario
        $request->authenticate();

        // Regenerar la sesión
        $request->session()->regenerate();

        // Obtener el rol del usuario autenticado
        $role = auth()->user()->role; // Asumiendo que 'role' es el nombre del campo que almacena el rol del usuario

        // Verificar el rol y redirigir al índice correspondiente
        if ($role === 'Conductor') {
            return redirect()->route('conductor.index');
        } elseif ($role === 'Alumno') {
            return redirect()->route('alumno.index');
        } elseif ($role === 'Administrativo') {
            return redirect()->route('administrativo.index');
        }

        // Si el rol no coincide con ninguno de los anteriores, redirigir a un lugar por defecto
        return redirect()->route('dashboard');
    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
