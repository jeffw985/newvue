<!--suppress ALL -->

<script setup lang="ts">
import type { EventDropArg, EventClickArg, CalendarOptions } from '@fullcalendar/core'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import timeGridPlugin from '@fullcalendar/timegrid'
import FullCalendar from '@fullcalendar/vue3'
import { Head, router } from '@inertiajs/vue3'
import axios from 'axios'
import { ref, computed } from 'vue'

import { Badge } from '@/components/ui/badge'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItem } from '@/types'

interface ServiceScheduleEvent {
    id: number
    title: string
    start: string
    end?: string
    customer_name: string
    customer_address: string | null
    service_requested: string[] | null
    service_status: string | null
    crew_assigned: string | null
    crew_status: string | null
    service_notes: string | null
    crew_comments: string | null
    cust_id: number
}

const props = defineProps<{
    schedules: ServiceScheduleEvent[]
}>()

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Service Calendar',
        href: '/service-calendar',
    },
]

const isDetailsModalOpen = ref(false)
const selectedEvent = ref<ServiceScheduleEvent | null>(null)

// Format events for FullCalendar
const calendarEvents = computed(() => {
    return props.schedules.map((schedule) => ({
        id: String(schedule.id),
        title: schedule.customer_name,
        start: schedule.start,
        end: schedule.end,
        extendedProps: schedule,
        backgroundColor: getEventColor(schedule.service_status),
        borderColor: getEventColor(schedule.service_status),
    }))
})

const getEventColor = (status: string | null): string => {
    switch (status?.toLowerCase()) {
        case 'completed':
            return '#22c55e'
        case 'in progress':
            return '#3b82f6'
        case 'scheduled':
            return '#f59e0b'
        case 'cancelled':
            return '#ef4444'
        default:
            return '#6b7280'
    }
}

const calendarOptions = ref<CalendarOptions>({
    plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
    initialView: 'dayGridMonth',
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay',
    },
    editable: true,
    droppable: true,
    eventDrop: handleEventDrop,
    eventClick: handleEventClick,
    height: 'auto',
    events: calendarEvents.value,
})

async function handleEventDrop(info: EventDropArg) {
    const event = info.event
    const scheduleId = event.id

    try {
        await axios.put(`/service-calendar/${scheduleId}`, {
            start_time: event.start?.toISOString(),
            end_time: event.end?.toISOString(),
        })

        // Reload the page to get fresh data with updated times
        router.reload({ only: ['schedules'] })
    } catch (error) {
        console.error('Failed to update event:', error)
        // Revert the event on error
        info.revert()
    }
}

function handleEventClick(info: EventClickArg) {
    const eventProps = info.event.extendedProps as ServiceScheduleEvent

    // Use the current event times from the calendar (which may have been updated by drag-drop)
    selectedEvent.value = {
        id: Number(info.event.id),
        title: info.event.title,
        start: info.event.start?.toISOString() || info.event.startStr,
        end: info.event.end?.toISOString() || info.event.endStr,
        customer_name: eventProps.customer_name,
        customer_address: eventProps.customer_address,
        service_requested: eventProps.service_requested,
        service_status: eventProps.service_status,
        crew_assigned: eventProps.crew_assigned,
        crew_status: eventProps.crew_status,
        service_notes: eventProps.service_notes,
        crew_comments: eventProps.crew_comments,
        cust_id: eventProps.cust_id,
    }
    isDetailsModalOpen.value = true
}

const formatDateTime = (dateString: string | undefined) => {
    if (!dateString) return 'N/A'
    const date = new Date(dateString)
    return date.toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
        hour12: true,
    })
}

const displayValue = (value: string | null | undefined): string => {
    if (value === null || value === undefined || value === '') return 'N/A'
    return value
}
</script>

