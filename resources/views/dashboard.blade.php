<x-app-layout>
    <div>
        <style>
            /* Estilos del Spinner */
            .spinner {
                border: 4px solid rgba(0, 0, 0, 0.1); /* Gris claro */
                border-left-color: #3498db; /* Color azul */
                border-radius: 50%;
                width: 40px;
                height: 40px;
                animation: spin 1s linear infinite;
            }
    
            @keyframes spin {
                to {
                    transform: rotate(360deg);
                }
            }
    
            /* Estilos para el mensaje de recarga */
            #mensaje-recarga {
                display: none;
                position: fixed;
                top: 20%;
                left: 50%;
                transform: translateX(-50%);
                background: white;
                padding: 16px;
                border: 1px solid #ddd;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                z-index: 1000;
            }
        </style>
    
        <div id="mapa" class="absolute inset-0 z-0"></div>
        <div id="cargando" class="fixed top-0 left-0 right-0 bottom-0 flex items-center justify-center bg-white z-50">
            <div class="flex flex-col items-center">
                <div class="spinner"></div>
                <p class="text-gray-700 mt-4">Cargando mapa...</p>
            </div>
        </div>
        <div id="mensaje-recarga"
            class="fixed top-20 left-1/2 transform -translate-x-1/2 bg-white border border-gray-300 shadow-lg p-4 z-50">
            <p class="text-gray-700">El mapa no se pudo cargar después de varios intentos. Por favor, <a href="#"
                    id="enlace-recarga" class="text-blue-500" onclick="window.location.reload();">recarga la página</a>.</p>
        </div>
    
        @push('scripts')
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCJJGvOLF0AX2sslRUJPouL0lR44XHZOKA&callback=initMap" async defer></script>
        <script>
            let mapa; // Variable para el mapa de Google Maps
            let marcador; // Variable para el marcador en el mapa
            let ubicacionInicialEstablecida = false; // Indica si la ubicación inicial ya ha sido establecida
            let intentosDeCarga = 0; // Contador de intentos de carga del mapa
            const maxIntentos = 5; // Número máximo de intentos de carga
    
            // Función para mostrar una alerta en la página
            function mostrarAlerta(mensaje, tipo = 'error') {
                const contenedorAlerta = document.getElementById('contenedor-alerta');
                const mensajeAlerta = document.getElementById('mensaje-alerta');
    
                if (contenedorAlerta && mensajeAlerta) {
                    mensajeAlerta.textContent = mensaje;
                    contenedorAlerta.classList.remove('hidden');
                    contenedorAlerta.className =
                        `fixed top-0 left-0 right-0 p-4 ${tipo === 'error' ? 'bg-red-500' : 'bg-green-500'} text-white text-center z-50`;
    
                    setTimeout(() => {
                        contenedorAlerta.classList.add('hidden');
                    }, 3000); // 3000 ms = 3 segundos
                }
            }
    
            // Función inicial para cargar el mapa
            function initMap() {
                const estilosMapa = [{
                    featureType: "poi",
                    elementType: "all",
                    stylers: [{
                        visibility: "off"
                    }]
                }];
    
                // Configuración inicial del mapa
                mapa = new google.maps.Map(document.getElementById('mapa'), {
                    zoom: 17,
                    disableDefaultUI: true, // Desactivar controles por defecto
                    zoomControl: false,
                    mapTypeControl: false,
                    scaleControl: false,
                    streetViewControl: false,
                    rotateControl: false,
                    fullscreenControl: false,
                    styles: estilosMapa
                });
    
                marcador = new google.maps.Marker({
                    map: mapa
                });
    
                obtenerUbicacionDispositivo();
                setInterval(actualizarUbicacionDispositivo, 1000); // Actualizar la ubicación cada segundo
    
                // Detectar cuando el mapa ha cargado completamente
                google.maps.event.addListenerOnce(mapa, 'tilesloaded', function () {
                    document.getElementById('cargando').style.display = 'none';
                    clearTimeout(tiempoEsperaCargaMapa);
                });
    
                // Intentar cargar el mapa varias veces si falla
                reintentarCargarMapa();
            }
    
            // Función para reintentar cargar el mapa hasta un máximo de intentos
            function reintentarCargarMapa() {
                intentosDeCarga++;
    
                if (intentosDeCarga <= maxIntentos) {
                    google.maps.event.addListenerOnce(mapa, 'tilesloaded', function () {
                        document.getElementById('cargando').style.display = 'none';
                        clearTimeout(tiempoEsperaCargaMapa);
                    });
    
                    tiempoEsperaCargaMapa = setTimeout(() => {
                        if (!ubicacionInicialEstablecida) {
                            initMap(); // Reintentar cargar el mapa
                        }
                    }, 3000); // Intentar recargar cada 3 segundos
    
                } else {
                    document.getElementById('cargando').style.display = 'none';
                    document.getElementById('mensaje-recarga').style.display = 'block';
                }
            }
    
            // Función para obtener la ubicación del dispositivo
            function obtenerUbicacionDispositivo() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            const posicion = {
                                lat: position.coords.latitude,
                                lng: position.coords.longitude
                            };
    
                            if (!ubicacionInicialEstablecida) {
                                mapa.setCenter(posicion); // Centrar el mapa en la ubicación
                                marcador.setPosition(posicion); // Colocar el marcador en la ubicación
                                ubicacionInicialEstablecida = true;
                            }

                            // Establecer la orientación del mapa
                            const heading = position.coords.heading || 0; // Tomar la orientación del dispositivo o 0 si no está disponible
                            mapa.setHeading(heading);
                        },
                        () => {
                            mostrarAlerta('Error obteniendo la geolocalización.', 'error');
                        }
                    );
                } else {
                    mostrarAlerta('Navegador no soporta geolocalización.', 'error');
                }
            }
    
            // Función para actualizar la ubicación del dispositivo cada segundo y mostrarla en la consola
            function actualizarUbicacionDispositivo() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            const posicion = {
                                lat: position.coords.latitude,
                                lng: position.coords.longitude
                            };
    
                            marcador.setPosition(posicion); // Actualizar la posición del marcador
    
                            // Mostrar la ubicación en la consola
                            console.log(`Ubicación actual: Latitud ${posicion.lat}, Longitud ${posicion.lng}`);

                            // Actualizar la orientación del mapa
                            const heading = position.coords.heading || 0;
                            mapa.setHeading(heading);
                        },
                        () => {
                            mostrarAlerta('Error obteniendo la geolocalización.', 'error');
                        }
                    );
                } else {
                    mostrarAlerta('Navegador no soporta geolocalización.', 'error');
                }
            }
        </script>
        @endpush
    </div>
    
</x-app-layout>
