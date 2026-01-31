<script setup lang="ts">
import { Pencil } from 'lucide-vue-next'

import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Label } from '@/components/ui/label'
import type { Customer } from '@/types/models'

const props = defineProps<{
    customer: Customer
    showEditButton?: boolean
}>()

const emit = defineEmits<{
    edit: []
}>()

const displayValue = (value: string | null | undefined): string => {
    return value || 'N/A'
}

const formatAddress = (): string => {
    const parts = [
        props.customer.street,
        props.customer.city,
        props.customer.state,
        props.customer.zipcode,
    ].filter(Boolean)
    return parts.length > 0 ? parts.join(', ') : 'N/A'
}
</script>

<template>
    <div class="space-y-6">
        <div class="space-y-4">
            <!-- Name Fields -->
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <Label class="text-muted-foreground">Contact Name</Label>
                    <p class="text-base font-medium">
                        {{ displayValue(customer.full_name) }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label class="text-muted-foreground">Customer ID</Label>
                    <p class="text-base">{{ customer.id }}</p>
                </div>
            </div>

            <!-- Name Fields -->
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <Label class="text-muted-foreground">First Name</Label>
                    <p class="text-base">{{ displayValue(customer.first) }}</p>
                </div>

                <div class="space-y-2">
                    <Label class="text-muted-foreground">Last Name</Label>
                    <p class="text-base">{{ displayValue(customer.last) }}</p>
                </div>
            </div>

            <!-- Spouse Fields -->
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <Label class="text-muted-foreground">Spouse First Name</Label>
                    <p class="text-base">{{ displayValue(customer.spouse_first) }}</p>
                </div>

                <div class="space-y-2">
                    <Label class="text-muted-foreground">Spouse Last Name</Label>
                    <p class="text-base">{{ displayValue(customer.spouse_last) }}</p>
                </div>
            </div>

            <!-- Company -->
            <div class="space-y-2">
                <Label class="text-muted-foreground">Company</Label>
                <p class="text-base">{{ displayValue(customer.company) }}</p>
            </div>

            <!-- Address -->
            <div class="space-y-2">
                <Label class="text-muted-foreground">Address</Label>
                <p class="text-base">{{ formatAddress() }}</p>
            </div>

            <!-- Phone Fields -->
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <Label class="text-muted-foreground">Phone</Label>
                    <p class="text-base">{{ displayValue(customer.phone) }}</p>
                </div>

                <div class="space-y-2">
                    <Label class="text-muted-foreground">Phone 2</Label>
                    <p class="text-base">{{ displayValue(customer.phone2) }}</p>
                </div>
            </div>

            <!-- Email Fields -->
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <Label class="text-muted-foreground">Email</Label>
                    <p class="text-base">{{ displayValue(customer.email) }}</p>
                </div>

                <div class="space-y-2">
                    <Label class="text-muted-foreground">Email 2</Label>
                    <p class="text-base">{{ displayValue(customer.email2) }}</p>
                </div>
            </div>

            <!-- Services -->
            <div class="space-y-2">
                <Label class="text-muted-foreground">Services</Label>
                <div class="flex gap-2">
                    <Badge v-if="customer.irrigation" variant="outline-blue">
                        Irrigation
                    </Badge>
                    <Badge v-if="customer.maintenance" variant="outline-green">
                        Maintenance
                    </Badge>
                    <span v-if="!customer.irrigation && !customer.maintenance" class="text-base">
                        N/A
                    </span>
                </div>
            </div>

            <!-- Notes -->
            <div class="space-y-2">
                <Label class="text-muted-foreground">Notes</Label>
                <p
                    class="text-base whitespace-pre-wrap rounded-md border border-input bg-muted/50 px-3 py-2 min-h-[100px]"
                >
                    {{ displayValue(customer.notes) }}
                </p>
            </div>
        </div>

        <!-- Edit Button (Optional) -->
        <div v-if="showEditButton" class="flex justify-end gap-2">
            <Button @click="emit('edit')">
                <Pencil class="mr-2 h-4 w-4" />
                Edit Customer
            </Button>
        </div>
    </div>
</template>
