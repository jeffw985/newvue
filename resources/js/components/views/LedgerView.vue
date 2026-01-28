<script setup lang="ts">
import { Pencil } from 'lucide-vue-next'
import { computed } from 'vue'

import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Label } from '@/components/ui/label'
import type { Ledger } from '@/types/models'

const props = defineProps<{
    ledger: Ledger
    showEditButton?: boolean
}>()

const emit = defineEmits<{
    edit: [ledger: Ledger]
}>()

const displayValue = (value: string | number | null | undefined): string => {
    if (value === null || value === undefined || value === '') return 'N/A'
    return value.toString()
}

const formatDateForDisplay = (dateString: string | null | undefined): string => {
    if (!dateString) return 'N/A'
    // Parse date string manually to avoid timezone issues with date-only strings
    const dateOnly = dateString.split('T')[0].split(' ')[0]
    const [year, month, day] = dateOnly.split('-')
    const date = new Date(parseInt(year), parseInt(month) - 1, parseInt(day))
    return date.toLocaleDateString()
}

const sheetDisplay = computed(() => {
    if (!props.ledger.sheet) return 'N/A'
    return `${formatDateForDisplay(props.ledger.sheet.begin_date)} - ${formatDateForDisplay(props.ledger.sheet.end_date)}`
})

const workTypeVariant = computed(() => {
    switch (props.ledger.work_type) {
        case 'Site Work':
            return 'outline-blue'
        case 'Irrigation':
            return 'outline-amber'
        case 'Maintenance':
            return 'outline-green'
        default:
            return 'outline'
    }
})
</script>

<template>
    <div class="space-y-6">
        <!-- Header with Edit Button -->
        <div v-if="showEditButton" class="flex justify-end">
            <Button size="sm" @click="emit('edit', ledger)">
                <Pencil class="mr-2 h-4 w-4" />
                Edit
            </Button>
        </div>

        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <!-- Customer -->
                <div class="space-y-2">
                    <Label class="text-muted-foreground">Customer</Label>
                    <p class="text-base font-medium">
                        {{ ledger.customer?.full_name || 'N/A' }}
                    </p>
                </div>

                <!-- Work Date -->
                <div class="space-y-2">
                    <Label class="text-muted-foreground">Work Date</Label>
                    <p class="text-base">{{ formatDateForDisplay(ledger.work_date) }}</p>
                </div>
            </div>

            <!-- Sheet Number -->
            <div class="space-y-2">
                <Label class="text-muted-foreground">Sheet Number</Label>
                <p class="text-base">
                    <a
                        v-if="ledger.sheet?.sheet_link"
                        :href="ledger.sheet.sheet_link"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="text-blue-600 hover:underline dark:text-blue-400"
                    >
                        {{ displayValue(ledger.sheet_number) }}
                    </a>
                    <span v-else>{{ displayValue(ledger.sheet_number) }}</span>
                </p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <!-- Times -->
                <div class="space-y-2">
                    <Label class="text-muted-foreground">Times</Label>
                    <p class="text-base">{{ displayValue(ledger.times) }}</p>
                </div>

                <!-- Hours -->
                <div class="space-y-2">
                    <Label class="text-muted-foreground">Hours</Label>
                    <p class="text-base">{{ ledger.hours !== null && ledger.hours !== undefined ? Number(ledger.hours).toFixed(2) : 'N/A' }}</p>
                </div>
            </div>

            <!-- Work Type -->
            <div class="space-y-2">
                <Label class="text-muted-foreground">Work Type</Label>
                <div>
                    <Badge :variant="workTypeVariant">
                        {{ ledger.work_type }}
                    </Badge>
                </div>
            </div>

            <!-- Work Performed -->
            <div class="space-y-2">
                <Label class="text-muted-foreground">Work Performed</Label>
                <p class="text-base whitespace-pre-wrap rounded-md border border-input bg-muted/50 px-3 py-2 min-h-[60px]">
                    {{ displayValue(ledger.work_performed) }}
                </p>
            </div>

            <!-- Job Notes -->
            <div class="space-y-2">
                <Label class="text-muted-foreground">Job Notes</Label>
                <p class="text-base whitespace-pre-wrap rounded-md border border-input bg-muted/50 px-3 py-2 min-h-[60px]">
                    {{ displayValue(ledger.job_notes) }}
                </p>
            </div>

            <!-- Job Code -->
            <div class="space-y-2">
                <Label class="text-muted-foreground">Job Code</Label>
                <p class="text-base">{{ displayValue(ledger.job_code) }}</p>
            </div>

            <!-- Sheet Link -->
            <div class="space-y-2">
                <Label class="text-muted-foreground">Sheet</Label>
                <p class="text-base">
                    <a
                        v-if="ledger.sheet?.sheet_link"
                        :href="ledger.sheet.sheet_link"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="text-blue-600 hover:underline dark:text-blue-400"
                    >
                        {{ sheetDisplay }}
                    </a>
                    <span v-else>{{ sheetDisplay }}</span>
                </p>
            </div>

            <!-- Billed -->
            <div class="space-y-2">
                <Label class="text-muted-foreground">Billed Status</Label>
                <div>
                    <Badge :variant="ledger.billed ? 'outline-green' : 'outline-red'">
                        {{ ledger.billed ? 'Billed' : 'Unbilled' }}
                    </Badge>
                </div>
            </div>
        </div>
    </div>
</template>
