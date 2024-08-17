<?php

namespace App\Http\Controllers\Conductor;

use App\Http\Controllers\Controller;
use App\Models\Position;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class ConductorController extends Controller
{

    public function index()
    {
        // Verifica si el usuario está autenticado
        if (!Auth::check()) {
            return redirect('/')->with('error', 'Debes iniciar sesión para acceder a esta página.');
        }

        $user = Auth::user();

        // Si es Conductor, obtiene solo el registro correspondiente a ese conductor
        $positions = Position::where('user_id', $user->id)->get();

        // Pasa el rol del usuario y los registros a la vista
        return view('Conductor.index', compact('positions'));
    }


    public function store(Request $request)
    {
        // Validar los datos de entrada
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'status' => 'required',
        ]);

        // Obtener el ID del usuario autenticado
        $userId = Auth::id();

        // Verificar si el usuario ya tiene un registro en la tabla Position
        $position = Position::firstOrNew(['user_id' => $userId]);

        // Actualizar la posición y el estado
        $position->latitud = $request->latitude;
        $position->longitud = $request->longitude;
        $position->status = $request->status;

        // Guardar el registro (insertar si es nuevo o actualizar si ya existía)
        $position->save();

        return response()->json([
            'success' => true,
            'message' => $position->wasRecentlyCreated ? 'Posición guardada exitosamente.' : 'Posición actualizada exitosamente.',
        ]);
    }


    public function getLatestLocation()
    {
        // Obtener todas las posiciones con status igual a 1
        $positions = Position::where('status', 1)->get(['id', 'latitud', 'longitud', 'status']);

        return response()->json($positions);
    }
}
