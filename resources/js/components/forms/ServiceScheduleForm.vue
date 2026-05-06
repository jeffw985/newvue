<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { X } from 'lucide-vue-next'

import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Badge } from '@/components/ui/badge'
import type { ServiceSchedule } from '@/types/models'

type FormMode = 'create' | 'edit'

const props = withDefaults(
    defineProps<{
        schedule?: Partial<ServiceSchedule>
        mode?: FormMode
        submitUrl?: string
        hideSubmitButton?: boolean
        onCancel?: () => void
    }>(),
    {
        mode: 'edit',
        hideSubmitButton: false,
    }
)

const emit = defineEmits<{
    success: [schedule: ServiceSchedule]
    error: [errors: any]
}>()

const formatDateTimeForInput = (dateString: string | null | undefined): string | undefined => {
    if (!dateString) return undefined
    const date = new Date(dateString)
    const year = date.getFullYear()
    const month = String(date.getMonth() + 1).padStart(2, '0')
    const day = String(date.getDate()).padStart(2, '0')
    const hours = String(date.getHours()).padStart(2, '0')
    const minutes = String(date.getMinutes()).padStart(2, '0')
    return `${year}-${month}-${day}T${hours}:${minutes}`
}

const formatDateTime = (dateString: string | null | undefined): string => {
    if (!dateString) return 'N/A'
    return new Date(dateString).toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
        hour12: true,
    })
}

const displayValue = (value: any): string => {
    if (value === null || value === undefined || value === '') return 'N/A'
    if (typeof value === 'boolean') return value ? 'Yes' : 'No'
    return value.toString()
}

const form = useForm({
    cust_id: props.schedule?.cust_id ?? undefined,
    start_time: formatDateTimeForInput(props.schedule?.start_time),
    end_time: formatDateTimeForInput(props.schedule?.end_time),
    service_requested: props.schedule?.service_requested ?? [],
    service_status: props.schedule?.service_status ?? '',
    crew_assigned: props.schedule?.crew_assigned ?? '',
    crew_status: props.schedule?.crew_status ?? '',
    service_notes: props.schedule?.service_notes ?? '',
    crew_comments: props.schedule?.crew_comments ?? '',
    site_address: props.schedule?.site_address ?? '',
})

const submit = () => {
    const url = props.submitUrl ?? (
        props.mode === 'create'
            ? '/service-schedules'
            : `/service-schedules/${props.schedule?.id}`
    )

    const method = props.mode === 'create' ? 'post' : 'put'

    form[method](url, {
        preserveScroll: true,
        headers: {
            'X-Timezone': Intl.DateTimeFormat().resolvedOptions().timeZone,
        },
        onSuccess: (page) => {
            const updatedSchedule = (page.props.flash as any)?.schedule as ServiceSchedule | undefined
            emit('success', updatedSchedule || props.schedule as ServiceSchedule)
        },
        onError: (errors) => {
            console.error('Service schedule form validation errors:', errors)
            emit('error', errors)
        },
    })
}
</script>

