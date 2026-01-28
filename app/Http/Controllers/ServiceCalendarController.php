<?php

namespace App\Http\Controllers;

use App\Models\ServiceSchedule;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ServiceCalendarController extends Controller
{
    /**
     * Display the service calendar page.
     */
    public function index(): Response
    {
        $schedules = ServiceSchedule::with('customer')
            ->orderBy('start_time')
            ->get()
            ->map(function ($schedule) {
                return [
                    'id' => $schedule->id,
                    'title' => $schedule->customer?->full_name ?? 'Unknown Customer',
                    'start' => $schedule->start_time?->toIso8601String(),
                    'end' => $schedule->end_time?->toIso8601String(),
                    'customer_name' => $schedule->customer?->full_name,
                    'customer_address' => $schedule->site_address ?? $schedule->customer?->street,
                    'service_requested' => $schedule->service_requested,
                    'service_status' => $schedule->service_status,
                    'crew_assigned' => $schedule->crew_assigned,
                    'crew_status' => $schedule->crew_status,
                    'service_notes' => $schedule->service_notes,
                    'crew_comments' => $schedule->crew_comments,
                    'cust_id' => $schedule->cust_id,
                ];
            });

        return Inertia::render('service-calendar/Index', [
            'schedules' => $schedules,
        ]);
    }

    /**
     * Update event date/time via drag and drop.
     */
    public function updateEvent(Request $request, ServiceSchedule $schedule): JsonResponse
    {
        $validated = $request->validate([
            'start_time' => 'required|date',
            'end_time' => 'nullable|date',
        ]);

        // Convert datetime from user's timezone to UTC
        if (! empty($validated['start_time'])) {
            $validated['start_time'] = Carbon::parse($validated['start_time'], $request->header('X-Timezone', config('app.timezone')))->utc();
        }
        if (! empty($validated['end_time'])) {
            $validated['end_time'] = Carbon::parse($validated['end_time'], $request->header('X-Timezone', config('app.timezone')))->utc();
        }

        $schedule->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Event updated successfully.',
        ]);
    }
}
