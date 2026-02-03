<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { Plus } from 'lucide-vue-next'
import { ref } from 'vue'

import ProspectForm from '@/components/forms/ProspectForm.vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItem } from '@/types'
import type { Prospect } from '@/types/models'

interface PaginatedProspects {
    data: Prospect[]
    current_page: number
    last_page: number
    per_page: number
    total: number
}

const { prospects } = defineProps<{
    prospects: PaginatedProspects
}>()

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Prospects',
        href: '/prospects',
    },
]

const isModalOpen = ref(false)
const modalMode = ref<'create' | 'edit'>('edit')
const selectedProspect = ref<Prospect | null>(null)

const handleCreateClick = () => {
    selectedProspect.value = {} as Prospect
    modalMode.value = 'create'
    isModalOpen.value = true
}

const handleRowClick = (prospect: Prospect) => {
    selectedProspect.value = prospect
    modalMode.value = 'edit'
    isModalOpen.value = true
}

const handleSaveSuccess = () => {
    isModalOpen.value = false
    selectedProspect.value = null
}

const formatDate = (dateString: string | null | undefined): string => {
    if (!dateString) return ''
    const date = new Date(dateString)
    return date.toLocaleDateString()
}
</script>

<template>
    <Head title="Prospects" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 sm:p-6 lg:p-8">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-4">
                    <CardTitle class="text-2xl font-bold">Prospects</CardTitle>
                    <Button @click="handleCreateClick">
                        <Plus class="mr-2 h-4 w-4" />
                        Add Prospect
                    </Button>
                </CardHeader>
                <CardContent>
                    <div class="rounded-md border">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Date</TableHead>
                                    <TableHead>Name & Address</TableHead>
                                    <TableHead>Contact</TableHead>
                                    <TableHead>Work Description</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow
                                    v-for="prospect in prospects.data"
                                    :key="prospect.id"
                                    class="cursor-pointer hover:bg-muted/50"
                                    @click="handleRowClick(prospect)"
                                >
                                    <TableCell class="whitespace-nowrap">{{ formatDate(prospect.date) }}</TableCell>
                                    <TableCell>
                                        <div class="font-medium text-primary">{{ prospect.name }}</div>
                                        <div class="text-xs text-muted-foreground">
                                            {{ prospect.street }}<br v-if="prospect.street">
                                            {{ prospect.city }}{{ prospect.city && (prospect.state || prospect.zip) ? ',' : '' }} {{ prospect.state }} {{ prospect.zip }}
                                        </div>
                                    </TableCell>
                                    <TableCell>
                                        <div v-if="prospect.phone" class="text-sm">{{ prospect.phone }}</div>
                                        <div v-if="prospect.email" class="text-xs text-muted-foreground">{{ prospect.email }}</div>
                                    </TableCell>
                                    <TableCell class="max-w-xs">
                                        <div class="line-clamp-3 text-sm break-words whitespace-normal">
                                            {{ prospect.work_description }}
                                        </div>
                                    </TableCell>
                                </TableRow>
                                <TableRow v-if="prospects.data.length === 0">
                                    <TableCell colspan="4" class="h-24 text-center">
                                        No prospects found.
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>
                </CardContent>
            </Card>
        </div>

        <Dialog v-model:open="isModalOpen">
            <DialogContent class="sm:max-w-[600px] max-h-[90vh] overflow-y-auto">
                <DialogHeader>
                    <DialogTitle>{{ modalMode === 'create' ? 'Add New Prospect' : 'Edit Prospect' }}</DialogTitle>
                </DialogHeader>
                <ProspectForm
                    v-if="selectedProspect"
                    :prospect="selectedProspect"
                    :mode="modalMode"
                    @success="handleSaveSuccess"
                />
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
