@php
    // Obtener el rol del usuario autenticado
    $role = auth()->user()->roles()->first()->name; // Asumiendo que 'role' es el nombre del campo que almacena el rol del usuario
    
    // Definir la ruta de 'Inicio' según el rol del usuario
    $homeRoute = match ($role) {
        'Conductor' => route('conductor.index'),
        'Alumno' => route('alumno.index'),
        'Administrativo' => route('administrativo.index'),
        //default => route('dashboard'), // Ruta por defecto si no coincide con los roles
    };

    $links = [
        [
            'name' => 'Inicio',
            'route' => $homeRoute, // Usamos la ruta dinámica basada en el rol
            'active' => false,
        ],
        [
            'name' => 'Perfil',
            'route' => 'profile', // Ruta para el perfil
            'active' => false,
        ],
    ];
@endphp


<div x-data="{ isOpen: false }" class="relative">
    <!-- Botón de Menú -->
    <button @click="isOpen = !isOpen" class="p-2 border border-gray-300 shadow-lg rounded focus:outline-none bg-gray-800">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
        </svg>
    </button>

    <!-- Fondo Opaco -->
    <div x-show="isOpen" x-transition.opacity @click="isOpen = false" class="fixed inset-0 bg-gray-800 bg-opacity-75 z-10" x-cloak></div>

    <!-- Menú -->
    <div x-show="isOpen" x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="transform -translate-x-full opacity-0"
         x-transition:enter-end="transform translate-x-0 opacity-100"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="transform translate-x-0 opacity-100"
         x-transition:leave-end="transform -translate-x-full opacity-0"
         class="fixed inset-y-0 left-0 bg-white w-64 h-full p-4 z-20" x-cloak>
        <h2 class="text-xl font-semibold mb-4">Menú</h2>
        <ul>
            @foreach ($links as $link)
                <li class="mb-2"><a href="{{ $link['route'] }}" class="text-gray-700">{{ $link['name'] }}</a></li>
            @endforeach
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <li>
                    <button onclick="event.preventDefault(); this.closest('form').submit();" class="text-gray-700">
                        Cerrar Sesión
                    </button>
                </li>
            </form>
        </ul>
    </div>
</div>
