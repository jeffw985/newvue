<script setup lang="ts">
import { Badge } from '@/components/ui/badge'
import { Label } from '@/components/ui/label'
import type { Irrigation } from '@/types/models'

defineProps<{
    irrigation: Irrigation
}>()

const formatDateForDisplay = (dateString: string | null | undefined): string => {
    if (!dateString) return 'N/A'
    // Parse date string manually to avoid timezone issues with date-only strings
    const dateOnly = dateString.split('T')[0].split(' ')[0]
    const [year, month, day] = dateOnly.split('-')
    const date = new Date(parseInt(year), parseInt(month) - 1, parseInt(day))
    return date.toLocaleDateString()
}

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
                <h3 class="text-sm font-semibold border-b pb-1 uppercase tracking-wider text-muted-foreground">General Info</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <Label class="text-xs text-muted-foreground uppercase">Site Address</Label>
                        <p class="text-sm">{{ displayValue(irrigation.site_address) }}</p>
                    </div>
                    <div class="space-y-1">
                        <Label class="text-xs text-muted-foreground uppercase">Subdivision</Label>
                        <p class="text-sm">{{ displayValue(irrigation.subdivision) }}</p>
                    </div>
                    <div class="space-y-1">
                        <Label class="text-xs text-muted-foreground uppercase">Site ID</Label>
                        <p class="text-sm">{{ displayValue(irrigation.site_id) }}</p>
                    </div>
                    <div class="space-y-1">
                        <Label class="text-xs text-muted-foreground uppercase">Sequence Order</Label>
                        <p class="text-sm">{{ displayValue(irrigation.sequence_order) }}</p>
                    </div>
                    <div class="space-y-1">
                        <Label class="text-xs text-muted-foreground uppercase">Turn On</Label>
                        <p class="text-sm">
                            <Badge :variant="irrigation.turn_on ? 'outline-green' : 'outline-red'">{{ irrigation.turn_on ? 'Yes' : 'No' }}</Badge>
                            <span v-if="irrigation.turn_on_date" class="ml-2 text-xs">({{ formatDateForDisplay(irrigation.turn_on_date) }})</span>
                        </p>
                    </div>
                    <div class="space-y-1">
                        <Label class="text-xs text-muted-foreground uppercase">Backflow Testing</Label>
                        <p class="text-sm">
                            <Badge :variant="irrigation.backflow_testing ? 'outline-blue' : 'outline-gray'">{{ irrigation.backflow_testing ? 'Yes' : 'No' }}</Badge>
                            <span v-if="irrigation.backflow_test_date" class="ml-2 text-xs">({{ formatDateForDisplay(irrigation.backflow_test_date) }})</span>
                        </p>
                    </div>
                    <div class="space-y-1">
                        <Label class="text-xs text-muted-foreground uppercase">Blowout</Label>
                        <p class="text-sm">
                            <Badge :variant="irrigation.blowout ? 'outline-green' : 'outline-red'">{{ irrigation.blowout ? 'Yes' : 'No' }}</Badge>
                            <span v-if="irrigation.blowout_date" class="ml-2 text-xs">({{ formatDateForDisplay(irrigation.blowout_date) }})</span>
                        </p>
                    </div>
                    <div class="space-y-1">
                        <Label class="text-xs text-muted-foreground uppercase">Controller Location</Label>
                        <p class="text-sm">{{ displayValue(irrigation.controller_location) }}</p>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <h3 class="text-sm font-semibold border-b pb-1 uppercase tracking-wider text-muted-foreground">Backflow Device</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <Label class="text-xs text-muted-foreground uppercase">Brand</Label>
                        <p class="text-sm">{{ displayValue(irrigation.backflow_brand) }}</p>
                    </div>
                    <div class="space-y-1">
                        <Label class="text-xs text-muted-foreground uppercase">Model</Label>
                        <p class="text-sm">{{ displayValue(irrigation.backflow_model) }}</p>
                    </div>
                    <div class="space-y-1">
                        <Label class="text-xs text-muted-foreground uppercase">Serial #</Label>
                        <p class="text-sm">{{ displayValue(irrigation.backflow_serial) }}</p>
                    </div>
                    <div class="space-y-1">
                        <Label class="text-xs text-muted-foreground uppercase">Type</Label>
                        <p class="text-sm">{{ displayValue(irrigation.backflow_type) }}</p>
                    </div>
                    <div class="space-y-1">
                        <Label class="text-xs text-muted-foreground uppercase">Location</Label>
                        <p class="text-sm">{{ displayValue(irrigation.backflow_location) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <h3 class="text-sm font-semibold border-b pb-1 uppercase tracking-wider text-muted-foreground">Testing</h3>
            <div class="space-y-1">
                <Label class="text-xs text-muted-foreground uppercase">Pass Status</Label>
                <p class="text-sm">
                    <Badge :variant="irrigation.backflow_test_pass ? 'outline-green' : 'outline-red'">{{ irrigation.backflow_test_pass ? 'Passed' : 'Failed/No Data' }}</Badge>
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4 border-t">
                <div class="space-y-4">
                    <h4 class="text-xs font-bold uppercase text-muted-foreground">PVB Results</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <Label class="text-xs text-muted-foreground uppercase">Air Inlet</Label>
                            <p class="text-sm">
                                {{ displayValue(irrigation.pvb_ai) }}
                                <Badge v-if="irrigation.pvb_ai_opened" variant="outline" class="ml-2 text-[10px]">Opened</Badge>
                            </p>
                        </div>
                        <div class="space-y-1">
                            <Label class="text-xs text-muted-foreground uppercase">Check Valve</Label>
                            <p class="text-sm">
                                {{ displayValue(irrigation.pvb_cv) }}
                                <Badge v-if="irrigation.pvb_cv_held" variant="outline" class="ml-2 text-[10px]">Held</Badge>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="space-y-4">
                    <h4 class="text-xs font-bold uppercase text-muted-foreground">RP Results</h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="space-y-1">
                            <Label class="text-xs text-muted-foreground uppercase">CV 1</Label>
                            <p class="text-sm">
                                {{ displayValue(irrigation.rp_cv1) }}
                                <Badge v-if="irrigation.rp_cv1_held" variant="outline" class="ml-2 text-[10px]">Held</Badge>
                            </p>
                        </div>
                        <div class="space-y-1">
                            <Label class="text-xs text-muted-foreground uppercase">CV 2</Label>
                            <p class="text-sm">
                                {{ displayValue(irrigation.rp_cv2) }}
                                <Badge v-if="irrigation.rp_cv2_held" variant="outline" class="ml-2 text-[10px]">Held</Badge>
                            </p>
                        </div>
                        <div class="space-y-1">
                            <Label class="text-xs text-muted-foreground uppercase">RV</Label>
                            <p class="text-sm">
                                {{ displayValue(irrigation.rp_rv) }}
                                <Badge v-if="irrigation.rp_rv_opened" variant="outline" class="ml-2 text-[10px]">Opened</Badge>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-2">
            <Label class="text-xs text-muted-foreground uppercase">Irrigation Notes</Label>
            <p class="text-sm whitespace-pre-wrap rounded-md border bg-muted/50 px-3 py-2 min-h-[60px]">
                {{ displayValue(irrigation.irrig_notes) }}
            </p>
        </div>
    </div>
</template>
