<x-map-layout>
    <div class="fixed top-0 left-0 p-4 z-10">
        @livewire('navigation-menu')
    </div>
    <div id="cargando">
        <div class="text-center">
            <div class="spinner"></div>
            <p class="text-gray-700 mt-4">Cargando mapa...</p>
        </div>
    </div>

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
    <div class="absolute top-4 right-4 z-2 flex items-center space-x-2 bg-gray-800 p-2 rounded-xl">
        <span class="text-sm text-slate-200 font-semibold">Ubicación</span>
        <label class="inline-flex items-center cursor-pointer">
            <input type="checkbox" id="ubicacion-switch" class="sr-only peer" checked>
            <div
                class="relative w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-focus:ring-4 peer-focus:ring-orange-300 dark:peer-focus:ring-orange-800 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-orange-500">
            </div>
        </label>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script>
        let map, marker, lastPosition, watchId;
        let ubicacionActiva = 1;
        //let posicionYEstadoInterval = null;
        const userId = {{ auth()->user()->id }};
        const positions = @json($positions);

        function initMap() {
            map = new google.maps.Map(document.getElementById('mapa'), {
                center: {
                    lat: 0,
                    lng: 0
                },
                zoom: 15,
                disableDefaultUI: true,
                styles: [{
                    featureType: 'poi.business',
                    stylers: [{
                        visibility: 'off'
                    }]
                }],
                gestureHandling: 'greedy'
            });

            marker = new google.maps.Marker({
                position: {
                    lat: 0,
                    lng: 0
                },
                map: map,
                icon: {
                    url: "{{ asset('storage/image/bus.png') }}",
                    scaledSize: new google.maps.Size(50, 50),
                    rotation: 0
                },
                animation: google.maps.Animation.DROP
            });

            // Crear los marcadores predeterminados
            const defaultMarkers = [{
                    position: {
                        lat: -6.769064,
                        lng: -79.845935
                    },
                    icon: "{{ asset('storage/image/paradero.png') }}"
                },
                {
                    position: {
                        lat: -6.707778,
                        lng: -79.905071
                    },
                    icon: "{{ asset('storage/image/paradero.png') }}"
                }
            ];

            defaultMarkers.forEach(markerData => {
                new google.maps.Marker({
                    position: markerData.position,
                    map: map,
                    icon: {
                        url: markerData.icon,
                        scaledSize: new google.maps.Size(50,
                            50) // Ajusta el tamaño del ícono si es necesario
                    },
                    title: 'Marcador Predeterminado'
                });
            });

            lastPosition = marker.getPosition();

            obtenerUbicacion();
        }

        function actualizarUbicacion(latitude, longitude) {
            const newPosition = new google.maps.LatLng(latitude, longitude);

            function animateMarker(marker, newPosition) {
                const currentPosition = marker.getPosition();
                const steps = 30;
                const latDelta = (newPosition.lat() - currentPosition.lat()) / steps;
                const lngDelta = (newPosition.lng() - currentPosition.lng()) / steps;
                let stepCount = 0;

                function step() {
                    stepCount++;
                    const intermediatePosition = new google.maps.LatLng(
                        currentPosition.lat() + latDelta * stepCount,
                        currentPosition.lng() + lngDelta * stepCount
                    );

                    const heading = google.maps.geometry.spherical.computeHeading(lastPosition, newPosition);

                    marker.setIcon({
                        url: "{{ asset('storage/image/bus.png') }}",
                        scaledSize: new google.maps.Size(50, 50),
                        rotation: heading
                    });

                    marker.setPosition(intermediatePosition);

                    if (google.maps.geometry.spherical.computeDistanceBetween(newPosition, map.getCenter()) > 100) {
                        map.panTo(marker.getPosition());
                    }

                    if (stepCount < steps) {
                        requestAnimationFrame(step);
                    } else {
                        lastPosition = marker.getPosition();

                        // Enviar posición actualizada al servidor mediante AJAX
                        enviarPosicion(latitude, longitude, ubicacionActiva);
                    }
                }

                step();
            }

            animateMarker(marker, newPosition);
        }

        function enviarPosicion(latitude, longitude, status) {
            const url = `/add-locations?latitude=${latitude}&longitude=${longitude}&status=${status}`;

            fetch(url, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    // Aquí puedes manejar lo que ocurre después de recibir una respuesta exitosa.
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Aquí puedes manejar el error, mostrar un mensaje al usuario, etc.
                });
        }

        function obtenerUbicacion() {
            if (navigator.geolocation && ubicacionActiva) {
                if (watchId) {
                    navigator.geolocation.clearWatch(watchId);
                }

                watchId = navigator.geolocation.watchPosition(
                    position => {
                        actualizarUbicacion(position.coords.latitude, position.coords.longitude);
                        document.getElementById('cargando').style.display = 'none';
                        document.getElementById('mensaje-recarga').style.display = 'none';
                    },
                    error => {
                        if (error.code !== error.PERMISSION_DENIED) {
                            const mensajeTexto = document.getElementById('mensaje-texto');
                            const mensajeRecarga = document.getElementById('mensaje-recarga');
                            mensajeRecarga.style.display = 'block';

                            switch (error.code) {
                                case error.POSITION_UNAVAILABLE:
                                    mensajeTexto.textContent =
                                        'Ubicación no disponible. Intenta nuevamente más tarde.';
                                    mensajeRecarga.className = 'mensaje-recarga-gps';
                                    break;
                                default:
                                    mensajeTexto.textContent =
                                        'Ocurrió un error inesperado. Intenta recargar la página.';
                                    mensajeRecarga.className = 'mensaje-recarga-api';
                            }
                        }
                    }, {
                        enableHighAccuracy: true,
                        timeout: 5000,
                        maximumAge: 0
                    }
                );
            } else {
                const mensajeTexto = document.getElementById('mensaje-texto');
                mensajeTexto.textContent =
                    'Geolocalización no es soportada por tu navegador. Por favor, usa uno que sea compatible.';
                document.getElementById('mensaje-recarga').className = 'mensaje-recarga-navegador';
                document.getElementById('mensaje-recarga').style.display = 'block';
            }
        }

        function toggleMarker(visible) {
            if (marker) {
                marker.setMap(visible ? map : null);
            }
        }

        document.getElementById('ubicacion-switch').addEventListener('change', function() {
            if (ubicacionActiva == 1) {
                ubicacionActiva = 0;
                if (watchId) {
                    navigator.geolocation.clearWatch(watchId);
                    watchId = null;
                }
                toggleMarker(false);
                enviarPosicion(0, 0, ubicacionActiva);
                //detenerPosicionYEstado(); // Detener el monitoreo de la posición
            } else {
                ubicacionActiva = 1;
                obtenerUbicacion();
                toggleMarker(true);
            }
        });

        document.addEventListener('DOMContentLoaded', initMap);
    </script>

</x-map-layout>
