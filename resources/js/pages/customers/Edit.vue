<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { Pencil, Plus } from 'lucide-vue-next'
import { ref, toRefs } from 'vue'

import RelatedRecordsTabs from '@/components/customers/RelatedRecordsTabs.vue'
import CustomerForm from '@/components/forms/CustomerForm.vue'
import IrrigationForm from '@/components/forms/IrrigationForm.vue'
import LedgerForm from '@/components/forms/LedgerForm.vue'
import MaintenanceForm from '@/components/forms/MaintenanceForm.vue'
import ScheduleForm from '@/components/forms/ScheduleForm.vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog'
import CustomerView from '@/components/views/CustomerView.vue'
import IrrigationView from '@/components/views/IrrigationView.vue'
import LedgerView from '@/components/views/LedgerView.vue'
import MaintenanceView from '@/components/views/MaintenanceView.vue'
import ScheduleView from '@/components/views/ScheduleView.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItem } from '@/types'
import type { Customer } from '@/types/models'

const props = defineProps<{
    customer: Customer
}>()

// Destructure props while maintaining reactivity
const { customer } = toRefs(props)

const isEditMode = ref(false)
const isRecordEditMode = ref(false)
const currentTab = ref<'customer' | 'ledgers' | 'irrigations' | 'maintenances' | 'schedules'>( 'customer')
const selectedRecord = ref<{ type: string; data: any } | null>(null)
const isCreateLedgerDialogOpen = ref(false)
const isCreateIrrigationDialogOpen = ref(false)
const isCreateMaintenanceDialogOpen = ref(false)
const isCreateScheduleDialogOpen = ref(false)

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Customers',
        href: '/customers',
    },
    {
        title: customer.value.full_name || `Customer #${customer.value.id}`,
        href: `/customers/${customer.value.id}`,
    },
]

const toggleEditMode = () => {
    isEditMode.value = !isEditMode.value
}

const handleSaveSuccess = () => {
    isEditMode.value = false
}

const submitForm = () => {
    const formElement = document.querySelector('form')
    if (formElement) {
        formElement.requestSubmit()
    }
}

const handleTabChange = (tab: 'customer' | 'ledgers' | 'irrigations' | 'maintenances' | 'schedules') => {
    currentTab.value = tab
    selectedRecord.value = null
    isRecordEditMode.value = false
    // When switching to customer tab, exit edit mode and show CustomerView
    if (tab === 'customer') {
        isEditMode.value = false
    }
}

const handleRecordSelected = (type: string, record: any) => {
    selectedRecord.value = { type, data: record }
    isRecordEditMode.value = false
}

const handleRecordEdit = () => {
    isRecordEditMode.value = true
}

const handleRecordSaveSuccess = (updatedRecord?: any) => {
    isRecordEditMode.value = false
    if (selectedRecord.value) {
        if (updatedRecord) {
            // Update the selected record data
            selectedRecord.value.data = { ...selectedRecord.value.data, ...updatedRecord }

            // Also update the record in the customer object to reflect in the tabs
            if (customer.value) {
                const type = selectedRecord.value.type === 'schedule' ? 'service_schedules' : `${selectedRecord.value.type}s`
                const records = (customer.value as any)[type] as any[] | undefined
                if (records) {
                    const index = records.findIndex((r) => r.id === updatedRecord.id)
                    if (index !== -1) {
                        records[index] = { ...records[index], ...updatedRecord }
                    }
                }
            }
        }
    }
}

const handleCreateLedgerSuccess = (newLedger?: any) => {
    isCreateLedgerDialogOpen.value = false
    // Refresh the page to show the new ledger item
    if (newLedger) {
        window.location.reload()
    }
}

const handleCreateIrrigationSuccess = (newIrrigation?: any) => {
    isCreateIrrigationDialogOpen.value = false
    // Refresh the page to show the new irrigation record
    if (newIrrigation) {
        window.location.reload()
    }
}

const handleCreateMaintenanceSuccess = (newMaintenance?: any) => {
    isCreateMaintenanceDialogOpen.value = false
    // Refresh the page to show the new maintenance record
    if (newMaintenance) {
        window.location.reload()
    }
}

