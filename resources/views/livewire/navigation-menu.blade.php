@php
    $links = [
        [
            'name' => 'Dashboard',
            'route' => 'dashboard',
            'active' => false,
        ],
        [
            'name' => 'Perfil',
            'route' => 'profile',
            'active' => false,
        ],       
    ];
@endphp

<div x-data="{ isOpen: false }" class="relative">
    <!-- Botón de Menú -->
    <button @click="isOpen = !isOpen" class="p-2 border border-gray-300 shadow-lg rounded focus:outline-none bg-white">
        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
        </svg>
    </button>

    <!-- Fondo Opaco -->
    <div x-show="isOpen" x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="isOpen = false" class="fixed inset-0 bg-gray-800 bg-opacity-75 z-10" x-cloak></div>

    <!-- Menú -->
    <div x-show="isOpen" x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="transform -translate-x-full"
         x-transition:enter-end="transform translate-x-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="transform translate-x-0"
         x-transition:leave-end="transform -translate-x-full"
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
