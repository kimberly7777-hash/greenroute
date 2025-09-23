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
        let map, markers = [], currentLocation;
        
        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 12,
                center: { lat: 40.7128, lng: -74.0060 }
            });
            
            loadClientLocations();
            getCurrentLocation();
            
            document.getElementById('updateLocation').onclick = updateMyLocation;
            document.getElementById('optimizeRoute').onclick = optimizeRoute;
        }
        
        function loadClientLocations() {
            fetch('/contractor/clients/locations')
                .then(response => response.json())
                .then(clients => {
                    clients.forEach(client => {
                        const marker = new google.maps.Marker({
                            position: { lat: parseFloat(client.latitude), lng: parseFloat(client.longitude) },
                            map: map,
                            title: client.name
                        });
                        markers.push(marker);
                    });
                    document.getElementById('clientCount').textContent = clients.length;
                })
                .catch(() => document.getElementById('clientCount').textContent = '0');
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
                    fetch('/location/update', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            latitude: position.coords.latitude,
                            longitude: position.coords.longitude
                        })
                    })
                    .then(response => response.json())
                    .then(data => alert(data.success ? 'Location updated!' : 'Error updating location'));
                });
            }
        }
        
        function optimizeRoute() {
            alert('Route optimization feature - requires more client data');
        }
    </script>
    
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBcwt701YioUFnzbJp9Bktla31qjKwM304&callback=initMap"></script>
</x-app-layout>