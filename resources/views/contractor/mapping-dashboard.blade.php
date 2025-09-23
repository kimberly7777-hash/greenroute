<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold">Route Planning Dashboard</h2>
                        <button id="updateLocation" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                            Update My Location
                        </button>
                    </div>
                    
                    <div id="map" style="height: 600px; width: 100%; border-radius: 8px;"></div>
                    
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-green-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-green-800">Assigned Clients</h3>
                            <p class="text-2xl font-bold text-green-600" id="clientCount">0</p>
                        </div>
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-blue-800">Route Options</h3>
                            <button id="optimizeRoute" class="mt-2 bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                                Optimize Route
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let map, directionsService, directionsRenderer, markers = [], currentLocation;
        
        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 12,
                center: { lat: 40.7128, lng: -74.0060 }
            });
            
            directionsService = new google.maps.DirectionsService();
            directionsRenderer = new google.maps.DirectionsRenderer();
            directionsRenderer.setMap(map);
            
            loadClientLocations();
            getCurrentLocation();
            
            document.getElementById('updateLocation').addEventListener('click', updateMyLocation);
            document.getElementById('optimizeRoute').addEventListener('click', optimizeRoute);
        }
        
        function loadClientLocations() {
            fetch('/contractor/clients/locations')
                .then(response => response.json())
                .then(clients => {
                    clients.forEach(client => {
                        const marker = new google.maps.Marker({
                            position: { lat: parseFloat(client.latitude), lng: parseFloat(client.longitude) },
                            map: map,
                            title: client.name,
                            icon: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png'
                        });
                        
                        const infoWindow = new google.maps.InfoWindow({
                            content: `<div><h3>${client.name}</h3><p>${client.address}</p><p>Phone: ${client.phone}</p></div>`
                        });
                        
                        marker.addListener('click', () => {
                            infoWindow.open(map, marker);
                            if (currentLocation) {
                                calculateRoute(currentLocation, { lat: parseFloat(client.latitude), lng: parseFloat(client.longitude) });
                            }
                        });
                        
                        markers.push(marker);
                    });
                    
                    document.getElementById('clientCount').textContent = clients.length;
                });
        }
        
        function getCurrentLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(position => {
                    currentLocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    
                    new google.maps.Marker({
                        position: currentLocation,
                        map: map,
                        title: 'My Location',
                        icon: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png'
                    });
                    
                    map.setCenter(currentLocation);
                });
            }
        }
        
        function updateMyLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(position => {
                    const location = {
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude
                    };
                    
                    fetch('/location/update', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(location)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Location updated successfully!');
                        }
                    });
                });
            }
        }
        
        function calculateRoute(start, end) {
            directionsService.route({
                origin: start,
                destination: end,
                travelMode: google.maps.TravelMode.DRIVING
            }, (result, status) => {
                if (status === 'OK') {
                    directionsRenderer.setDirections(result);
                }
            });
        }
        
        function optimizeRoute() {
            if (!currentLocation || markers.length === 0) return;
            
            const waypoints = markers.slice(0, 8).map(marker => ({
                location: marker.getPosition(),
                stopover: true
            }));
            
            directionsService.route({
                origin: currentLocation,
                destination: currentLocation,
                waypoints: waypoints,
                optimizeWaypoints: true,
                travelMode: google.maps.TravelMode.DRIVING
            }, (result, status) => {
                if (status === 'OK') {
                    directionsRenderer.setDirections(result);
                }
            });
        }
    </script>
    
    <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&callback=initMap&libraries=geometry"></script>
</x-app-layout>