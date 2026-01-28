<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { watch } from 'vue'

import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import type { ServiceSchedule } from '@/types/models'


type FormMode = 'create' | 'edit'

const props = withDefaults(
    defineProps<{
        schedule?: Partial<ServiceSchedule>
        mode?: FormMode
        submitUrl?: string
        hideSubmitButton?: boolean
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

const serviceOptions = [
    'Maintenance',
    'Mulch',
    'Irrigation Blowout',
    'Backflow Testing',
    'Spring Start-Up',
    'Fall Cleanup',
]

const form = useForm({
    cust_id: props.schedule?.cust_id ?? undefined,
    start_time: formatDateTimeForInput(props.schedule?.start_time),
    end_time: formatDateTimeForInput(props.schedule?.end_time),
    service_status: props.schedule?.service_status ?? 'Pending',
    site_address: props.schedule?.site_address ?? '',
    crew_assigned: props.schedule?.crew_assigned ?? '',
    crew_status: props.schedule?.crew_status ?? '',
    crew_comments: props.schedule?.crew_comments ?? '',
    service_notes: props.schedule?.service_notes ?? '',
    service_requested: props.schedule?.service_requested ?? [],
})

const toggleService = (service: string) => {
    const index = form.service_requested.indexOf(service)
    if (index === -1) {
        form.service_requested.push(service)
    } else {
        form.service_requested.splice(index, 1)
    }
}

// Watch for changes to start_time and automatically set end_time to start_time + 1 hour
watch(() => form.start_time, (newStartTime) => {
    if (newStartTime) {
        const startDate = new Date(newStartTime)
        startDate.setHours(startDate.getHours() + 1)
        const year = startDate.getFullYear()
        const month = String(startDate.getMonth() + 1).padStart(2, '0')
        const day = String(startDate.getDate()).padStart(2, '0')
        const hours = String(startDate.getHours()).padStart(2, '0')
        const minutes = String(startDate.getMinutes()).padStart(2, '0')
        form.end_time = `${year}-${month}-${day}T${hours}:${minutes}`
    }
})

const submit = () => {
    const url = props.submitUrl ?? (
        props.mode === 'create'
            ? '/schedules'
            : `/schedules/${props.schedule?.id}`
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
            emit('error', errors)
        },
    })
}
</script>

<template>
    <form @submit.prevent="submit" class="space-y-6">
        <div v-if="!hideSubmitButton" class="flex justify-end">
            <Button type="submit" size="sm" :disabled="form.processing">
                {{ form.processing ? 'Saving...' : 'Save Changes' }}
            </Button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-4">
                <h3 class="text-sm font-semibold border-b pb-1 uppercase tracking-wider text-muted-foreground">Schedule Info</h3>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="start_time">Start Time</Label>
                        <Input type="datetime-local" id="start_time" v-model="form.start_time" class="[&::-webkit-calendar-picker-indicator]:cursor-pointer [&::-webkit-calendar-picker-indicator]:opacity-100 [&::-webkit-calendar-picker-indicator]:filter [&::-webkit-calendar-picker-indicator]:invert [&::-webkit-calendar-picker-indicator]:sepia [&::-webkit-calendar-picker-indicator]:saturate-[500%] [&::-webkit-calendar-picker-indicator]:hue-rotate-[180deg] [&::-webkit-calendar-picker-indicator]:brightness-[0.8]" />
                        <InputError :message="form.errors.start_time" />
                    </div>
                    <div class="space-y-2">
                        <Label for="end_time">End Time</Label>
                        <Input type="datetime-local" id="end_time" v-model="form.end_time" class="[&::-webkit-calendar-picker-indicator]:cursor-pointer [&::-webkit-calendar-picker-indicator]:opacity-100 [&::-webkit-calendar-picker-indicator]:filter [&::-webkit-calendar-picker-indicator]:invert [&::-webkit-calendar-picker-indicator]:sepia [&::-webkit-calendar-picker-indicator]:saturate-[500%] [&::-webkit-calendar-picker-indicator]:hue-rotate-[180deg] [&::-webkit-calendar-picker-indicator]:brightness-[0.8]" />
                        <InputError :message="form.errors.end_time" />
                    </div>
                </div>

                <div class="space-y-2">
                    <Label for="service_status">Service Status</Label>
                    <select
                        id="service_status"
                        v-model="form.service_status"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <option value="Scheduled">Scheduled</option>
                        <option value="Completed">Completed</option>
                    </select>
                    <InputError :message="form.errors.service_status" />
                </div>

                <div class="space-y-2">
                    <Label for="site_address">Site Address</Label>
                    <Input id="site_address" v-model="form.site_address" />
                    <InputError :message="form.errors.site_address" />
                </div>
            </div>

            <div class="space-y-4">
                <h3 class="text-sm font-semibold border-b pb-1 uppercase tracking-wider text-muted-foreground">Crew Assignment</h3>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="crew_assigned">Assigned Crew</Label>
                        <Input id="crew_assigned" v-model="form.crew_assigned" />
                    </div>
                    <div class="space-y-2">
                        <Label for="crew_status">Crew Status</Label>
                        <select
                            id="crew_status"
                            v-model="form.crew_status"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <option value="Pending">Pending</option>
                            <option value="Completed">Completed</option>
                            <option value="See Comments">See Comments</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <Label for="crew_comments">Crew Comments</Label>
                    <textarea
                        id="crew_comments"
                        v-model="form.crew_comments"
                        rows="2"
                        class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    />
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <h3 class="text-sm font-semibold border-b pb-1 uppercase tracking-wider text-muted-foreground">Service Details</h3>

            <div class="space-y-2">
                <Label>Services Requested</Label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 border rounded-md p-4 bg-muted/20">
                    <div v-for="option in serviceOptions" :key="option" class="flex items-center space-x-2">
                        <input
                            type="checkbox"
                            :id="`service-${option}`"
                            :checked="form.service_requested.includes(option)"
                            @change="toggleService(option)"
                            class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary"
                        />
                        <Label :for="`service-${option}`" class="text-sm font-normal cursor-pointer">{{ option }}</Label>
                    </div>
                </div>
                <InputError :message="form.errors.service_requested" />
            </div>

            <div class="space-y-2">
                <Label for="service_notes">Service Notes</Label>
                <textarea
                    id="service_notes"
                    v-model="form.service_notes"
                    rows="3"
                    class="flex min-h-[100px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                />
                <InputError :message="form.errors.service_notes" />
            </div>
        </div>
    </form>
</template>
