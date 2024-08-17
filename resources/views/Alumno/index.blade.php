<x-map-layout>
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


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script>
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
    </script>

</x-map-layout>
