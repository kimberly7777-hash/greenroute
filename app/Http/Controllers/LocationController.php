<?php

namespace App\Http\Controllers;

use App\Models\ContractorLocation;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LocationController extends Controller
{
    public function updateContractorLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180'
        ]);

        ContractorLocation::create([
            'contractor_id' => auth()->id(),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude
        ]);

        return response()->json(['success' => true]);
    }

    public function getContractorLocations()
    {
        $locations = ContractorLocation::with('contractor:id,name')
            ->select('contractor_id', 'latitude', 'longitude', 'created_at')
            ->whereHas('contractor', function($query) {
                $query->where('user_type', 'contractor');
            })
            ->get()
            ->groupBy('contractor_id')
            ->map(function($locations) {
                return $locations->first();
            });

        return response()->json($locations->values());
    }

    public function getClientLocations()
    {
        $clients = Client::where('contractor_id', auth()->id())
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select('id', 'name', 'address', 'latitude', 'longitude')
            ->get();

        return response()->json($clients);
    }

    public function geocodeAddress(Request $request)
    {
        $request->validate(['address' => 'required|string']);
        
        $apiKey = config('services.google_maps.api_key');
        $address = urlencode($request->address);
        
        $response = Http::get("https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key={$apiKey}");
        
        if ($response->successful() && $response->json()['status'] === 'OK') {
            $location = $response->json()['results'][0]['geometry']['location'];
            return response()->json([
                'success' => true,
                'latitude' => $location['lat'],
                'longitude' => $location['lng']
            ]);
        }
        
        return response()->json(['success' => false], 400);
    }
    
    public function validateLocationAccuracy(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy' => 'nullable|numeric'
        ]);
        
        $lat = (float) $request->latitude;
        $lng = (float) $request->longitude;
        $accuracy = $request->accuracy;
        
        // Check if coordinates are within Tanzania bounds
        $inTanzania = ($lat >= -11.7 && $lat <= -0.95 && $lng >= 29.3 && $lng <= 40.5);
        
        // Check if coordinates are within Moshi area (more specific)
        $inMoshi = ($lat >= -3.5 && $lat <= -3.2 && $lng >= 37.2 && $lng <= 37.4);
        
        return response()->json([
            'valid' => $inTanzania,
            'in_tanzania' => $inTanzania,
            'in_moshi' => $inMoshi,
            'accuracy_good' => $accuracy ? $accuracy < 50 : null,
            'message' => $inTanzania ? 
                ($inMoshi ? 'Location confirmed in Moshi, Tanzania' : 'Location confirmed in Tanzania') :
                'Location appears to be outside Tanzania'
        ]);
    }
}