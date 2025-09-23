<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold">Contractor Tracking Dashboard</h2>
                        <div class="text-sm text-gray-600">
                            Last updated: <span id="lastUpdate">Loading...</span>
                        </div>
                    </div>
                    
                    <div id="map" style="height: 600px; width: 100%; border-radius: 8px;"></div>
                    
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-blue-800">Active Contractors</h3>
                            <p class="text-2xl font-bold text-blue-600" id="activeCount">0</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-green-800">Total Locations</h3>
                            <p class="text-2xl font-bold text-green-600" id="locationCount">0</p>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-yellow-800">Last Update</h3>
                            <p class="text-sm text-yellow-600" id="lastLocationUpdate">-</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let map, markers = [];
        
        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 10,
                center: { lat: 40.7128, lng: -74.0060 }
            });
            
            loadContractorLocations();
            setInterval(loadContractorLocations, 30000);
        }
        
        function loadContractorLocations() {
            fetch('/admin/contractors/locations')
                .then(response => response.json())
                .then(data => {
                    clearMarkers();
                    
                    data.forEach(contractor => {
                        const marker = new google.maps.Marker({
                            position: { lat: parseFloat(contractor.latitude), lng: parseFloat(contractor.longitude) },
                            map: map,
                            title: contractor.name
                        });
                        
                        const infoWindow = new google.maps.InfoWindow({
                            content: `<div><h3>${contractor.name}</h3><p>Last seen: ${new Date(contractor.updated_at).toLocaleString()}</p></div>`
                        });
                        
                        marker.addListener('click', () => infoWindow.open(map, marker));
                        markers.push(marker);
                    });
                    
                    updateStats(data);
                    document.getElementById('lastUpdate').textContent = new Date().toLocaleTimeString();
                });
        }
        
        function clearMarkers() {
            markers.forEach(marker => marker.setMap(null));
            markers = [];
        }
        
        function updateStats(data) {
            document.getElementById('activeCount').textContent = data.length;
            document.getElementById('locationCount').textContent = data.length;
        }
    </script>
    
    <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&callback=initMap"></script>
</x-app-layout>