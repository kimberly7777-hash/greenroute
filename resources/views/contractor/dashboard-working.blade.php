<!DOCTYPE html>
<html>
<head>
    <title>Contractor Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <nav class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <h1 class="text-xl font-bold">AFIA ORBIT - Contractor</h1>
                    </div>
                    <div class="flex items-center">
                        <span class="mr-4">{{ auth()->user()->name }}</span>
                        <a href="/logout" class="text-red-600">Logout</a>
                    </div>
                </div>
            </div>
        </nav>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-bold">Route Planning Dashboard</h2>
                            <button id="updateLocation" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                Update My Location
                            </button>
                        </div>
                        
                        <div class="bg-gray-200 rounded-lg p-8 text-center mb-6">
                            <p class="text-gray-600 mb-4">Map will load here once Google Maps APIs are properly configured</p>
                            <div id="mapPlaceholder" class="h-96 bg-gray-300 rounded flex items-center justify-center">
                                <span class="text-gray-500">Google Maps Loading...</span>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-green-50 p-4 rounded-lg">
                                <h3 class="font-semibold text-green-800">Assigned Clients</h3>
                                <p class="text-2xl font-bold text-green-600" id="clientCount">Loading...</p>
                            </div>
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <h3 class="font-semibold text-blue-800">Route Options</h3>
                                <button id="optimizeRoute" class="mt-2 bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                                    Optimize Route
                                </button>
                            </div>
                            <div class="bg-yellow-50 p-4 rounded-lg">
                                <h3 class="font-semibold text-yellow-800">Status</h3>
                                <p class="text-sm text-yellow-600">Ready for mapping</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Load client count
        fetch('/contractor/clients/locations')
            .then(response => response.json())
            .then(clients => {
                document.getElementById('clientCount').textContent = clients.length;
            })
            .catch(() => {
                document.getElementById('clientCount').textContent = '0';
            });

        // Update location button
        document.getElementById('updateLocation').addEventListener('click', function() {
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
            } else {
                alert('Geolocation not supported');
            }
        });
    </script>
</body>
</html>