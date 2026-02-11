<?php

namespace App\Observers;

use App\Models\ServiceSchedule;
use Carbon\Carbon;
use Spatie\GoogleCalendar\Event;

class ServiceScheduleObserver
{
    /**
     * Handle the ServiceSchedule "created" event.
     */
    public function created(ServiceSchedule $serviceSchedule): void
    {
        try {
            $event = new Event;
            $event->name = $this->getEventName($serviceSchedule);
            $event->description = $this->getEventDescription($serviceSchedule);
            $event->startDateTime = $serviceSchedule->start_time ?? Carbon::now();
            $event->endDateTime = $serviceSchedule->end_time ?? ($serviceSchedule->start_time ? $serviceSchedule->start_time->addHour() : Carbon::now()->addHour());
            $event->location = $serviceSchedule->site_address ?? '';

            $googleEvent = $event->save();

            $serviceSchedule->withoutEvents(function () use ($serviceSchedule, $googleEvent) {
                $serviceSchedule->update([
                    'google_event_id' => $googleEvent->id,
                    'last_synced_at' => now(),
                ]);
            });
        } catch (\Exception $e) {
            \Log::error('Google Calendar Sync Error (created): '.$e->getMessage());
        }
    }

    /**
     * Handle the ServiceSchedule "updated" event.
     */
    public function updated(ServiceSchedule $serviceSchedule): void
    {
        if (! $serviceSchedule->google_event_id) {
            $this->created($serviceSchedule);

            return;
        }

        try {
            $event = Event::find($serviceSchedule->google_event_id);

            if (! $event) {
                $this->created($serviceSchedule);

                return;
            }

            $event->name = $this->getEventName($serviceSchedule);
            $event->description = $this->getEventDescription($serviceSchedule);
            $event->startDateTime = $serviceSchedule->start_time ?? Carbon::now();
            $event->endDateTime = $serviceSchedule->end_time ?? ($serviceSchedule->start_time ? $serviceSchedule->start_time->addHour() : Carbon::now()->addHour());
            $event->location = $serviceSchedule->site_address ?? '';

            $event->save();

            $serviceSchedule->withoutEvents(function () use ($serviceSchedule) {
                $serviceSchedule->update(['last_synced_at' => now()]);
            });
        } catch (\Exception $e) {
            \Log::error('Google Calendar Sync Error (updated): '.$e->getMessage());
        }
    }

    /**
     * Handle the ServiceSchedule "deleted" event.
     */
    public function deleted(ServiceSchedule $serviceSchedule): void
    {
        if ($serviceSchedule->google_event_id) {
            try {
                $event = Event::find($serviceSchedule->google_event_id);
                if ($event) {
                    $event->delete();
                }
            } catch (\Exception $e) {
                \Log::error('Google Calendar Sync Error (deleted): '.$e->getMessage());
            }
        }
    }

    /**
     * Get the event name for Google Calendar.
     */
    protected function getEventName(ServiceSchedule $serviceSchedule): string
    {
        $customerName = $serviceSchedule->customer?->full_name ?? 'Customer';
        $siteAddress = $serviceSchedule->site_address ?? $serviceSchedule->customer?->street ?? '';

        return trim("Service: {$customerName} - {$siteAddress}");
    }

    /**
     * Get the event description for Google Calendar.
     */
    protected function getEventDescription(ServiceSchedule $serviceSchedule): string
    {
        $description = 'Service Notes: '.($serviceSchedule->service_notes ?? 'None')."\n";
        $description .= 'Crew assigned: '.($serviceSchedule->crew_assigned ?? 'None')."\n";
        $description .= 'Status: '.($serviceSchedule->service_status ?? 'Pending')."\n";

        if ($serviceSchedule->service_requested) {
            $description .= 'Services: '.implode(', ', (array) $serviceSchedule->service_requested);
        }

        return $description;
    }
}
