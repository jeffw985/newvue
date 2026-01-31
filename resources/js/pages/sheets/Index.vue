<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { Plus } from 'lucide-vue-next'
import { ref } from 'vue'

import SheetForm from '@/components/forms/SheetForm.vue'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItem } from '@/types'
import type { Sheet } from '@/types/models'

interface PaginatedSheets {
    data: Sheet[]
    current_page: number
    last_page: number
    per_page: number
    total: number
}

defineProps<{
    sheets: PaginatedSheets
}>()

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Sheets',
        href: '/sheets',
    },
]

const isModalOpen = ref(false)
const selectedSheet = ref<Sheet | null>(null)
const modalMode = ref<'create' | 'edit'>('edit')

const handleRowClick = (sheet: Sheet) => {
    selectedSheet.value = sheet
    modalMode.value = 'edit'
    isModalOpen.value = true
}

const handleCreateClick = () => {
    selectedSheet.value = null
    modalMode.value = 'create'
    isModalOpen.value = true
}

const handleSaveSuccess = () => {
    isModalOpen.value = false
    selectedSheet.value = null
}

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

const formatDateRange = (beginDate: string, endDate: string) => {
    return `${formatDate(beginDate)} - ${formatDate(endDate)}`
}
</script>

<template>
    <Head title="Sheets" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <CardTitle>Sheet Records</CardTitle>
                        <Button @click="handleCreateClick" size="sm">
                            <Plus class="mr-2 h-4 w-4" />
                            Create New Sheet
                        </Button>
                    </div>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div v-if="sheets.data.length === 0" class="rounded-md border bg-muted/20 p-6 text-center text-muted-foreground">
                        No sheet records found
                    </div>

                    <div
                        v-for="sheet in sheets.data"
                        :key="sheet.id"
                        class="rounded-md border p-4 hover:bg-muted/50 cursor-pointer transition-colors"
                        @click="handleRowClick(sheet)"
                    >
                        <div class="flex items-start justify-between">
                            <div class="space-y-1 flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-small">Sheet ID.  {{ sheet.id }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <Badge variant="outline-green" class="text-xs">
                                        {{ formatDateRange(sheet.begin_date, sheet.end_date) }}
                                    </Badge>
                                    <Badge
                                        v-if="sheet.ledgers_count"
                                        variant="secondary"
                                        class="text-xs"
                                    >
                                        {{ sheet.ledgers_count }} {{ sheet.ledgers_count === 1 ? 'related ledger entry' : 'related ledger entries' }}
                                    </Badge>
                                </div>
                                <div v-if="sheet.sheet_link" class="text-sm">
                                    <a
                                        :href="sheet.sheet_link"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="text-primary hover:underline"
                                        @click.stop
                                    >
                                        View Sheet Set
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination Info -->
                    <div v-if="sheets.total > 0" class="text-sm text-muted-foreground text-center pt-4">
                        Showing {{ sheets.data.length }} of {{ sheets.total }} records
                        <span v-if="sheets.last_page > 1">
                            (Page {{ sheets.current_page }} of {{ sheets.last_page }})
                        </span>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Create/Edit Modal -->
        <Dialog v-model:open="isModalOpen">
            <DialogContent class="max-w-2xl max-h-[90vh] overflow-y-auto">
                <DialogHeader>
                    <DialogTitle>{{ modalMode === 'create' ? 'Create New Sheet' : 'Edit Sheet Record' }}</DialogTitle>
                </DialogHeader>
                <SheetForm
                    :sheet="selectedSheet || undefined"
                    :mode="modalMode"
                    @success="handleSaveSuccess"
                />
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
