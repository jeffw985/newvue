<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import { MoreVertical, Search, X, Pencil, Trash2 } from 'lucide-vue-next'
import { ref, watch } from 'vue'

import IrrigationForm from '@/components/forms/IrrigationForm.vue'
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
import InlineSelect from '@/components/ui/InlineSelect.vue'
import InlineToggle from '@/components/ui/InlineToggle.vue'
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
import type { Irrigation } from '@/types/models'

const props = defineProps<{
    irrigations: Irrigation[]
    search?: string
    filters: {
        result?: string
        submitted?: string
        billed?: string
        complete?: string
    }
}>()

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Irrigation Tracking',
        href: '/irrigation-completions',
    },
]

const search = ref(props.search || '')
const resultFilter = ref(props.filters.result || '')
const submittedFilter = ref(props.filters.submitted || '')
const billedFilter = ref(props.filters.billed || '')
const completeFilter = ref(props.filters.complete || '')
const isEditDialogOpen = ref(false)
const editingIrrigation = ref<Irrigation | null>(null)

let searchTimeout: ReturnType<typeof setTimeout> | null = null

const applyFilters = () => {
    router.get('/irrigation-completions', {
        search: search.value || undefined,
        result: resultFilter.value || undefined,
        submitted: submittedFilter.value || undefined,
        billed: billedFilter.value || undefined,
        complete: completeFilter.value || undefined,
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
        applyFilters()
    }, 300)
})

watch(resultFilter, () => {
    applyFilters()
})

watch(submittedFilter, () => {
    applyFilters()
})

watch(billedFilter, () => {
    applyFilters()
})

watch(completeFilter, () => {
    applyFilters()
})

const clearSearch = () => {
    search.value = ''
}

const formatDate = (date: string | null): string => {
    if (!date) return 'N/A'
    return new Date(date).toLocaleDateString()
}

const getReadings = (record: Irrigation): string | null => {
    if (record.backflow_type === 'PVB') {
        return `AI: ${record.pvb_ai || 'N/A'} (${record.pvb_ai_opened || 'N/A'}), CV: ${record.pvb_cv || 'N/A'} (${record.pvb_cv_held || 'N/A'})`
    } else if (record.backflow_type === 'RP') {
        return `CV1: ${record.rp_cv1 || 'N/A'} (${record.rp_cv1_held || 'N/A'}), CV2: ${record.rp_cv2 || 'N/A'} (${record.rp_cv2_held || 'N/A'}), RV: ${record.rp_rv || 'N/A'} (${record.rp_rv_opened || 'N/A'})`
    }
    return null
}

const getResultBadgeVariant = (result: string | null) => {
    if (result === 'Pass') return 'green'
    if (result === 'Fail') return 'destructive'
    return 'outline-gray'
}

const getBackflowTypeColor = (type: string | null) => {
    if (type === 'PVB') return 'text-blue-600 dark:text-blue-400'
    if (type === 'RP') return 'text-orange-600 dark:text-orange-400'
    return 'text-foreground'
}

const submittedOptions = [
    { value: 'Submitted', label: 'Submitted' },
    { value: 'Not Required', label: 'Not Required' },
    { value: 'Pending Completion', label: 'Pending Completion' },
]

const billedOptions = [
    { value: 'Billed', label: 'Billed' },
    { value: 'Pending Completion', label: 'Pending Completion' },
    { value: 'No Charge', label: 'No Charge' },
]

const handleEdit = (irrigation: Irrigation) => {
    editingIrrigation.value = irrigation
    isEditDialogOpen.value = true
}

const handleDelete = (irrigation: Irrigation) => {
    if (confirm('Are you sure you want to delete this irrigation record?')) {
        router.delete(`/irrigations/${irrigation.id}`, {
            preserveScroll: true,
        })
    }
}

const handleEditSuccess = () => {
    isEditDialogOpen.value = false
    editingIrrigation.value = null
}
</script>

