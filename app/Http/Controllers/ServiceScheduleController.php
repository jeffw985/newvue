<?php

namespace App\Http\Controllers;

use App\Models\ServiceSchedule;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ServiceScheduleController extends Controller
{
    /**
     * Display a listing of scheduled service records.
     */
    public function index(Request $request): Response
    {
        $search = $request->input('search');

        $schedules = ServiceSchedule::query()
            ->with(['customer:id,full_name,street', 'customer.areaGroup', 'maintenance'])
            ->where('service_status', 'Scheduled')
            ->when($search, function ($query, $search) {
                $searchTerm = strtolower($search);
                $query->where(function ($q) use ($searchTerm) {
                    $q->whereHas('customer', function ($cq) use ($searchTerm) {
                        $cq->whereRaw('LOWER(full_name) LIKE ?', ["%$searchTerm%"]);
                    })->orWhereRaw('LOWER(site_address) LIKE ?', ["%$searchTerm%"]);
                });
            })
            ->orderBy('start_time', 'asc')
            ->get();

        return Inertia::render('service-schedules/Index', [
            'schedules' => $schedules,
            'search' => $search,
        ]);
    }

    /**
     * Store a newly created service schedule.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'cust_id' => 'required|exists:customers,id',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date',
            'service_requested' => 'nullable|array',
            'service_status' => 'nullable|string|max:255',
            'crew_assigned' => 'nullable|string|max:255',
            'crew_status' => 'nullable|string|max:255',
            'service_notes' => 'nullable|string',
            'crew_comments' => 'nullable|string',
            'site_address' => 'nullable|string|max:255',
        ]);

        // Convert date inputs from user's timezone to UTC
        $userTimezone = $request->header('X-Timezone', config('app.timezone'));
        if (! empty($validated['start_time'])) {
            $validated['start_time'] = Carbon::parse($validated['start_time'], $userTimezone)->utc();
        }
        if (! empty($validated['end_time'])) {
            $validated['end_time'] = Carbon::parse($validated['end_time'], $userTimezone)->utc();
        }

        $schedule = ServiceSchedule::create($validated);

        return back()->with([
            'success' => 'Service schedule created successfully.',
            'schedule' => $schedule,
        ]);
    }

    /**
     * Update the specified service schedule.
     */
    public function update(Request $request, ServiceSchedule $schedule): RedirectResponse
    {
        $validated = $request->validate([
            'cust_id' => 'required|exists:customers,id',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date',
            'service_requested' => 'nullable|array',
            'service_status' => 'nullable|string|max:255',
            'crew_assigned' => 'nullable|string|max:255',
            'crew_status' => 'nullable|string|max:255',
            'service_notes' => 'nullable|string',
            'crew_comments' => 'nullable|string',
            'site_address' => 'nullable|string|max:255',
        ]);

        // Convert date inputs from user's timezone to UTC
        $userTimezone = $request->header('X-Timezone', config('app.timezone'));
        if (! empty($validated['start_time'])) {
            $validated['start_time'] = Carbon::parse($validated['start_time'], $userTimezone)->utc();
        }
        if (! empty($validated['end_time'])) {
            $validated['end_time'] = Carbon::parse($validated['end_time'], $userTimezone)->utc();
        }

        $schedule->update($validated);

        return back()->with([
            'success' => 'Service schedule updated successfully.',
            'schedule' => $schedule,
        ]);
    }

    /**
     * Remove the specified service schedule.
     */
    public function destroy(ServiceSchedule $schedule): RedirectResponse
    {
        $schedule->delete();

        return back()->with('success', 'Service schedule deleted successfully.');
    }
}
