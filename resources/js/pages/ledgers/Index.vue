<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import { Search, X } from 'lucide-vue-next'
import { ref, computed, watch } from 'vue'

import LedgerForm from '@/components/forms/LedgerForm.vue'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItem } from '@/types'
import type { Customer, Ledger, Sheet } from '@/types/models'

interface PaginatedLedgers {
    data: Ledger[]
    current_page: number
    last_page: number
    per_page: number
    total: number
}

const props = defineProps<{
    ledgers: PaginatedLedgers
    customers: Customer[]
    sheets: Sheet[]
    filter: 'all' | 'billed' | 'unbilled'
    search?: string
    work_type?: string
}>()

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Ledger',
        href: '/ledgers',
    },
]

const isModalOpen = ref(false)
const modalMode = ref<'create' | 'edit'>('edit')
const selectedLedger = ref<Ledger | null>(null)
const currentFilter = ref(props.filter)
const searchQuery = ref(props.search || '')
const currentWorkType = ref(props.work_type || 'all')
let searchTimeout: ReturnType<typeof setTimeout> | null = null

// Group ledgers by customer
const groupedLedgers = computed(() => {
    const groups = new Map<number, { customer: Customer; ledgers: Ledger[] }>()

    props.ledgers.data.forEach((ledger) => {
        if (!ledger.customer) return

        if (!groups.has(ledger.cust_id)) {
            groups.set(ledger.cust_id, {
                customer: ledger.customer,
                ledgers: [],
            })
        }

        groups.get(ledger.cust_id)!.ledgers.push(ledger)
    })

    return Array.from(groups.values())
})

const handleCreateClick = () => {
    selectedLedger.value = {} as Ledger
    modalMode.value = 'create'
    isModalOpen.value = true
}

const handleRowClick = (ledger: Ledger) => {
    selectedLedger.value = ledger
    modalMode.value = 'edit'
    isModalOpen.value = true
}

const handleSaveSuccess = () => {
    isModalOpen.value = false
    selectedLedger.value = null
}

const handleFilterChange = (filter: 'all' | 'billed' | 'unbilled') => {
    currentFilter.value = filter
    performSearch()
}

const handleWorkTypeChange = (workType: string) => {
    currentWorkType.value = workType
    performSearch()
}

const performSearch = () => {
    const params: Record<string, string> = {
        filter: currentFilter.value,
        work_type: currentWorkType.value,
    }

    if (searchQuery.value) {
        params.search = searchQuery.value
    }

    router.get('/ledgers', params, { preserveState: true, preserveScroll: true })
}

// Debounced search
watch(searchQuery, () => {
    if (searchTimeout) {
        clearTimeout(searchTimeout)
    }
    searchTimeout = setTimeout(() => {
        performSearch()
    }, 300)
})

const clearSearch = () => {
    searchQuery.value = ''
}

const formatDate = (dateString: string) => {
    // Parse date string as YYYY-MM-DD without timezone conversion
    const [year, month, day] = dateString.split('T')[0].split('-')
    const date = new Date(parseInt(year), parseInt(month) - 1, parseInt(day))
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    })
}

const formatAddress = (customer: Customer) => {
    const parts = [customer.street, customer.city, customer.state, customer.zipcode].filter(Boolean)
    return parts.join(', ')
}

const calculateTotalHours = (ledgers: Ledger[]) => {
    return ledgers.reduce((sum, ledger) => {
        const hours = ledger.hours ? parseFloat(ledger.hours.toString()) : 0
        return sum + hours
    }, 0).toFixed(2)
}
</script>

