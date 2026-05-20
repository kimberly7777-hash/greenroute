<!DOCTYPE html>
<html>
<head>
    <title>Mapbox API Test</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        #map {
            height: 400px;
            width: 100%;
        }
        .status {
            padding: 20px;
            margin: 20px;
            border-radius: 5px;
        }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <h1>Mapbox API Test</h1>
    <div id="status" class="status">Loading...</div>
    <div id="map"></div>

    <link href="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css" rel="stylesheet" />
    <script src="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js"></script>
    <script>
        mapboxgl.accessToken = '{{ config('services.mapbox.token') }}';

        function initMap() {
            const statusDiv = document.getElementById('status');

            try {
                const map = new mapboxgl.Map({
                    container: 'map',
                    style: 'mapbox://styles/mapbox/streets-v11',
                    zoom: 10,
                    center: [39.2083, -6.7924]
                });

                new mapboxgl.Marker()
                    .setLngLat([39.2083, -6.7924])
                    .setPopup(new mapboxgl.Popup({ offset: 25 }).setText('Test Location'))
                    .addTo(map);

                statusDiv.className = 'status success';
                statusDiv.innerHTML = '✓ Mapbox API is working correctly!';
            } catch (error) {
                statusDiv.className = 'status error';
                statusDiv.innerHTML = '✗ Error: ' + error.message;
            }
        }

        function authFailure() {
            const statusDiv = document.getElementById('status');
            statusDiv.className = 'status error';
            statusDiv.innerHTML = '✗ Mapbox authentication failed. Check your token.';
        }

        // Fallback if initMap is not called within 10 seconds
        setTimeout(function() {
            const statusDiv = document.getElementById('status');
            if (statusDiv.innerHTML === 'Loading...') {
                statusDiv.className = 'status error';
                statusDiv.innerHTML = '✗ Mapbox API failed to load within 10 seconds.';
            }
        }, 10000);

        document.addEventListener('DOMContentLoaded', initMap);
    </script>
</body>
</html>