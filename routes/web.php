<?php

use App\Http\Controllers\Alumno\AlumnoController;
use App\Http\Controllers\Conductor\ConductorController;
use App\Http\Controllers\MapaController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        // Obtener el rol del usuario autenticado
        $rolUser = Auth::user()->roles()->first()->name; // Asumiendo que 'role' es el nombre del campo que almacena el rol del usuario

        // Verificar el rol y redirigir al índice correspondiente
        if ($rolUser === 'Conductor') {
            return redirect()->route('conductor.index');
        } elseif ($rolUser === 'Alumno') {
            return redirect()->route('alumno.index');
        } elseif ($rolUser === 'Administrativo') {
            return redirect()->route('administrativo.index');
        }

        // Si el rol no coincide con ninguno de los anteriores, redirigir a un lugar por defecto
        return redirect()->route('dashboard');
    }

    // Si el usuario no está autenticado, muestra el formulario de inicio de sesión
    return view('welcome');
});

// Route::get('/', function () {
//     if (Auth::check()) {
//         // Si el usuario ya está autenticado, redirige a la vista del mapa
//         return redirect()->route('mapa.index');
//     }
//     // Si el usuario no está autenticado, muestra el formulario de inicio de sesión
//     return view('welcome');
// });


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::get('/Maps-Pedri-Movil', [MapaController::class, 'index'])->name('mapa.index');

Route::get('/conductor', [ConductorController::class, 'index'])->name('conductor.index');
Route::get('/add-locations', [ConductorController::class, 'store'])->name('conductor.store');
Route::get('/get-latest-location', [ConductorController::class, 'getLatestLocation'])->name('conductor.getlocations');


Route::get('/alumno', [AlumnoController::class, 'index'])->name('alumno.index');


require __DIR__ . '/auth.php';
