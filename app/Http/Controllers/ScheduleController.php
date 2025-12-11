<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Client;
use App\Models\Location;
use App\Models\ContractorRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    public function index()
    {
        // Only show non-completed schedules (completed ones go to disposal tab)
        $schedules = Schedule::forContractor(Auth::id())
            ->whereIn('status', ['scheduled', 'in_progress', 'cancelled'])
            ->with('client')
            ->orderBy('pickup_date', 'desc')
            ->paginate(15);

        return view('schedules.index', compact('schedules'));
    }

    public function create()
    {
        // Check if user is a contractor and redirect to contractor-specific view
        if (Auth::user()->user_type === 'contractor') {
            $contractor = Auth::user();
            $clients = Client::where('contractor_id', Auth::id())
                ->select('id', 'name', 'registration_number', 'email', 'phone', 'address', 'city', 'region', 'district', 'ward', 'street', 'route')
                ->get();
            
            // Get regions only - for initial dropdown (dependent dropdowns)
            $regions = collect([]);
            if (Schema::hasTable('tbl_locations')) {
                try {
                    $regions = Location::select('region')
                        ->distinct()
                        ->orderBy('region')
                        ->pluck('region');
                } catch (\Exception $e) {
                    $regions = collect([]);
                }
            }
            
            // Get routes with their assigned locations (only active routes with locations)
            $routes = ContractorRoute::where('contractor_id', Auth::id())
                ->where('is_active', true)
                ->whereNotNull('region')
                ->select('id', 'route_name', 'region', 'district', 'ward', 'street')
                ->orderBy('route_name')
                ->get();
            
            // Pass empty collections as defaults to avoid null errors
            $siteLocations = collect([]);
            $assignedClient = $clients->first();
            
            return view('contractor.create-schedule', compact('contractor', 'clients', 'assignedClient', 'regions', 'routes', 'siteLocations'));
        }
        
        // Admin view (simplified for now)
        $regions = collect([]);
        if (Schema::hasTable('tbl_locations')) {
            try {
                $regions = Location::select('region')->distinct()->orderBy('region')->pluck('region');
            } catch (\Exception $e) {
                $regions = collect([]);
            }
        }
        return view('schedules.create', compact('regions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_ids' => 'required|array',
            'client_ids.*' => 'exists:clients,id',
            'pickup_date' => 'required|date',
            'pickup_time' => 'required',
            'pickup_location' => 'required|string',
            'service_type' => 'required|string',
            'estimated_duration' => 'nullable|numeric',
            'total_volume' => 'nullable|numeric',
            'disposal_site' => 'nullable|string',
            'notes' => 'nullable|string',
            'site_location' => 'nullable|string', // Just for validation
        ]);

        $contractor = Auth::user();

        DB::transaction(function () use ($validated, $contractor) {
            foreach ($validated['client_ids'] as $clientId) {
                $client = Client::findOrFail($clientId);

                Schedule::create([
                    'contractor_id' => $contractor->id,
                    'client_id' => $client->id,
                    'contractor_registration_number' => $contractor->registration_number,
                    'client_registration_number' => $client->registration_number,
                    'route' => $client->route ?? 'Not Assigned',
                    'pickup_date' => $validated['pickup_date'],
                    'pickup_time' => $validated['pickup_time'],
                    'scheduled_date' => $validated['pickup_date'], // Default to pickup date
                    'scheduled_time' => $validated['pickup_time'],
                    'pickup_location' => $validated['pickup_location'],
                    'pickup_address' => $client->address ?? 'Not Provided',
                    'city' => $client->city ?? 'Not Provided',
                    'state' => $client->state ?? 'Not Provided',
                    'zip_code' => $client->zip_code ?? '00000',
                    'service_type' => $validated['service_type'],
                    'status' => 'scheduled',
                    'estimated_duration' => $validated['estimated_duration'] ?? null,
                    'total_volume' => $validated['total_volume'] ?? null,
                    'disposal_site' => $validated['disposal_site'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                ]);
            }
        });

        return redirect()->route('schedules.index')
            ->with('success', 'Schedules created successfully.');
    }

    public function show(Schedule $schedule)
    {
        if ($schedule->contractor_id !== Auth::id()) {
            abort(404);
        }

        // Get all schedules for the same location and date range
        $locationSchedules = Schedule::forContractor(Auth::id())
            ->where('pickup_location', $schedule->pickup_location)
            ->where('pickup_date', '>=', $schedule->pickup_date)
            ->with('client')
            ->orderBy('pickup_address')
            ->get();

        return view('schedules.show', compact('schedule', 'locationSchedules'));
    }

    public function updateStatus(Request $request, Schedule $schedule)
    {
        if ($schedule->contractor_id !== Auth::id()) {
            abort(404);
        }

        $validated = $request->validate([
            'status' => 'required|in:scheduled,in_progress,completed,cancelled'
        ]);

        $oldStatus = $schedule->status;
        $schedule->update(['status' => $validated['status']]);

        // Add message when marking as completed
        $message = 'Status updated successfully';
        if ($validated['status'] === 'completed' && $oldStatus !== 'completed') {
            $message = 'Schedule marked as completed! Please record disposal data in the Disposal section.';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'redirectToDisposal' => $validated['status'] === 'completed'
        ]);
    }

    public function print(Schedule $schedule)
    {
        if ($schedule->contractor_id !== Auth::id()) {
            abort(404);
        }

        $locationSchedules = Schedule::forContractor(Auth::id())
            ->where('pickup_location', $schedule->pickup_location)
            ->where('pickup_date', '>=', $schedule->pickup_date)
            ->with('client')
            ->orderBy('pickup_address')
            ->get();

        return view('schedules.print', compact('schedule', 'locationSchedules'));
    }
}