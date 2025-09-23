<?php

namespace App\Http\Controllers;

use App\Models\Client;

class ContractorController extends Controller
{
    public function dashboard()
    {
        return view('contractor.dashboard');
    }

    public function getAssignedClients()
    {
        $clients = Client::where('contractor_id', auth()->id())
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select('id', 'name', 'address', 'latitude', 'longitude', 'phone')
            ->get();

        return response()->json($clients);
    }
}