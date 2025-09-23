<?php

namespace App\Http\Controllers;

use App\Models\ContractorLocation;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
}