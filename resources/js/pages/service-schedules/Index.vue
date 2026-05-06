<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import { MoreVertical, Search, X, Pencil, Trash2 } from 'lucide-vue-next'
import { ref, watch } from 'vue'

import ServiceScheduleForm from '@/components/forms/ServiceScheduleForm.vue'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog'
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { Input } from '@/components/ui/input'
import {
    Table,
    TableBody,
    TableCell,
    TableEmpty,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItem } from '@/types'
import type { ServiceSchedule } from '@/types/models'

const props = defineProps<{
    schedules: ServiceSchedule[]
    search?: string
    status?: string
}>()

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Service Schedule',
        href: '/service-schedules',
    },
]

const search = ref(props.search || '')
const status = ref(props.status || 'Scheduled')
const isEditDialogOpen = ref(false)
const editingSchedule = ref<ServiceSchedule | null>(null)

let searchTimeout: ReturnType<typeof setTimeout> | null = null

const applySearch = () => {
    router.get('/service-schedules', {
        search: search.value || undefined,
        status: status.value,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}

watch(search, () => {
    if (searchTimeout) {
        clearTimeout(searchTimeout)
    }
    searchTimeout = setTimeout(() => {
        applySearch()
    }, 300)
})

watch(status, () => {
    applySearch()
})

const clearSearch = () => {
    search.value = ''
}

const formatDateTime = (date: string | null): string => {
    if (!date) return 'N/A'
    return new Date(date).toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
        hour12: true,
    })
}

const getServiceRequestedColor = (service: string): 'green' | 'blue' | 'outline-amber' | 'outline' => {
    const vLower = service.toLowerCase()

    // Green badges
    if (vLower.includes('maintenance') || vLower.includes('mulch') ||
        vLower.includes('fall cleanup') || vLower.includes('plant replacement')) {
        return 'green'
    }

    // Blue badges
    if (vLower.includes('irrigation blowout') || vLower.includes('backflow') ||
        vLower.includes('spring start-up') || vLower.includes('spring startup') ||
        vLower.includes('irrigation service')) {
        return 'blue'
    }

    // Amber badges
    if (vLower.includes('small job / repair') || vLower.includes('other - see notes')) {
        return 'outline-amber'
    }

    return 'outline'
}

const getCrewStatusPrefix = (status: string | null): string => {
    if (!status) return ''
    const statusLower = status.toLowerCase()
    if (statusLower === 'completed') return '[✓]'
    if (statusLower === 'did not finish') return '[~]'
    if (statusLower === 'pending') return '[•]'
    return ''
}

const getCrewStatusColor = (status: string | null): string => {
    if (!status) return ''
    const statusLower = status.toLowerCase()
    if (statusLower === 'completed') return 'text-red-600'
    if (statusLower === 'did not finish') return 'text-green-600'
    if (statusLower === 'pending') return 'text-blue-600'
    return ''
}

const handleRowClick = (schedule: ServiceSchedule) => {
    editingSchedule.value = schedule
    isEditDialogOpen.value = true
}

const handleDelete = (schedule: ServiceSchedule) => {
    if (confirm('Are you sure you want to delete this service schedule?')) {
        router.delete(`/service-schedules/${schedule.id}`, {
            preserveScroll: true,
        })
    }
}

const handleEditSuccess = () => {
    isEditDialogOpen.value = false
    editingSchedule.value = null
}
</script>

