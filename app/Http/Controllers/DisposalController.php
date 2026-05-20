<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DisposalController extends Controller
{
    public function index()
    {
        $schedules = Schedule::forContractor(Auth::id())
            ->where('status', 'completed')
            ->with('client')
            ->orderBy('pickup_date', 'desc')
            ->paginate(15);

        return view('disposal.index', compact('schedules'));
    }

    public function create()
    {
        $pendingSchedules = Schedule::forContractor(Auth::id())
            ->where('status', 'completed')
            ->whereNull('disposal_site')
            ->with('client')
            ->orderBy('pickup_date', 'desc')
            ->paginate(15);

        return view('disposal.create', compact('pendingSchedules'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'schedule_id' => 'required|integer|exists:schedules,id',
            'service_type' => 'required|in:collection,disposal,both',
            'estimated_duration' => 'nullable|numeric|min:0',
            'total_volume' => 'required|numeric|min:0',
            'disposal_site' => 'required|string|max:255',
            'disposal_type' => 'required|in:sorting_facility,landfill',
            'disposal_notes' => 'nullable|string',
        ]);

        $schedule = Schedule::forContractor(Auth::id())
            ->where('status', 'completed')
            ->whereNull('disposal_site')
            ->findOrFail($validated['schedule_id']);

        $schedule->update([
            'service_type' => $validated['service_type'],
            'estimated_duration' => $validated['estimated_duration'],
            'total_volume' => $validated['total_volume'],
            'disposal_site' => $validated['disposal_site'],
            'disposal_type' => $validated['disposal_type'],
            'disposal_notes' => $validated['disposal_notes'],
        ]);

        return redirect()->route('disposal.index')->with('success', 'Disposal schedule has been recorded successfully.');
    }

    public function show(Schedule $schedule)
    {
        if ($schedule->contractor_id !== Auth::id()) {
            abort(404);
        }

        return view('disposal.show', compact('schedule'));
    }

    public function edit(Schedule $schedule)
    {
        if ($schedule->contractor_id !== Auth::id()) {
            abort(404);
        }

        return view('disposal.edit', compact('schedule'));
    }

    public function update(Request $request, Schedule $schedule)
    {
        if ($schedule->contractor_id !== Auth::id()) {
            abort(404);
        }

        $validated = $request->validate([
            'total_volume' => 'required|numeric|min:0',
            'disposal_site' => 'required|string|max:255',
            'disposal_type' => 'required|in:sorting_facility,landfill',
            'disposal_notes' => 'nullable|string'
        ]);

        $schedule->update([
            'total_volume' => $validated['total_volume'],
            'disposal_site' => $validated['disposal_site'],
            'disposal_type' => $validated['disposal_type'],
            'disposal_notes' => $validated['disposal_notes']
        ]);

        return redirect()->route('disposal.index')->with('success', 'Disposal data recorded successfully');
    }
}