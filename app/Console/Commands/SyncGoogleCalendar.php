<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncGoogleCalendar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'google-calendar:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync events from Google Calendar to the application';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Starting Google Calendar sync...');

        // Get the latest last_synced_at to determine how far back to look
        $lastSyncedAt = \App\Models\ServiceSchedule::whereNotNull('last_synced_at')
            ->max('last_synced_at');

        // Fallback to 1 day ago if never synced
        $since = $lastSyncedAt ? \Carbon\Carbon::parse($lastSyncedAt) : now()->subDay();

        $this->comment("Fetching events modified since: {$since->toDateTimeString()}");

        try {
            // Fetch events from Google
            // We use 'updatedMin' to only get events modified after $since.
            // We set startDateTime to null to avoid the default timeMin filter.
            $events = \Spatie\GoogleCalendar\Event::get(null, null, [
                'updatedMin' => $since->toRfc3339String(),
                'showDeleted' => true,
            ]);

            if ($events->isEmpty()) {
                $this->info('No events found to sync.');

                return;
            }

            foreach ($events as $googleEvent) {
                $schedule = \App\Models\ServiceSchedule::withTrashed()->where('google_event_id', $googleEvent->id)->first();

                if ($schedule) {
                    if ($googleEvent->status === 'cancelled') {
                        $this->line("Deleting schedule for event (cancelled in Google): {$googleEvent->name}");
                        $schedule->delete();

                        continue;
                    }

                    $this->line("Updating schedule for event: {$googleEvent->name}");

                    // Use withoutEvents to prevent the observer from pushing back to Google
                    $schedule->withoutEvents(function () use ($schedule, $googleEvent) {
                        $schedule->update([
                            'start_time' => $googleEvent->startDateTime->setTimezone(config('app.timezone')),
                            'end_time' => $googleEvent->endDateTime->setTimezone(config('app.timezone')),
                            'service_notes' => $this->parseDescription($googleEvent->description),
                            'last_synced_at' => now(),
                        ]);
                    });
                }
            }

            $this->info('Google Calendar sync completed successfully.');
        } catch (\Exception $e) {
            $this->error('Error syncing Google Calendar: '.$e->getMessage());
            \Log::error('Google Calendar Inbound Sync Error: '.$e->getMessage());
        }
    }

    /**
     * Parse the description to extract notes if possible, or just return it.
     */
    protected function parseDescription(?string $description): ?string
    {
        if (! $description) {
            return null;
        }

        // The observer puts "Service Notes: ..." at the beginning.
        // If we want to be smart, we could try to extract it, but for now,
        // let's just keep the description as is or look for our marker.
        if (str_starts_with($description, 'Service Notes: ')) {
            $lines = explode("\n", $description);

            return str_replace('Service Notes: ', '', $lines[0]);
        }

        return $description;
    }
}
