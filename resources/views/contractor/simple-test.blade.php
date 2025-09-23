<!DOCTYPE html>
<html>
<head>
    <title>Contractor Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h1>Contractor Dashboard Test</h1>
    <p>User: {{ auth()->user()->name ?? 'Not logged in' }}</p>
    <p>User Type: {{ auth()->user()->user_type ?? 'No type' }}</p>
    
    <div id="map" style="height: 400px; width: 100%; background: #ccc;"></div>
    
    <script>
        function initMap() {
            const map = new google.maps.Map(document.getElementById('map'), {
                zoom: 10,
                center: { lat: 40.7128, lng: -74.0060 }
            });
        }
    </script>
    
    <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&callback=initMap"></script>
</body>
</html>