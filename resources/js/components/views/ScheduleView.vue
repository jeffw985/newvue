<script setup lang="ts">
import { Badge } from '@/components/ui/badge'
import { Label } from '@/components/ui/label'
import type { ServiceSchedule } from '@/types/models'

defineProps<{
    schedule: ServiceSchedule
}>()

const formatDateTime = (dateString: string | null | undefined): string => {
    if (!dateString) return 'N/A'
    return new Date(dateString).toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    })
}

const displayValue = (value: any): string => {
    if (value === null || value === undefined || value === '') return 'N/A'
    return value.toString()
}
</script>

<template>
    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <h3 class="text-sm font-semibold border-b pb-1 uppercase tracking-wider text-muted-foreground">Schedule Info</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <Label class="text-xs text-muted-foreground uppercase">Start Time</Label>
                        <p class="text-sm">{{ formatDateTime(schedule.start_time) }}</p>
                    </div>
                    <div class="space-y-1">
                        <Label class="text-xs text-muted-foreground uppercase">End Time</Label>
                        <p class="text-sm">{{ formatDateTime(schedule.end_time) }}</p>
                    </div>
                    <div class="space-y-1">
                        <Label class="text-xs text-muted-foreground uppercase">Status</Label>
                        <p class="text-sm">
                            <Badge variant="outline">{{ displayValue(schedule.service_status) }}</Badge>
                        </p>
                    </div>
                    <div class="space-y-1">
                        <Label class="text-xs text-muted-foreground uppercase">Site Address</Label>
                        <p class="text-sm">{{ displayValue(schedule.site_address) }}</p>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <h3 class="text-sm font-semibold border-b pb-1 uppercase tracking-wider text-muted-foreground">Crew Assignment</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <Label class="text-xs text-muted-foreground uppercase">Assigned Crew</Label>
                        <p class="text-sm">{{ displayValue(schedule.crew_assigned) }}</p>
                    </div>
                    <div class="space-y-1">
                        <Label class="text-xs text-muted-foreground uppercase">Crew Status</Label>
                        <p class="text-sm">{{ displayValue(schedule.crew_status) }}</p>
                    </div>
                </div>
                <div class="space-y-1">
                    <Label class="text-xs text-muted-foreground uppercase">Crew Comments</Label>
                    <p class="text-sm whitespace-pre-wrap rounded-md border bg-muted/50 px-3 py-1 min-h-[40px]">
                        {{ displayValue(schedule.crew_comments) }}
                    </p>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <h3 class="text-sm font-semibold border-b pb-1 uppercase tracking-wider text-muted-foreground">Service Details</h3>
            <div class="space-y-2">
                <Label class="text-xs text-muted-foreground uppercase">Services Requested</Label>
                <div class="flex flex-wrap gap-1.5">
                    <template v-if="schedule.service_requested && schedule.service_requested.length">
                        <Badge v-for="service in schedule.service_requested" :key="service" variant="secondary" class="px-2 py-0.5">
                            {{ service }}
                        </Badge>
                    </template>
                    <span v-else class="text-sm text-muted-foreground">None</span>
                </div>
            </div>
            <div class="space-y-2">
                <Label class="text-xs text-muted-foreground uppercase">Service Notes</Label>
                <p class="text-sm whitespace-pre-wrap rounded-md border bg-muted/50 px-3 py-2 min-h-[60px]">
                    {{ displayValue(schedule.service_notes) }}
                </p>
            </div>
        </div>
    </div>
</template>