<template>
    <form @submit.prevent="submit" class="relative space-y-6">
        <div v-if="!hideSubmitButton || onCancel" class="flex justify-end items-center gap-4">
            <Button v-if="!hideSubmitButton" type="submit" size="sm" :disabled="form.processing">
                {{ form.processing ? 'Saving...' : 'Save Changes' }}
            </Button>
            <button
                v-if="onCancel"
                type="button"
                @click="onCancel"
                class="p-1 text-muted-foreground hover:text-foreground transition-colors"
                title="Cancel"
            >
                <X class="h-5 w-5" />
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Customer Information -->
            <div class="space-y-4">
                <h3 class="text-sm font-semibold border-b pb-1 uppercase tracking-wider text-muted-foreground">Customer Information</h3>

                <div class="space-y-2">
                    <Label class="text-xs text-muted-foreground uppercase">Customer Name</Label>
                    <p class="text-sm font-medium">{{ schedule?.customer?.full_name || 'N/A' }}</p>
                </div>

                <div class="space-y-2">
                    <Label for="site_address">Site Address</Label>
                    <Input id="site_address" v-model="form.site_address" />
                    <InputError :message="form.errors.site_address" />
                </div>

                <div v-if="schedule?.customer?.areaGroup" class="space-y-2">
                    <Label class="text-xs text-muted-foreground uppercase">Area Group</Label>
                    <Badge>{{ schedule.customer.areaGroup.area_name }}</Badge>
                </div>

                <div v-if="schedule?.maintenance?.service_interval" class="space-y-2">
                    <Label class="text-xs text-muted-foreground uppercase">Service Interval</Label>
                    <Badge>{{ schedule.maintenance.service_interval }}</Badge>
                </div>
            </div>

            <!-- Schedule Times -->
            <div class="space-y-4">
                <h3 class="text-sm font-semibold border-b pb-1 uppercase tracking-wider text-muted-foreground">Schedule Times</h3>

                <div class="space-y-2">
                    <Label for="start_time">Start Time</Label>
                    <Input
                        id="start_time"
                        type="datetime-local"
                        v-model="form.start_time"
                        class="[&::-webkit-calendar-picker-indicator]:cursor-pointer [&::-webkit-calendar-picker-indicator]:opacity-100"
                    />
                    <InputError :message="form.errors.start_time" />
                </div>

                <div class="space-y-2">
                    <Label for="end_time">End Time</Label>
                    <Input
                        id="end_time"
                        type="datetime-local"
                        v-model="form.end_time"
                        class="[&::-webkit-calendar-picker-indicator]:cursor-pointer [&::-webkit-calendar-picker-indicator]:opacity-100"
                    />
                    <InputError :message="form.errors.end_time" />
                </div>
            </div>
        </div>

        <!-- Service Details -->
        <div class="space-y-4">
            <h3 class="text-sm font-semibold border-b pb-1 uppercase tracking-wider text-muted-foreground">Service Details</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <Label class="text-xs text-muted-foreground uppercase">Services Requested</Label>
                    <div v-if="schedule?.service_requested && schedule.service_requested.length > 0" class="flex flex-wrap gap-2">
                        <Badge
                            v-for="service in schedule.service_requested"
                            :key="service"
                            variant="outline"
                        >
                            {{ service }}
                        </Badge>
                    </div>
                    <p v-else class="text-sm text-muted-foreground">N/A</p>
                </div>

                <div class="space-y-2">
                    <Label for="service_status">Service Status</Label>
                    <Input id="service_status" v-model="form.service_status" />
                    <InputError :message="form.errors.service_status" />
                </div>
            </div>

            <div class="space-y-2">
                <Label for="service_notes">Service Notes</Label>
                <textarea
                    id="service_notes"
                    v-model="form.service_notes"
                    rows="3"
                    class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                />
                <InputError :message="form.errors.service_notes" />
            </div>
        </div>

        <!-- Crew Information -->
        <div class="space-y-4">
            <h3 class="text-sm font-semibold border-b pb-1 uppercase tracking-wider text-muted-foreground">Crew Information</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <Label for="crew_assigned">Crew Assigned</Label>
                    <Input id="crew_assigned" v-model="form.crew_assigned" />
                    <InputError :message="form.errors.crew_assigned" />
                </div>

                <div class="space-y-2">
                    <Label for="crew_status">Crew Status</Label>
                    <Input id="crew_status" v-model="form.crew_status" />
                    <InputError :message="form.errors.crew_status" />
                </div>
            </div>

            <div class="space-y-2">
                <Label for="crew_comments">Crew Comments</Label>
                <textarea
                    id="crew_comments"
                    v-model="form.crew_comments"
                    rows="3"
                    class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                />
                <InputError :message="form.errors.crew_comments" />
            </div>
        </div>
    </form>
</template>
