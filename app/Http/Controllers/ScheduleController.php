<?php

/** @noinspection ALL */

namespace App\Http\Controllers;

use App\Models\ServiceSchedule;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /**
     * Store a newly created service schedule record.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'cust_id' => 'required|exists:customers,id',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date',
            'service_status' => 'nullable|string|max:255',
            'site_address' => 'nullable|string|max:255',
            'crew_assigned' => 'nullable|string|max:255',
            'crew_status' => 'nullable|string|max:255',
            'crew_comments' => 'nullable|string',
            'service_notes' => 'nullable|string',
            'service_requested' => 'nullable|array',
            'service_requested.*' => 'string|max:255',
        ]);

        // Convert datetime-local inputs from user's timezone to UTC
        if (! empty($validated['start_time'])) {
            $validated['start_time'] = Carbon::parse($validated['start_time'], $request->header('X-Timezone', config('app.timezone')))->utc();
        }
        if (! empty($validated['end_time'])) {
            $validated['end_time'] = Carbon::parse($validated['end_time'], $request->header('X-Timezone', config('app.timezone')))->utc();
        }

        $schedule = ServiceSchedule::create($validated);

        return back()->with([
            'success' => 'Service schedule created successfully.',
            'schedule' => $schedule,
        ]);
    }

    /**
     * Update the specified service schedule record.
     */
    public function update(Request $request, ServiceSchedule $schedule)
    {
        $validated = $request->validate([
            'cust_id' => 'required|exists:customers,id',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date',
            'service_status' => 'nullable|string|max:255',
            'site_address' => 'nullable|string|max:255',
            'crew_assigned' => 'nullable|string|max:255',
            'crew_status' => 'nullable|string|max:255',
            'crew_comments' => 'nullable|string',
            'service_notes' => 'nullable|string',
            'service_requested' => 'nullable|array',
            'service_requested.*' => 'string|max:255',
        ]);

        // Convert datetime-local inputs from user's timezone to UTC
        if (! empty($validated['start_time'])) {
            $validated['start_time'] = Carbon::parse($validated['start_time'], $request->header('X-Timezone', config('app.timezone')))->utc();
        }
        if (! empty($validated['end_time'])) {
            $validated['end_time'] = Carbon::parse($validated['end_time'], $request->header('X-Timezone', config('app.timezone')))->utc();
        }

        $schedule->update($validated);

        return back()->with([
            'success' => 'Service schedule updated successfully.',
            'schedule' => $schedule,
        ]);
    }

    /**
     * Remove the specified service schedule record.
     */
    public function destroy(ServiceSchedule $schedule): RedirectResponse
    {
        $schedule->delete();

        return back()->with('success', 'Service schedule deleted successfully.');
    }
}
