<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import { Trash2 } from 'lucide-vue-next'
import { computed, ref } from 'vue'

import { Badge } from '@/components/ui/badge'
import type { Customer } from '@/types/models'

const props = defineProps<{
    customer: Customer
    currentTab?: 'customer' | 'ledgers' | 'irrigations' | 'maintenances' | 'schedules'
}>()

const emit = defineEmits<{
    tabChange: [tab: 'customer' | 'ledgers' | 'irrigations' | 'maintenances' | 'schedules']
    recordSelected: [type: string, record: any]
}>()

const activeTab = ref<'customer' | 'ledgers' | 'irrigations' | 'maintenances' | 'schedules'>(props.currentTab || 'customer')

const sortedSchedules = computed(() => {
    if (!props.customer.service_schedules) return []
    return [...props.customer.service_schedules].sort((a, b) => {
        const dateA = a.start_time ? new Date(a.start_time).getTime() : 0
        const dateB = b.start_time ? new Date(b.start_time).getTime() : 0
        return dateB - dateA
    })
})

const formatDate = (dateString: string) => {
    // Parse date string manually to avoid timezone issues with date-only strings
    const dateOnly = dateString.split('T')[0].split(' ')[0]
    const [year, month, day] = dateOnly.split('-')
    const date = new Date(parseInt(year), parseInt(month) - 1, parseInt(day))
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    })
}

const selectTab = (tab: 'customer' | 'ledgers' | 'irrigations' | 'maintenances' | 'schedules') => {
    activeTab.value = tab
    emit('tabChange', tab)
}

const deleteRecord = (type: string, id: number) => {
    const endpoint = type === 'schedule' ? 'schedules' : `${type}s`

    if (confirm(`Are you sure you want to delete this ${type} record?`)) {
        router.delete(`/${endpoint}/${id}`, {
            preserveScroll: true,
        })
    }
}
</script>