<template>
    <Head title="Service Calendar" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <Card>
                <CardHeader>
                    <CardTitle>Service Calendar</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="calendar-wrapper">
                        <FullCalendar :options="calendarOptions" />
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Event Details Modal -->
        <Dialog v-model:open="isDetailsModalOpen">
            <DialogContent class="max-w-2xl max-h-[90vh] overflow-y-auto">
                <DialogHeader>
                    <DialogTitle>Service Schedule Details</DialogTitle>
                </DialogHeader>
                <div v-if="selectedEvent" class="space-y-4">
                    <!-- Customer Information -->
                    <div class="space-y-2">
                        <h3 class="font-semibold text-lg">{{ selectedEvent.customer_name }}</h3>
                        <p class="text-sm text-muted-foreground">
                            {{ displayValue(selectedEvent.customer_address) }}
                        </p>
                    </div>

                    <!-- Date & Time -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <p class="text-sm font-medium text-muted-foreground">Start Time</p>
                            <p class="text-base">{{ formatDateTime(selectedEvent.start) }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-sm font-medium text-muted-foreground">End Time</p>
                            <p class="text-base">{{ formatDateTime(selectedEvent.end) }}</p>
                        </div>
                    </div>

                    <!-- Service Information -->
                    <div class="space-y-2">
                        <p class="text-sm font-medium text-muted-foreground">Service Requested</p>
                        <div class="flex flex-wrap gap-2">
                            <template v-if="selectedEvent.service_requested && selectedEvent.service_requested.length > 0">
                                <Badge
                                    v-for="service in selectedEvent.service_requested"
                                    :key="service"
                                    variant="outline"
                                >
                                    {{ service }}
                                </Badge>
                            </template>
                            <span v-else class="text-muted-foreground">N/A</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <p class="text-sm font-medium text-muted-foreground">Service Status</p>
                            <Badge
                                :variant="
                                    selectedEvent.service_status === 'Completed'
                                        ? 'outline-green'
                                        : selectedEvent.service_status === 'In Progress'
                                        ? 'outline-blue'
                                        : 'outline-amber'
                                "
                            >
                                {{ displayValue(selectedEvent.service_status) }}
                            </Badge>
                        </div>
                        <div class="space-y-1">
                            <p class="text-sm font-medium text-muted-foreground">Crew Assigned</p>
                            <p class="text-base">{{ displayValue(selectedEvent.crew_assigned) }}</p>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <p class="text-sm font-medium text-muted-foreground">Crew Status</p>
                        <p class="text-base">{{ displayValue(selectedEvent.crew_status) }}</p>
                    </div>

                    <!-- Notes -->
                    <div class="space-y-2">
                        <p class="text-sm font-medium text-muted-foreground">Service Notes</p>
                        <div class="rounded-md border bg-muted/50 p-3 min-h-[60px] whitespace-pre-wrap">
                            {{ displayValue(selectedEvent.service_notes) }}
                        </div>
                    </div>

                    <div class="space-y-2">
                        <p class="text-sm font-medium text-muted-foreground">Crew Comments</p>
                        <div class="rounded-md border bg-muted/50 p-3 min-h-[60px] whitespace-pre-wrap">
                            {{ displayValue(selectedEvent.crew_comments) }}
                        </div>
                    </div>
                </div>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>

<style scoped>
.calendar-wrapper {
    min-height: 600px;
}

/* stylelint-disable selector-class-pattern */
/* FullCalendar styles - .fc-* classes are generated by FullCalendar library at runtime */

/* Calendar container with rounded corners */
.calendar-wrapper :deep(.fc) {
    border-radius: 0.5rem;
    overflow: hidden;
    font-weight: 400;
}

/* Grid lines - muted color like ledger grouping */
.calendar-wrapper :deep(.fc-theme-standard td),
.calendar-wrapper :deep(.fc-theme-standard th) {
    border: 1px solid hsl(0 0% 92.8%);
}

.calendar-wrapper :deep(.fc-scrollgrid) {
    border-color: hsl(0 0% 92.8%);
}

/* Weekday headers - system grey for visibility */
.calendar-wrapper :deep(.fc-col-header-cell) {
    background-color: hsl(0 0% 45.1%);
    font-weight: 500;
}

.calendar-wrapper :deep(.fc-col-header-cell-cushion) {
    color: hsl(0 0% 100%);
    font-weight: 500;
    padding: 0.5rem;
}

/* Day numbers - reduced font weight */
.calendar-wrapper :deep(.fc-daygrid-day-number) {
    font-weight: 400;
}

/* Events styling - lighter weight and smaller size */
.calendar-wrapper :deep(.fc-event) {
    cursor: pointer;
    transition: opacity 0.2s;
    font-weight: 300;
    font-size: 0.813rem;
    border-radius: 0.25rem;
}

.calendar-wrapper :deep(.fc-event-title),
.calendar-wrapper :deep(.fc-event-time) {
    font-weight: 300;
}

.calendar-wrapper :deep(.fc-event:hover) {
    opacity: 0.8;
}

/* Time grid events (week/day view) */
.calendar-wrapper :deep(.fc-timegrid-event) {
    font-size: 0.813rem;
}

.calendar-wrapper :deep(.fc-timegrid-event-harness) {
    font-weight: 300;
}

/* Toolbar buttons - match grid lines and header colors */
.calendar-wrapper :deep(.fc-button) {
    font-weight: 500;
    border-radius: 0.375rem;
    border-color: hsl(0 0% 92.8%) !important;
    background-color: transparent !important;
    color: hsl(0 0% 3.9%) !important;
}

.calendar-wrapper :deep(.fc-button:hover) {
    background-color: hsl(0 0% 96.1%) !important;
}

.calendar-wrapper :deep(.fc-button-active),
.calendar-wrapper :deep(.fc-button:active) {
    background-color: hsl(0 0% 45.1%) !important;
    color: hsl(0 0% 100%) !important;
    border-color: hsl(0 0% 45.1%) !important;
}

/* Title - reduced font weight */
.calendar-wrapper :deep(.fc-toolbar-title) {
    font-weight: 600;
}

/* Week/Day view time labels */
.calendar-wrapper :deep(.fc-timegrid-axis-cushion),
.calendar-wrapper :deep(.fc-timegrid-slot-label-cushion) {
    font-weight: 400;
}

/* Dark mode support */
.dark .calendar-wrapper :deep(.fc) {
    --fc-border-color: hsl(0 0% 16.08%);
    --fc-today-bg-color: hsl(var(--accent));
}

/* Dark mode - toolbar buttons */
.dark .calendar-wrapper :deep(.fc-button) {
    border-color: hsl(0 0% 16.08%) !important;
    background-color: transparent !important;
    color: hsl(0 0% 98%) !important;
}

.dark .calendar-wrapper :deep(.fc-button:hover) {
    background-color: hsl(0 0% 14.9%) !important;
}

.dark .calendar-wrapper :deep(.fc-button-active),
.dark .calendar-wrapper :deep(.fc-button:active) {
    background-color: hsl(0 0% 25%) !important;
    color: hsl(0 0% 90%) !important;
    border-color: hsl(0 0% 25%) !important;
}

/* Dark mode - grid lines */
.dark .calendar-wrapper :deep(.fc-theme-standard td),
.dark .calendar-wrapper :deep(.fc-theme-standard th) {
    border-color: hsl(0 0% 16.08%);
}

.dark .calendar-wrapper :deep(.fc-scrollgrid) {
    border-color: hsl(0 0% 16.08%);
}

/* Dark mode - weekday headers */
.dark .calendar-wrapper :deep(.fc-col-header-cell) {
    background-color: hsl(0 0% 25%);
}

.dark .calendar-wrapper :deep(.fc-col-header-cell-cushion) {
    color: hsl(0 0% 90%);
}

.dark .calendar-wrapper :deep(.fc .fc-daygrid-day-number) {
    color: hsl(var(--foreground));
}
</style>