<template>
    <Head title="Ledger" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <Card>
                <CardHeader>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <CardTitle>Ledger Records</CardTitle>
                            <div class="flex items-center gap-2">
                                <Button
                                    size="sm"
                                    @click="handleCreateClick"
                                >
                                    Create Ledger
                                </Button>
                                <div class="flex gap-2 ml-4">
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        :class="{ '!bg-white !text-black hover:!bg-white hover:!text-black': currentFilter === 'all' }"
                                        @click="handleFilterChange('all')"
                                    >
                                        All
                                    </Button>
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        :class="{ '!bg-white !text-black hover:!bg-white hover:!text-black': currentFilter === 'unbilled' }"
                                        @click="handleFilterChange('unbilled')"
                                    >
                                        Unbilled
                                    </Button>
                                </div>
                            </div>
                        </div>

                        <!-- Search Box -->
                        <div class="relative">
                            <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                            <Input
                                v-model="searchQuery"
                                placeholder="Search by customer name or street..."
                                class="pl-9 pr-9"
                            />
                            <button
                                v-if="searchQuery"
                                @click="clearSearch"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors"
                                type="button"
                            >
                                <X class="h-4 w-4" />
                            </button>
                        </div>

                        <!-- Work Type Filters -->
                        <div class="flex gap-2">
                            <Button
                                variant="outline"
                                size="sm"
                                :class="{ '!bg-white !text-black hover:!bg-white hover:!text-black': currentWorkType === 'all' }"
                                @click="handleWorkTypeChange('all')"
                            >
                                All Types
                            </Button>
                            <Button
                                variant="outline"
                                size="sm"
                                :class="{ '!bg-white !text-black hover:!bg-white hover:!text-black': currentWorkType === 'Site Work' }"
                                @click="handleWorkTypeChange('Site Work')"
                            >
                                Site Work
                            </Button>
                            <Button
                                variant="outline"
                                size="sm"
                                :class="{ '!bg-white !text-black hover:!bg-white hover:!text-black': currentWorkType === 'Irrigation' }"
                                @click="handleWorkTypeChange('Irrigation')"
                            >
                                Irrigation
                            </Button>
                            <Button
                                variant="outline"
                                size="sm"
                                :class="{ '!bg-white !text-black hover:!bg-white hover:!text-black': currentWorkType === 'Maintenance' }"
                                @click="handleWorkTypeChange('Maintenance')"
                            >
                                Maintenance
                            </Button>
                        </div>
                    </div>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div v-if="groupedLedgers.length === 0" class="rounded-md border bg-muted/20 p-6 text-center text-muted-foreground">
                        No ledger records found
                    </div>

                    <div v-for="group in groupedLedgers" :key="group.customer.id" class="space-y-3">
                        <!-- Customer Header -->
                        <div class="rounded-md bg-muted p-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-semibold text-lg">
                                        {{ group.customer.full_name }}
                                        <span class="text-sm text-muted-foreground ml-2">
                                            #{{ group.customer.id }}
                                        </span>
                                    </h3>
                                    <p class="text-sm text-muted-foreground">
                                        {{ formatAddress(group.customer) }}
                                    </p>
                                </div>
                                <div class="flex gap-2">
                                    <Badge variant="secondary">
                                        {{ group.ledgers.length }} {{ group.ledgers.length === 1 ? 'record' : 'records' }}
                                    </Badge>
                                    <Badge variant="outline">
                                        {{ calculateTotalHours(group.ledgers) }} hrs
                                    </Badge>
                                </div>
                            </div>
                        </div>

                        <!-- Ledger Items -->
                        <div class="space-y-2">
                            <div
                                v-for="ledger in group.ledgers"
                                :key="ledger.id"
                                class="rounded-md border p-3 hover:bg-muted/50 cursor-pointer transition-colors"
                                @click="handleRowClick(ledger)"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="space-y-1 flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="font-medium">{{ formatDate(ledger.work_date) }}</span>
                                            <span class="text-sm text-muted-foreground">{{ ledger.customer?.full_name }}</span>
                                            <Badge
                                                :variant="ledger.work_type === 'Irrigation' ? 'outline-blue' : ledger.work_type === 'Maintenance' ? 'outline-green' : 'outline-gray'"
                                                class="text-xs"
                                            >
                                                {{ ledger.work_type }}
                                            </Badge>
                                            <Badge
                                                :variant="ledger.billed ? 'outline-green' : 'outline-red'"
                                                class="text-xs"
                                            >
                                                {{ ledger.billed ? 'Billed' : 'Unbilled' }}
                                            </Badge>
                                        </div>
                                        <div class="text-sm text-muted-foreground">
                                            <p v-if="ledger.work_performed">{{ ledger.work_performed }}</p>
                                            <p v-if="ledger.job_notes" class="mt-1">{{ ledger.job_notes }}</p>
                                            <div class="flex gap-4 mt-1">
                                                <span v-if="ledger.hours">Hours: {{ ledger.hours }}</span>
                                                <span v-if="ledger.sheet_number">
                                                    <span class="text-muted-foreground">Sheet: </span>
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
                                                    <span v-else class="text-muted-foreground">{{ ledger.sheet_number }}</span>
                                                </span>
                                                <span v-if="ledger.job_code">Code: {{ ledger.job_code }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination Info -->
                    <div v-if="ledgers.total > 0" class="text-sm text-muted-foreground text-center pt-4">
                        Showing {{ ledgers.data.length }} of {{ ledgers.total }} records
                        <span v-if="ledgers.last_page > 1">
                            (Page {{ ledgers.current_page }} of {{ ledgers.last_page }})
                        </span>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Create/Edit Modal -->
        <Dialog v-model:open="isModalOpen">
            <DialogContent class="max-w-2xl max-h-[90vh] overflow-y-auto">
                <DialogHeader>
                    <DialogTitle>{{ modalMode === 'create' ? 'Create Ledger Record' : 'Edit Ledger Record' }}</DialogTitle>
                </DialogHeader>
                <LedgerForm
                    v-if="selectedLedger"
                    :ledger="selectedLedger"
                    :customers="customers"
                    :sheets="sheets"
                    :mode="modalMode"
                    @success="handleSaveSuccess"
                />
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
