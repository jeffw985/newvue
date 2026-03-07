<?php

use App\Models\ServiceSchedule;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('crew schedule page is displayed', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('crew-schedule.index'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('CrewSchedule')
        ->has('schedules')
    );
});

test('crew schedule page only shows scheduled records in ascending order', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $schedule1 = ServiceSchedule::factory()->create([
        'service_status' => 'Scheduled',
        'start_time' => now()->addDays(2),
    ]);
    $schedule2 = ServiceSchedule::factory()->create([
        'service_status' => 'Scheduled',
        'start_time' => now()->addDay(),
    ]);
    $schedule3 = ServiceSchedule::factory()->create([
        'service_status' => 'Completed',
        'start_time' => now(),
    ]);

    $response = $this->get(route('crew-schedule.index'));

    $response->assertInertia(fn (Assert $page) => $page
        ->has('schedules', 2)
        ->where('schedules.0.id', $schedule2->id)
        ->where('schedules.1.id', $schedule1->id)
    );
});
