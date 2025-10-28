<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\LocationService;
use App\Http\Resources\LocationResource;

class LocationController extends Controller
{
    /**
     * Location service instance
     */
    protected $locationService;

    /**
     * Create a new controller instance.
     */
    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    /**
     * Reverse geocode coordinates to get address
     */
    public function reverseGeocode(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180'
        ]);

        $lat = $request->latitude;
        $lng = $request->longitude;
        $apiKey = config('services.google_maps.api_key');

        // Check if API key is configured
        if (empty($apiKey) || $apiKey === 'your_google_maps_api_key_here') {
            return $this->generateFallbackAddress($lat, $lng);
        }

        try {
            $response = Http::timeout(10)->get('https://maps.googleapis.com/maps/api/geocode/json', [
                'latlng' => "{$lat},{$lng}",
                'key' => $apiKey,
                'language' => 'en'
            ]);

            $data = $response->json();

            if ($data['status'] === 'OK' && !empty($data['results'])) {
                $result = $data['results'][0];
                $formattedAddress = $result['formatted_address'];

                return response()->json([
                    'success' => true,
                    'address' => $formattedAddress,
                    'components' => $result['address_components'] ?? []
                ]);
            }

            return $this->generateFallbackAddress($lat, $lng);

        } catch (\Exception $e) {
            Log::error('Reverse geocoding failed', [
                'latitude' => $lat,
                'longitude' => $lng,
                'error' => $e->getMessage()
            ]);

            return $this->generateFallbackAddress($lat, $lng);
        }
    }

    /**
     * Generate a fallback address when reverse geocoding is not available
     */
    private function generateFallbackAddress($lat, $lng)
    {
        // Basic location-based address generation for Tanzania
        $address = "GPS Location: {$lat}, {$lng}";
        
        // Check if coordinates are in known areas
        if ($lat >= -3.5 && $lat <= -3.2 && $lng >= 37.2 && $lng <= 37.4) {
            $address = "Moshi, Kilimanjaro Region, Tanzania (GPS: {$lat}, {$lng})";
        } elseif ($lat >= -6.9 && $lat <= -6.7 && $lng >= 39.1 && $lng <= 39.3) {
            $address = "Dar es Salaam, Tanzania (GPS: {$lat}, {$lng})";
        } elseif ($lat >= -11.7 && $lat <= -0.95 && $lng >= 29.3 && $lng <= 40.5) {
            $address = "Tanzania (GPS: {$lat}, {$lng})";
        }

        return response()->json([
            'success' => true,
            'address' => $address,
            'fallback' => true
        ]);
    }

    /**
     * Validate location accuracy for registration
     */
    public function validateLocationAccuracy(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy' => 'nullable|numeric'
        ]);

        $lat = (float) $request->latitude;
        $lng = (float) $request->longitude;

        // Tanzania bounds
        $inTanzania = ($lat >= -11.7 && $lat <= -0.95 && $lng >= 29.3 && $lng <= 40.5);
        
        // Moshi area bounds (more specific)
        $inMoshi = ($lat >= -3.5 && $lat <= -3.2 && $lng >= 37.2 && $lng <= 37.4);

        return response()->json([
            'in_tanzania' => $inTanzania,
            'in_moshi' => $inMoshi,
            'accuracy' => $request->accuracy
        ]);
    }

    /**
     * Get all regions from tbl_locations (with caching)
     */
    public function getRegions()
    {
        $regions = $this->locationService->getRegions();
            
        return response()->json([
            'success' => true,
            'data' => $regions,
            'cached' => true
        ]);
    }
    
    /**
     * Get districts for a specific region (with caching)
     */
    public function getDistricts(Request $request)
    {
        $request->validate([
            'region' => 'required|string'
        ]);

        $districts = $this->locationService->getDistricts($request->region);
            
        return response()->json([
            'success' => true,
            'data' => $districts,
            'cached' => true
        ]);
    }
    
    /**
     * Get wards for a specific district (with caching)
     */
    public function getWards(Request $request)
    {
        $request->validate([
            'region' => 'required|string',
            'district' => 'required|string'
        ]);

        $wards = $this->locationService->getWards($request->region, $request->district);
            
        return response()->json([
            'success' => true,
            'data' => $wards,
            'cached' => true
        ]);
    }
    
    /**
     * Get streets for a specific ward (with caching)
     */
    public function getStreets(Request $request)
    {
        $request->validate([
            'region' => 'required|string',
            'district' => 'required|string',
            'ward' => 'required|string'
        ]);

        $streets = $this->locationService->getStreets(
            $request->region,
            $request->district,
            $request->ward
        );
            
        return response()->json([
            'success' => true,
            'data' => $streets,
            'cached' => true
        ]);
    }

    /**
     * Search locations by keyword
     */
    public function searchLocations(Request $request)
    {
        $request->validate([
            'keyword' => 'required|string|min:2'
        ]);

        $results = $this->locationService->searchLocations(
            $request->keyword,
            $request->input('limit', 50)
        );
            
        return response()->json([
            'success' => true,
            'data' => LocationResource::collection($results),
            'count' => $results->count()
        ]);
    }

    /**
     * Get location statistics
     */
    public function getStatistics()
    {
        $stats = $this->locationService->getStatistics();
            
        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Validate a location exists
     */
    public function validateLocation(Request $request)
    {
        $request->validate([
            'region' => 'required|string',
            'district' => 'required|string',
            'ward' => 'required|string',
            'street' => 'nullable|string'
        ]);

        $exists = $this->locationService->validateLocation(
            $request->region,
            $request->district,
            $request->ward,
            $request->street
        );
            
        return response()->json([
            'success' => true,
            'exists' => $exists
        ]);
    }
}