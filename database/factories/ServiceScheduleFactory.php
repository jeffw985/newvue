<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceSchedule>
 */
class ServiceScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cust_id' => \App\Models\Customer::factory(),
            'start_time' => $this->faker->dateTimeBetween('now', '+1 month'),
            'end_time' => function (array $attributes) {
                return \Illuminate\Support\Carbon::parse($attributes['start_time'])->addHours(2);
            },
            'service_status' => 'Pending',
            'service_requested' => [$this->faker->word(), $this->faker->word()],
            'service_notes' => $this->faker->sentence(),
            'site_address' => $this->faker->address(),
            'crew_assigned' => $this->faker->name(),
        ];
    }
}