<template>
    <Head title="Service Schedule" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Service Schedule</h1>
            </div>

            <div class="inline-flex h-10 items-center justify-center rounded-md bg-muted p-1 text-muted-foreground">
                <Button
                    :variant="status === 'Scheduled' ? 'default' : 'ghost'"
                    size="sm"
                    @click="status = 'Scheduled'"
                    class="rounded-sm px-3"
                >
                    Scheduled
                </Button>
                <Button
                    :variant="status === 'Completed' ? 'default' : 'ghost'"
                    size="sm"
                    @click="status = 'Completed'"
                    class="rounded-sm px-3"
                >
                    Completed
                </Button>
            </div>

            <div class="rounded-md border bg-card">
                <div class="p-4">
                    <div class="relative">
                        <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                        <Input
                            v-model="search"
                            type="text"
                            placeholder="Search by customer name or site address..."
                            class="pl-9 pr-9"
                        />
                        <button
                            v-if="search"
                            @click="clearSearch"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors"
                            type="button"
                        >
                            <X class="h-4 w-4" />
                        </button>
                    </div>
                </div>

                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[140px]">Start Time</TableHead>
                            <TableHead>Customer</TableHead>
                            <TableHead class="w-[120px]">Area Group</TableHead>
                            <TableHead class="w-[140px]">Service Interval</TableHead>
                            <TableHead class="w-[200px]">Services Requested</TableHead>
                            <TableHead class="w-[140px]">Crew Assigned</TableHead>
                            <TableHead class="w-[50px]"></TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableEmpty v-if="!schedules.length" :colspan="7">
                            No service schedules found.
                        </TableEmpty>
                        <TableRow
                            v-for="schedule in schedules"
                            :key="schedule.id"
                            class="cursor-pointer"
                            @click="handleRowClick(schedule)"
                        >
                            <TableCell>
                                {{ formatDateTime(schedule.start_time) }}
                            </TableCell>
                            <TableCell>
                                <div class="flex flex-col">
                                    <span class="font-medium">
                                        {{ schedule.customer?.full_name || 'N/A' }}
                                    </span>
                                    <span
                                        v-if="schedule.site_address"
                                        class="text-sm text-muted-foreground"
                                    >
                                        {{ schedule.site_address }}
                                    </span>
                                </div>
                            </TableCell>
                            <TableCell>
                                <Badge v-if="schedule.customer?.area_group_id">
                                    {{ schedule.customer.areaGroup?.area_name || 'N/A' }}
                                </Badge>
                                <span v-else class="text-muted-foreground text-sm">N/A</span>
                            </TableCell>
                            <TableCell>
                                <Badge
                                    v-if="schedule.maintenance?.service_interval"
                                    :variant="
                                        schedule.maintenance.service_interval === 'Monthly' ? 'blue' :
                                        schedule.maintenance.service_interval === 'Quarterly' ? 'outline-purple' :
                                        schedule.maintenance.service_interval === '3X Per Year' ? 'outline-blue' :
                                        schedule.maintenance.service_interval === 'Spring & Fall' ? 'green' :
                                        schedule.maintenance.service_interval === 'Will Call' ? 'outline-amber' :
                                        'outline'
                                    "
                                >
                                    {{ schedule.maintenance.service_interval }}
                                </Badge>
                                <span v-else class="text-muted-foreground text-sm">N/A</span>
                            </TableCell>
                            <TableCell>
                                <div class="flex flex-col gap-1 items-center">
                                    <div class="flex flex-wrap gap-1 justify-center">
                                        <Badge
                                            v-for="service in schedule.service_requested"
                                            :key="service"
                                            :variant="getServiceRequestedColor(service)"
                                            class="text-xs"
                                        >
                                            {{ service }}
                                        </Badge>
                                    </div>
                                    <div v-if="schedule.service_notes" class="text-amber-600 font-bold text-xs text-center">
                                        See Notes
                                    </div>
                                </div>
                            </TableCell>
                            <TableCell>
                                <div class="flex flex-col gap-1">
                                    <span class="text-sm">{{ schedule.crew_assigned || 'N/A' }}</span>
                                    <span
                                        v-if="schedule.crew_status"
                                        :class="['text-xs', getCrewStatusColor(schedule.crew_status)]"
                                    >
                                        {{ getCrewStatusPrefix(schedule.crew_status) }} {{ schedule.crew_status }}
                                    </span>
                                </div>
                            </TableCell>
                            <TableCell @click.stop>
                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child>
                                        <Button variant="ghost" size="icon">
                                            <MoreVertical class="h-4 w-4" />
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end">
                                        <DropdownMenuItem
                                            @click="handleRowClick(schedule)"
                                        >
                                            <Pencil class="mr-2 h-4 w-4" />
                                            Edit
                                        </DropdownMenuItem>
                                        <DropdownMenuItem
                                            @click="handleDelete(schedule)"
                                            class="text-destructive"
                                        >
                                            <Trash2 class="mr-2 h-4 w-4" />
                                            Delete
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <div class="text-sm text-muted-foreground">
                Showing {{ schedules.length }} {{ schedules.length === 1 ? 'record' : 'records' }}
            </div>
        </div>

        <!-- Edit Dialog -->
        <Dialog v-model:open="isEditDialogOpen">
            <DialogContent class="sm:max-w-4xl max-h-[90vh] overflow-y-auto">
                <DialogHeader>
                    <DialogTitle>Service Schedule Details</DialogTitle>
                    <DialogDescription>
                        View and update the service schedule details
                    </DialogDescription>
                </DialogHeader>
                <ServiceScheduleForm
                    v-if="editingSchedule"
                    :schedule="editingSchedule"
                    mode="edit"
                    @success="handleEditSuccess"
                />
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
