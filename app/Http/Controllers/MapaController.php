<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MapaController extends Controller
{
    public function index()
    {
        // Verifica si el usuario está autenticado
        if (!Auth::check()) {
            return redirect('/')->with('error', 'Debes iniciar sesión para acceder a esta página.');
        }

        $user = Auth::user();
        $rolUser = $user->roles()->first()->name;

        // Obtener registros de Position según el rol del usuario
        if ($rolUser == 'Administrativo' || $rolUser == 'Alumno') {
            // Si es Administrativo o Alumno, obtiene todos los registros de Position
            $positions = Position::all();
        } elseif ($rolUser == 'Conductor') {
            // Si es Conductor, obtiene solo el registro correspondiente a ese conductor
            $positions = Position::where('user_id', $user->id)->get();
        }

        // Pasa el rol del usuario y los registros a la vista
        return view('Mapa.index', compact('rolUser', 'positions'));
    }
}