<template>
    <div class="space-y-4">
        <!-- Tab Buttons as Badges -->
        <div class="flex flex-wrap gap-1.5">
            <Badge
                as="button"
                @click="selectTab('customer')"
                :variant="activeTab === 'customer' ? 'default' : 'outline-purple'"
                :class="[
                    'cursor-pointer transition-all px-2 py-0.5 text-[11px]',
                    activeTab === 'customer'
                        ? 'ring-2 ring-ring/20'
                        : 'hover:bg-purple-50 dark:hover:bg-purple-950'
                ]"
            >
                Customer
            </Badge>

            <Badge
                as="button"
                @click="selectTab('ledgers')"
                :variant="activeTab === 'ledgers' ? 'default' : 'outline-gray'"
                :class="[
                    'cursor-pointer transition-all px-2 py-0.5 text-[11px]',
                    activeTab === 'ledgers'
                        ? 'ring-2 ring-ring/20'
                        : 'hover:bg-gray-50 dark:hover:bg-gray-950'
                ]"
            >
                Ledger
                <Badge
                    v-if="customer.ledgers_count"
                    variant="secondary"
                    class="ml-1 text-[10px] px-1 py-0"
                >
                    {{ customer.ledgers_count }}
                </Badge>
            </Badge>

            <Badge
                as="button"
                @click="selectTab('irrigations')"
                :variant="activeTab === 'irrigations' ? 'default' : 'outline-blue'"
                :class="[
                    'cursor-pointer transition-all px-2 py-0.5 text-[11px]',
                    activeTab === 'irrigations'
                        ? 'ring-2 ring-ring/20'
                        : 'hover:bg-blue-50 dark:hover:bg-blue-950'
                ]"
            >
                Irrigation
                <Badge
                    v-if="customer.irrigations_count"
                    variant="secondary"
                    class="ml-1 text-[10px] px-1 py-0"
                >
                    {{ customer.irrigations_count }}
                </Badge>
            </Badge>

            <Badge
                as="button"
                @click="selectTab('maintenances')"
                :variant="activeTab === 'maintenances' ? 'default' : 'outline-green'"
                :class="[
                    'cursor-pointer transition-all px-2 py-0.5 text-[11px]',
                    activeTab === 'maintenances'
                        ? 'ring-2 ring-ring/20'
                        : 'hover:bg-green-50 dark:hover:bg-green-950'
                ]"
            >
                Maintenance
                <Badge
                    v-if="customer.maintenances_count"
                    variant="secondary"
                    class="ml-1 text-[10px] px-1 py-0"
                >
                    {{ customer.maintenances_count }}
                </Badge>
            </Badge>

            <Badge
                as="button"
                @click="selectTab('schedules')"
                :variant="activeTab === 'schedules' ? 'default' : 'outline-amber'"
                :class="[
                    'cursor-pointer transition-all px-2 py-0.5 text-[11px]',
                    activeTab === 'schedules'
                        ? 'ring-2 ring-ring/20'
                        : 'hover:bg-amber-50 dark:hover:bg-amber-950'
                ]"
            >
                Schedules
                <Badge
                    v-if="customer.service_schedules_count"
                    variant="secondary"
                    class="ml-1 text-[10px] px-1 py-0"
                >
                    {{ customer.service_schedules_count }}
                </Badge>
            </Badge>
        </div>

        <!-- Tab Content -->
        <div v-if="activeTab === 'customer'">
            <div class="rounded-md border p-3">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-sm">{{ customer.full_name || 'No name' }}</span>
                        <span class="text-[10px] text-muted-foreground">ID: {{ customer.id }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="activeTab === 'ledgers'">
            <div class="space-y-2">
                <div
                    v-if="!customer.ledgers?.length"
                    class="rounded-md border bg-muted/20 p-6 text-center text-sm text-muted-foreground"
                >
                    No ledger entries found.
                </div>
                <div
                    v-for="ledger in customer.ledgers"
                    :key="ledger.id"
                    class="rounded-md border p-3 hover:bg-muted/50 cursor-pointer transition-colors"
                    @click="emit('recordSelected', 'ledger', { ...ledger, customer })"
                >
                    <div class="space-y-1">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-sm">{{ formatDate(ledger.work_date) }}</span>
                                <Badge
                                    :variant="ledger.work_type === 'Irrigation' ? 'outline-blue' : ledger.work_type === 'Maintenance' ? 'outline-green' : 'outline-gray'"
                                    class="text-[10px] px-1 py-0"
                                >
                                    {{ ledger.work_type }}
                                </Badge>
                                <Badge
                                    :variant="ledger.billed ? 'outline-green' : 'outline-red'"
                                    class="text-[10px] px-1 py-0"
                                >
                                    {{ ledger.billed ? 'Billed' : 'Unbilled' }}
                                </Badge>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] text-muted-foreground">ID: {{ ledger.id }}</span>
                                <button
                                    @click.stop="deleteRecord('ledger', ledger.id)"
                                    class="text-muted-foreground hover:text-destructive transition-colors"
                                >
                                    <Trash2 class="h-3 w-3" />
                                </button>
                            </div>
                        </div>
                        <div class="text-xs text-muted-foreground line-clamp-2">
                            <p v-if="ledger.work_performed">{{ ledger.work_performed }}</p>
                            <p v-if="ledger.job_notes">{{ ledger.job_notes }}</p>
                        </div>
                        <div class="flex gap-3 mt-1 text-[10px] text-muted-foreground">
                            <span v-if="ledger.hours">Hrs: {{ ledger.hours }}</span>
                            <span v-if="ledger.sheet_number">
                                Sheet:
                                <a
                                    v-if="ledger.sheet?.sheet_link"
                                    :href="ledger.sheet.sheet_link"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="text-blue-600 hover:underline dark:text-blue-400"
                                    @click.stop
                                >
                                    {{ ledger.sheet_number }}
                                </a>
                                <span v-else>{{ ledger.sheet_number }}</span>
                            </span>
                            <span v-if="ledger.job_code">Code: {{ ledger.job_code }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="activeTab === 'irrigations'">
            <div class="space-y-2">
                <div
                    v-if="!customer.irrigations?.length"
                    class="rounded-md border bg-muted/20 p-6 text-center text-sm text-muted-foreground"
                >
                    No irrigation records found.
                </div>
                <div
                    v-for="irrigation in customer.irrigations"
                    :key="irrigation.id"
                    class="rounded-md border p-3 hover:bg-muted/50 cursor-pointer transition-colors"
                    @click="emit('recordSelected', 'irrigation', { ...irrigation, customer })"
                >
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-sm">{{ irrigation.site_address || 'No address' }}</span>
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] text-muted-foreground">ID: {{ irrigation.id }}</span>
                                <button
                                    @click.stop="deleteRecord('irrigation', irrigation.id)"
                                    class="text-muted-foreground hover:text-destructive transition-colors"
                                >
                                    <Trash2 class="h-3 w-3" />
                                </button>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <div v-if="irrigation.turn_on" class="flex items-center gap-1">
                                <Badge variant="outline-blue" class="text-[10px] px-1 py-0">Turn On</Badge>
                                <span v-if="irrigation.turn_on_date" class="text-[10px] text-muted-foreground">
                                    {{ formatDate(irrigation.turn_on_date) }}
                                </span>
                            </div>

                            <div v-if="irrigation.blowout" class="flex items-center gap-1">
                                <Badge variant="outline-amber" class="text-[10px] px-1 py-0">Blowout</Badge>
                                <span v-if="irrigation.blowout_date" class="text-[10px] text-muted-foreground">
                                    {{ formatDate(irrigation.blowout_date) }}
                                </span>
                            </div>

                            <div v-if="irrigation.backflow_testing" class="flex items-center gap-1">
                                <Badge variant="outline-green" class="text-[10px] px-1 py-0">Backflow</Badge>
                                <span v-if="irrigation.backflow_test_date" class="text-[10px] text-muted-foreground">
                                    {{ formatDate(irrigation.backflow_test_date) }}
                                </span>
                            </div>
                        </div>

                        <div v-if="irrigation.irrig_notes" class="text-xs text-muted-foreground line-clamp-2">
                            {{ irrigation.irrig_notes }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="activeTab === 'maintenances'">
            <div class="space-y-2">
                <div
                    v-if="!customer.maintenances?.length"
                    class="rounded-md border bg-muted/20 p-6 text-center text-sm text-muted-foreground"
                >
                    No maintenance records found.
                </div>
                <div
                    v-for="maintenance in customer.maintenances"
                    :key="maintenance.id"
                    class="rounded-md border p-3 hover:bg-muted/50 cursor-pointer transition-colors"
                    @click="emit('recordSelected', 'maintenance', { ...maintenance, customer })"
                >
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-sm">{{ maintenance.site_address || 'No address' }}</span>
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] text-muted-foreground">ID: {{ maintenance.id }}</span>
                                <button
                                    @click.stop="deleteRecord('maintenance', maintenance.id)"
                                    class="text-muted-foreground hover:text-destructive transition-colors"
                                >
                                    <Trash2 class="h-3 w-3" />
                                </button>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <Badge v-if="maintenance.service_interval" variant="outline-green" class="text-[10px] px-1 py-0">
                                {{ maintenance.service_interval }}
                            </Badge>
                            <Badge v-if="maintenance.annual_pay" variant="outline-purple" class="text-[10px] px-1 py-0">
                                Annual Pay
                            </Badge>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div v-if="activeTab === 'schedules'">
            <div class="space-y-2">
                <div
                    v-if="!sortedSchedules.length"
                    class="rounded-md border bg-muted/20 p-6 text-center text-sm text-muted-foreground"
                >
                    No service schedules found.
                </div>
                <div
                    v-for="schedule in sortedSchedules"
                    :key="schedule.id"
                    class="rounded-md border p-3 hover:bg-muted/50 cursor-pointer transition-colors"
                    @click="emit('recordSelected', 'schedule', { ...schedule, customer })"
                >
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-sm">{{ schedule.start_time ? formatDate(schedule.start_time) : 'No date' }}</span>
                                <span class="text-xs text-muted-foreground">{{ schedule.site_address }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] text-muted-foreground">ID: {{ schedule.id }}</span>
                                <button
                                    @click.stop="deleteRecord('schedule', schedule.id)"
                                    class="text-muted-foreground hover:text-destructive transition-colors"
                                >
                                    <Trash2 class="h-3 w-3" />
                                </button>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <template v-if="Array.isArray(schedule.service_requested)">
                                <Badge
                                    v-for="service in schedule.service_requested"
                                    :key="service"
                                    variant="outline-blue"
                                    class="text-[10px] px-1 py-0"
                                >
                                    {{ service }}
                                </Badge>
                            </template>
                            <Badge v-if="schedule.service_status" variant="outline-amber" class="text-[10px] px-1 py-0">
                                {{ schedule.service_status }}
                            </Badge>
                        </div>

                        <div v-if="schedule.service_notes || schedule.crew_comments" class="text-xs text-muted-foreground line-clamp-2">
                            {{ schedule.service_notes || schedule.crew_comments }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
