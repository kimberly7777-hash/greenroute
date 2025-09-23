<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ContractorLocation;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function getContractorLocations()
    {
        $contractors = User::where('user_type', 'contractor')
            ->with(['contractorLocations' => function($query) {
                $query->latest()->limit(1);
            }])
            ->get()
            ->filter(function($contractor) {
                return $contractor->contractorLocations->isNotEmpty();
            })
            ->map(function($contractor) {
                $location = $contractor->contractorLocations->first();
                return [
                    'id' => $contractor->id,
                    'name' => $contractor->name,
                    'latitude' => $location->latitude,
                    'longitude' => $location->longitude,
                    'updated_at' => $location->created_at
                ];
            });

        return response()->json($contractors->values());
    }
}