<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlumnoController extends Controller
{
    public function index()
    {
        // Verifica si el usuario está autenticado
        if (!Auth::check()) {
            return redirect('/')->with('error', 'Debes iniciar sesión para acceder a esta página.');
        }


       $positions = Position::all();
        

        // Pasa el rol del usuario y los registros a la vista
        return view('Alumno.index', compact('positions'));
    }
}