<template>
    <Head title="Irrigation Tracking" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Irrigation Tracking</h1>
            </div>

            <div class="rounded-md border bg-card">
                <div class="p-4 space-y-4">
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

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-medium">Result:</label>
                            <select
                                v-model="resultFilter"
                                class="border border-border rounded-md px-3 py-2 text-sm bg-background"
                            >
                                <option value="">All Results</option>
                                <option value="pass">Pass</option>
                                <option value="fail">Fail</option>
                                <option value="null">Not Tested</option>
                            </select>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-medium">Submitted:</label>
                            <select
                                v-model="submittedFilter"
                                class="border border-border rounded-md px-3 py-2 text-sm bg-background"
                            >
                                <option value="">All</option>
                                <option value="Submitted">Submitted</option>
                                <option value="Not Required">Not Required</option>
                                <option value="Pending Completion">Pending Completion</option>
                                <option value="null">None (Empty)</option>
                            </select>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-medium">Billed:</label>
                            <select
                                v-model="billedFilter"
                                class="border border-border rounded-md px-3 py-2 text-sm bg-background"
                            >
                                <option value="">All</option>
                                <option value="Billed">Billed</option>
                                <option value="Pending Completion">Pending Completion</option>
                                <option value="No Charge">No Charge</option>
                                <option value="null">None (Empty)</option>
                            </select>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-medium">Complete:</label>
                            <select
                                v-model="completeFilter"
                                class="border border-border rounded-md px-3 py-2 text-sm bg-background"
                            >
                                <option value="">All</option>
                                <option value="complete">Complete</option>
                                <option value="incomplete">Incomplete</option>
                            </select>
                        </div>
                    </div>
                </div>

                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[60px]">ID</TableHead>
                            <TableHead>Customer</TableHead>
                            <TableHead>Turn On</TableHead>
                            <TableHead>Backflow Test</TableHead>
                            <TableHead>Result</TableHead>
                            <TableHead class="w-[300px]">Readings</TableHead>
                            <TableHead class="w-[180px]">Submitted</TableHead>
                            <TableHead class="w-[180px]">Billed</TableHead>
                            <TableHead class="w-[80px]">Complete</TableHead>
                            <TableHead class="w-[50px]"></TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableEmpty v-if="!irrigations.length" :colspan="10">
                            No irrigation records found.
                        </TableEmpty>
                        <TableRow
                            v-for="irrigation in irrigations"
                            :key="irrigation.id"
                            class="cursor-pointer"
                            @click="handleEdit(irrigation)"
                        >
                            <TableCell class="font-medium">
                                {{ irrigation.id }}
                            </TableCell>
                            <TableCell>
                                <div class="flex flex-col">
                                    <span class="font-medium">
                                        {{ irrigation.customer?.full_name || 'N/A' }}
                                    </span>
                                    <span
                                        v-if="irrigation.site_address"
                                        class="text-sm text-muted-foreground"
                                    >
                                        {{ irrigation.site_address }}
                                    </span>
                                </div>
                            </TableCell>
                            <TableCell>
                                {{ formatDate(irrigation.turn_on_date) }}
                            </TableCell>
                            <TableCell>
                                {{ formatDate(irrigation.backflow_test_date) }}
                            </TableCell>
                            <TableCell>
                                <Badge :variant="getResultBadgeVariant(irrigation.backflow_test_pass)">
                                    {{ irrigation.backflow_test_pass || 'N/A' }}
                                </Badge>
                            </TableCell>
                            <TableCell>
                                <div class="flex flex-col gap-1">
                                    <span
                                        v-if="irrigation.backflow_type"
                                        :class="['text-xs font-semibold', getBackflowTypeColor(irrigation.backflow_type)]"
                                    >
                                        {{ irrigation.backflow_type }}
                                    </span>
                                    <span class="text-xs">
                                        {{ getReadings(irrigation) || 'N/A' }}
                                    </span>
                                </div>
                            </TableCell>
                            <TableCell @click.stop>
                                <InlineSelect
                                    v-model="irrigation.submitted"
                                    :options="submittedOptions"
                                    :irrigation-id="irrigation.id"
                                    field="submitted"
                                />
                            </TableCell>
                            <TableCell @click.stop>
                                <InlineSelect
                                    v-model="irrigation.billed"
                                    :options="billedOptions"
                                    :irrigation-id="irrigation.id"
                                    field="billed"
                                />
                            </TableCell>
                            <TableCell @click.stop>
                                <div class="flex justify-center">
                                    <InlineToggle
                                        v-model="irrigation.clear_list"
                                        :irrigation-id="irrigation.id"
                                        field="clear_list"
                                    />
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
                                            @click="handleEdit(irrigation)"
                                        >
                                            <Pencil class="mr-2 h-4 w-4" />
                                            Edit
                                        </DropdownMenuItem>
                                        <DropdownMenuItem
                                            @click="handleDelete(irrigation)"
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
                Showing {{ irrigations.length }} {{ irrigations.length === 1 ? 'record' : 'records' }}
            </div>
        </div>

        <!-- Edit Dialog -->
        <Dialog v-model:open="isEditDialogOpen">
            <DialogContent class="sm:max-w-5xl max-h-[90vh] overflow-y-auto">
                <DialogHeader>
                    <DialogTitle>Edit Irrigation Record</DialogTitle>
                    <DialogDescription>
                        Update the irrigation record details
                    </DialogDescription>
                </DialogHeader>
                <IrrigationForm
                    v-if="editingIrrigation"
                    :irrigation="editingIrrigation"
                    mode="edit"
                    @success="handleEditSuccess"
                />
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
