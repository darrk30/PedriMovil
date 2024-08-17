<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* [x-cloak] {
            display: none !important;
        } */
    </style>
    <style>
        /* Estilos del Spinner */
        .spinner {
            border: 4px solid rgba(0, 0, 0, 0.1);
            border-left-color: #3498db;
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
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            width: 80%;
            max-width: 400px;
            text-align: center;
        }

        .mensaje-recarga-api,
        .mensaje-recarga-gps,
        .mensaje-recarga-navegador {
            border-width: 1px;
            border-style: solid;
            padding: 10px;
        }

        .mensaje-recarga-api {
            border-color: #dc3545;
            color: #dc3545;
        }

        .mensaje-recarga-gps {
            border-color: #ffc107;
            color: #ffc107;
        }

        .mensaje-recarga-navegador {
            border-color: #17a2b8;
            color: #17a2b8;
        }

        /* Estilos para el mapa */
        html,
        body,
        #mapa {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        #mapa {
            outline: none;
            z-index: 1;
        }

        #cargando {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            background: white;
            z-index: 50;
        }
    </style>
</head>

<body class="font-sans antialiased">
    <div class="relative min-h-screen">
        <!-- Mapa -->
        <div id="mapa" class="absolute inset-0 z-0"></div>
        <!-- Contenido principal -->
        <div class="relative z-10">
            {{ $slot }}
        </div>

    </div>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCJJGvOLF0AX2sslRUJPouL0lR44XHZOKA&libraries=geometry&callback=initMap"
        async defer></script>



    {{-- <script>
        let map, marker, lastPosition, watchId;
        let ubicacionActiva = 1;
        //let posicionYEstadoInterval = null;
        const userId = {{ auth()->user()->id }};
        const positions = @json($positions);
        const isConductor = {{ $rolUser === 'Conductor' ? 'true' : 'false' }};

        if (isConductor) {

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
        } else {
            let map; // Mapa global
            const positionsUrl = '/get-latest-location';
            const markers = {}; // Objeto para almacenar los marcadores
            const paraderos = {
                lambayeque: {
                    position: {
                        lat: -6.707778,
                        lng: -79.905071
                    },
                    name: 'Lambayeque'
                },
                chiclayo: {
                    position: {
                        lat: -6.769064,
                        lng: -79.845935
                    },
                    name: 'Chiclayo'
                }
            };

            function initMap() {
                map = new google.maps.Map(document.getElementById('mapa'), {
                    center: {
                        lat: -6.7580,
                        lng: -79.8900
                    },
                    zoom: 12,
                    disableDefaultUI: true,
                    styles: [{
                        featureType: 'poi.business',
                        stylers: [{
                            visibility: 'off'
                        }]
                    }],
                    gestureHandling: 'greedy'
                });

                // Crear los marcadores de los paraderos
                for (const key in paraderos) {
                    const paradero = paraderos[key];
                    new google.maps.Marker({
                        position: paradero.position,
                        map: map,
                        icon: {
                            url: "{{ asset('storage/image/paradero.png') }}",
                            scaledSize: new google.maps.Size(50, 50)
                        },
                        title: paradero.name
                    });
                }

                const infoWindow = new google.maps.InfoWindow();

                function calculateEstimatedTime(currentPosition, destinationPosition, callback) {
                    const service = new google.maps.DistanceMatrixService();

                    service.getDistanceMatrix({
                        origins: [currentPosition],
                        destinations: [destinationPosition],
                        travelMode: 'DRIVING'
                    }, (response, status) => {
                        if (status === 'OK') {
                            const result = response.rows[0].elements[0];
                            if (result.status === 'OK') {
                                callback(result.duration.value); // Devuelve la duración en segundos
                            } else {
                                console.error('Error en la respuesta de Distance Matrix:', result.status);
                                callback(null);
                            }
                        } else {
                            console.error('Error en Distance Matrix:', status);
                            callback(null);
                        }
                    });
                }

                function updateAllMarkers() {
                    fetch(positionsUrl)
                        .then(response => response.json())
                        .then(data => {
                            console.log('Datos obtenidos desde la URL:', data);

                            data.forEach(pos => {
                                const latitud = parseFloat(pos.latitud);
                                const longitud = parseFloat(pos.longitud);
                                const id = pos.id;

                                if (!isNaN(latitud) && !isNaN(longitud)) {
                                    const newLatLng = new google.maps.LatLng(latitud, longitud);

                                    if (markers[id]) {
                                        animateMarker(markers[id], newLatLng);
                                    } else {
                                        const marker = new google.maps.Marker({
                                            position: newLatLng,
                                            map: map,
                                            icon: {
                                                url: "{{ asset('storage/image/bus.png') }}",
                                                scaledSize: new google.maps.Size(50, 50)
                                            },
                                            title: `ID: ${id}`,
                                            id: id
                                        });

                                        marker.addListener('click', function() {
                                            const currentPosition = marker.getPosition();
                                            const destinationLambayeque = paraderos.lambayeque.position;
                                            const destinationChiclayo = paraderos.chiclayo.position;

                                            // Calcular el tiempo estimado hacia ambos paraderos
                                            calculateEstimatedTime(currentPosition,
                                                destinationLambayeque, (timeToLambayeque) => {
                                                    calculateEstimatedTime(currentPosition,
                                                        destinationChiclayo, (
                                                        timeToChiclayo) => {
                                                            if (timeToLambayeque !== null &&
                                                                timeToChiclayo !== null) {
                                                                // Tiempo total considerando parada en Lambayeque
                                                                const contentString = `
                                                        <div><strong>ID del bus:</strong> ${this.id}</div>
                                                        <div><strong>Tiempo estimado de llegada:</strong></div>
                                                        <div>Lambayeque: ${Math.ceil(timeToLambayeque / 60)} minutos</div>
                                                        <div>Chiclayo: ${Math.ceil(timeToChiclayo / 60)} minutos</div>
                                                    `;
                                                                infoWindow.setContent(
                                                                    contentString);
                                                                infoWindow.open(map, marker);
                                                            } else {
                                                                infoWindow.setContent(
                                                                    '<div>Error al calcular el tiempo estimado.</div>'
                                                                    );
                                                                infoWindow.open(map, marker);
                                                            }
                                                        });
                                                });
                                        });

                                        markers[id] = marker;
                                    }
                                } else {
                                    console.error(`Coordenadas inválidas: lat=${latitud}, lng=${longitud}`);
                                }
                            });
                        })
                        .catch(error => console.error('Error al obtener los datos:', error));
                }

                function animateMarker(marker, newLatLng) {
                    const startLatLng = marker.getPosition();
                    const startTime = Date.now();
                    const duration = 1000;

                    function update() {
                        const elapsedTime = Date.now() - startTime;
                        const fraction = Math.min(elapsedTime / duration, 1);

                        const lat = startLatLng.lat() + (newLatLng.lat() - startLatLng.lat()) * fraction;
                        const lng = startLatLng.lng() + (newLatLng.lng() - startLatLng.lng()) * fraction;

                        marker.setPosition(new google.maps.LatLng(lat, lng));

                        if (fraction < 1) {
                            requestAnimationFrame(update);
                        }
                    }

                    update();
                }

                updateAllMarkers();
                setInterval(updateAllMarkers, 3000);
            }

            window.onload = initMap;


        }


        let wakeLock = null;

        async function requestWakeLock() {
            try {
                wakeLock = await navigator.wakeLock.request('screen');
                wakeLock.addEventListener('release', () => {
                    console.log('Screen Wake Lock was released');
                });
                console.log('Screen Wake Lock is active');
            } catch (err) {
                console.error(`${err.name}, ${err.message}`);
            }
        }

        document.addEventListener('DOMContentLoaded', requestWakeLock);
    </script> --}}



    {{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script> --}}

    @stack('scripts')
</body>

</html>
