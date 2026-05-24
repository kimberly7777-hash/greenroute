<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use App\Models\Contractor;
use App\Notifications\NewServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class ServiceRequestController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'contact_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'notes' => ['nullable', 'string']
        ]);

        // Map to service request fields
        $sr = ServiceRequest::create([
            'name' => $data['contact_name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'],
            'address' => $data['address'] ?? null,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'notes' => $data['notes'] ?? null,
            'status' => 'pending'
        ]);

        // Find nearest contractor by latest location (simple nearest search)
        $nearestContractor = null;
        $minDistance = null;

        $contractors = Contractor::all();
        foreach ($contractors as $contractor) {
            $loc = $contractor->latestLocation();
            if (!$loc || !$sr->latitude || !$sr->longitude) {
                continue;
            }
            $d = $this->haversineDistance($sr->latitude, $sr->longitude, $loc->latitude, $loc->longitude);
            if (is_null($minDistance) || $d < $minDistance) {
                $minDistance = $d;
                $nearestContractor = $contractor;
            }
        }

        if ($nearestContractor) {
            $sr->contractor_id = $nearestContractor->user_id ?? $nearestContractor->id;
            $sr->save();

            // Notify contractor
            try {
                Notification::route('mail', $nearestContractor->email)
                    ->notify(new NewServiceRequest($sr, $nearestContractor));
            } catch (\Exception $e) {
                \Log::error('Failed to notify contractor of new service request: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Service request submitted. You will be contacted by your contractor.');
    }

    protected function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * asin(min(1, sqrt($a)));
        return $earthRadius * $c;
    }
}