const handleCreateScheduleSuccess = (newSchedule?: any) => {
    isCreateScheduleDialogOpen.value = false
    // Refresh the page to show the new schedule
    if (newSchedule) {
        window.location.reload()
    }
}
</script>

<template>
    <Head :title="`Edit ${customer.full_name || 'Customer'}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 gap-4 p-4">
            <!-- Left Pane: Related Records -->
            <div class="w-1/3 space-y-4 overflow-y-auto">
                <Card>
                    <CardHeader>
                        <CardTitle>Related Records</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <RelatedRecordsTabs
                            :customer="customer"
                            :current-tab="currentTab"
                            @tab-change="handleTabChange"
                            @record-selected="handleRecordSelected"
                        />
                    </CardContent>
                </Card>
            </div>

            <!-- Right Pane: Customer Details/Edit Form -->
            <div class="flex-1 overflow-y-auto">
                <Card v-if="currentTab === 'customer'">
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <CardTitle>
                                {{ isEditMode ? 'Edit Customer' : 'Customer Details' }}
                                <span
                                    v-if="customer.full_name"
                                    class="text-muted-foreground"
                                >
                                    - {{ customer.full_name }}
                                </span>
                            </CardTitle>
                            <Button
                                v-if="!isEditMode"
                                @click="toggleEditMode"
                                size="sm"
                            >
                                <Pencil class="mr-2 h-4 w-4" />
                                Edit
                            </Button>
                            <Button
                                v-else
                                @click="submitForm"
                                size="sm"
                            >
                                Save Changes
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <CustomerView
                            v-if="!isEditMode"
                            :customer="customer"
                        />
                        <CustomerForm
                            v-else
                            :customer="customer"
                            hide-submit-button
                            @success="handleSaveSuccess"
                        />
                    </CardContent>
                </Card>

                <!-- Placeholder for other tabs -->
                <Card v-else>
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <CardTitle>
                                {{ selectedRecord ? (isRecordEditMode ? `Edit ${selectedRecord.type}` : `${selectedRecord.type} Details`) : (currentTab === 'ledgers' ? 'Ledger' : currentTab === 'irrigations' ? 'Irrigation' : currentTab === 'maintenances' ? 'Maintenance' : 'Schedules') }}
                            </CardTitle>
                            <div class="flex gap-2">
                                <Button
                                    v-if="!selectedRecord && currentTab === 'ledgers'"
                                    @click="isCreateLedgerDialogOpen = true"
                                    size="sm"
                                >
                                    <Plus class="mr-2 h-4 w-4" />
                                    Add Ledger Item
                                </Button>
                                <Button
                                    v-if="!selectedRecord && currentTab === 'irrigations'"
                                    @click="isCreateIrrigationDialogOpen = true"
                                    size="sm"
                                >
                                    <Plus class="mr-2 h-4 w-4" />
                                    Add Irrigation
                                </Button>
                                <Button
                                    v-if="!selectedRecord && currentTab === 'maintenances'"
                                    @click="isCreateMaintenanceDialogOpen = true"
                                    size="sm"
                                >
                                    <Plus class="mr-2 h-4 w-4" />
                                    Add Maintenance
                                </Button>
                                <Button
                                    v-if="!selectedRecord && currentTab === 'schedules'"
                                    @click="isCreateScheduleDialogOpen = true"
                                    size="sm"
                                >
                                    <Plus class="mr-2 h-4 w-4" />
                                    Schedule Service
                                </Button>
                                <Button
                                    v-if="selectedRecord && !isRecordEditMode"
                                    @click="handleRecordEdit"
                                    size="sm"
                                >
                                    <Pencil class="mr-2 h-4 w-4" />
                                    Edit
                                </Button>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <template v-if="selectedRecord">
                            <!-- Ledger -->
                            <template v-if="selectedRecord.type === 'ledger'">
                                <LedgerView
                                    v-if="!isRecordEditMode"
                                    :ledger="selectedRecord.data"
                                />
                                <LedgerForm
                                    v-else
                                    :ledger="selectedRecord.data"
                                    :customers="[customer]"
                                    @success="handleRecordSaveSuccess"
                                />
                            </template>

                            <!-- Irrigation -->
                            <template v-else-if="selectedRecord.type === 'irrigation'">
                                <IrrigationView
                                    v-if="!isRecordEditMode"
                                    :irrigation="selectedRecord.data"
                                />
                                <IrrigationForm
                                    v-else
                                    :irrigation="selectedRecord.data"
                                    @success="handleRecordSaveSuccess"
                                />
                            </template>

                            <!-- Maintenance -->
                            <template v-else-if="selectedRecord.type === 'maintenance'">
                                <MaintenanceView
                                    v-if="!isRecordEditMode"
                                    :maintenance="selectedRecord.data"
                                />
                                <MaintenanceForm
                                    v-else
                                    :maintenance="selectedRecord.data"
                                    @success="handleRecordSaveSuccess"
                                />
                            </template>

                            <!-- Schedule -->
                            <template v-else-if="selectedRecord.type === 'schedule' || selectedRecord.type === 'service_schedule'">
                                <ScheduleView
                                    v-if="!isRecordEditMode"
                                    :schedule="selectedRecord.data"
                                />
                                <ScheduleForm
                                    v-else
                                    :schedule="selectedRecord.data"
                                    @success="handleRecordSaveSuccess"
                                />
                            </template>

                            <div
                                v-else
                                class="rounded-md border p-6"
                            >
                                <h3 class="text-lg font-medium mb-4 capitalize">{{ selectedRecord.type }} Details</h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div v-for="(value, key) in selectedRecord.data" :key="key" class="space-y-1">
                                        <label class="text-xs text-muted-foreground uppercase">{{ String(key).replace(/_/g, ' ') }}</label>
                                        <p class="text-sm">{{ value || 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <div
                            v-else
                            class="rounded-md border bg-muted/20 p-6 text-center text-muted-foreground"
                        >
                            Select a record to view details
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>

        <!-- Create Ledger Dialog -->
        <Dialog v-model:open="isCreateLedgerDialogOpen">
            <DialogContent class="sm:max-w-2xl max-h-[90vh] overflow-y-auto">
                <DialogHeader>
                    <DialogTitle>Create Ledger Item</DialogTitle>
                    <DialogDescription>
                        Add a new ledger item for {{ customer.full_name }}
                    </DialogDescription>
                </DialogHeader>
                <LedgerForm
                    mode="create"
                    :ledger="{ cust_id: customer.id }"
                    :customers="[customer]"
                    @success="handleCreateLedgerSuccess"
                />
            </DialogContent>
        </Dialog>

        <!-- Create Irrigation Dialog -->
        <Dialog v-model:open="isCreateIrrigationDialogOpen">
            <DialogContent class="sm:max-w-2xl max-h-[90vh] overflow-y-auto">
                <DialogHeader>
                    <DialogTitle>Create Irrigation Record</DialogTitle>
                    <DialogDescription>
                        Add a new irrigation record for {{ customer.full_name }}
                    </DialogDescription>
                </DialogHeader>
                <IrrigationForm
                    mode="create"
                    :irrigation="{ cust_id: customer.id }"
                    @success="handleCreateIrrigationSuccess"
                />
            </DialogContent>
        </Dialog>

        <!-- Create Maintenance Dialog -->
        <Dialog v-model:open="isCreateMaintenanceDialogOpen">
            <DialogContent class="sm:max-w-2xl max-h-[90vh] overflow-y-auto">
                <DialogHeader>
                    <DialogTitle>Create Maintenance Record</DialogTitle>
                    <DialogDescription>
                        Add a new maintenance record for {{ customer.full_name }}
                    </DialogDescription>
                </DialogHeader>
                <MaintenanceForm
                    mode="create"
                    :maintenance="{ cust_id: customer.id }"
                    @success="handleCreateMaintenanceSuccess"
                />
            </DialogContent>
        </Dialog>

        <!-- Create Schedule Dialog -->
        <Dialog v-model:open="isCreateScheduleDialogOpen">
            <DialogContent class="sm:max-w-2xl max-h-[90vh] overflow-y-auto">
                <DialogHeader>
                    <DialogTitle>Schedule Service</DialogTitle>
                    <DialogDescription>
                        Add a new service schedule for {{ customer.full_name }}
                    </DialogDescription>
                </DialogHeader>
                <ScheduleForm
                    mode="create"
                    :schedule="{ cust_id: customer.id }"
                    @success="handleCreateScheduleSuccess"
                />
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
