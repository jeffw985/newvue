<?php

namespace App\Http\Controllers;

use App\Models\ServiceSchedule;
use Inertia\Inertia;
use Inertia\Response;

class CrewScheduleController extends Controller
{
    public function index(): Response
    {
        $schedules = ServiceSchedule::query()
            ->with(['customer.areaGroup'])
            ->where('service_status', 'Scheduled')
            ->orderBy('start_time', 'asc')
            ->get();

        return Inertia::render('CrewSchedule', [
            'schedules' => $schedules,
        ]);
    }
}
