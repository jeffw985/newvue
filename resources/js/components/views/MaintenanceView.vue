<script setup lang="ts">
import { Badge } from '@/components/ui/badge'
import { Label } from '@/components/ui/label'
import type { Maintenance } from '@/types/models'

defineProps<{
    maintenance: Maintenance
}>()

const displayValue = (value: any): string => {
    if (value === null || value === undefined || value === '') return 'N/A'
    if (typeof value === 'boolean') return value ? 'Yes' : 'No'
    return value.toString()
}
</script>

<template>
    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <h3 class="text-sm font-semibold border-b pb-1 uppercase tracking-wider text-muted-foreground">Service Info</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <Label class="text-xs text-muted-foreground uppercase">Site Address</Label>
                        <p class="text-sm">{{ displayValue(maintenance.site_address) }}</p>
                    </div>
                    <div class="space-y-1">
                        <Label class="text-xs text-muted-foreground uppercase">Subdivision</Label>
                        <p class="text-sm">{{ displayValue(maintenance.subdivision) }}</p>
                    </div>
                    <div class="space-y-1">
                        <Label class="text-xs text-muted-foreground uppercase">Service Interval</Label>
                        <p class="text-sm">{{ displayValue(maintenance.service_interval) }}</p>
                    </div>
                    <div class="space-y-1">
                        <Label class="text-xs text-muted-foreground uppercase">Mulch Color</Label>
                        <p class="text-sm">{{ displayValue(maintenance.mulch_color) }}</p>
                    </div>
                    <div class="space-y-1">
                        <Label class="text-xs text-muted-foreground uppercase">Annual Pay</Label>
                        <p class="text-sm">
                            <Badge :variant="maintenance.annual_pay ? 'outline-green' : 'outline-red'">{{ maintenance.annual_pay ? 'Yes' : 'No' }}</Badge>
                        </p>
                    </div>
                    <div class="space-y-1">
                        <Label class="text-xs text-muted-foreground uppercase">Sequence Order</Label>
                        <p class="text-sm">{{ displayValue(maintenance.sequence_order) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
