<x-map-layout :rolUser="$rolUser" :positions="$positions">
    <div class="fixed top-0 left-0 p-4 z-10">
        @livewire('navigation-menu')
    </div>
     {{-- <div id="cargando">
        <div class="text-center">
            <div class="spinner"></div>
            <p class="text-gray-700 mt-4">Cargando mapa...</p>
        </div>
    </div> --}}

    {{-- <div id="info-card" class="absolute z-2 flex items-center space-x-2">
        <h3>Tiempo Estimado de Llegada</h3>
        <div id="info-content"></div>
        <button id="close-info-card">Cerrar</button>
    </div>
     --}}

    <div id="mensaje-recarga" class="mensaje-recarga-api">
        <p id="mensaje-texto">
            El mapa no se pudo cargar después de varios intentos. Por favor,
            <a href="#" id="enlace-recarga" onclick="window.location.reload();">recarga la página</a>.
        </p>
    </div> 
    @if ($rolUser == "Conductor")
        <div class="absolute top-4 right-4 z-2 flex items-center space-x-2 bg-gray-800 p-2 rounded-xl">
            <span class="text-sm text-slate-200 font-semibold">Ubicación</span>
            <label class="inline-flex items-center cursor-pointer">
                <input type="checkbox" id="ubicacion-switch" class="sr-only peer" checked>
                <div
                    class="relative w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-focus:ring-4 peer-focus:ring-orange-300 dark:peer-focus:ring-orange-800 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-orange-500">
                </div>
            </label>
        </div>
    @endif

</x-map-layout>
