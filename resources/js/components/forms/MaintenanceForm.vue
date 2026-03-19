<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { X } from 'lucide-vue-next'

import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import type { Maintenance } from '@/types/models'

type FormMode = 'create' | 'edit'

const props = withDefaults(
    defineProps<{
        maintenance?: Partial<Maintenance>
        mode?: FormMode
        submitUrl?: string
        hideSubmitButton?: boolean
        onCancel?: () => void
    }>(),
    {
        mode: 'edit',
        hideSubmitButton: false,
    }
)

const emit = defineEmits<{
    success: [maintenance: Maintenance]
    error: [errors: any]
}>()

const form = useForm({
    cust_id: props.maintenance?.cust_id ?? undefined,
    site_address: props.maintenance?.site_address ?? '',
    subdivision: props.maintenance?.subdivision ?? '',
    service_interval: props.maintenance?.service_interval ?? '',
    mulch_color: props.maintenance?.mulch_color ?? '',
    annual_pay: !!props.maintenance?.annual_pay,
    rock_only: !!props.maintenance?.rock_only,
    mulch_client: !!props.maintenance?.mulch_client,
    sequence_order: props.maintenance?.sequence_order ?? undefined,
    maint_notes: props.maintenance?.maint_notes ?? '',
})

const submit = () => {
    const url = props.submitUrl ?? (
        props.mode === 'create'
            ? '/maintenances'
            : `/maintenances/${props.maintenance?.id}`
    )

    const method = props.mode === 'create' ? 'post' : 'put'

    form[method](url, {
        preserveScroll: true,
        onSuccess: (page) => {
            const updatedMaintenance = (page.props.flash as any)?.maintenance as Maintenance | undefined
            emit('success', updatedMaintenance || props.maintenance as Maintenance)
        },
        onError: (errors) => {
            emit('error', errors)
        },
    })
}
</script>

<template>
    <form @submit.prevent="submit" class="relative space-y-6">
        <div v-if="!hideSubmitButton || onCancel" class="flex justify-end items-center gap-4">
            <Button v-if="!hideSubmitButton" type="submit" size="sm" :disabled="form.processing">
                {{ form.processing ? 'Saving...' : 'Save Changes' }}
            </Button>
            <button
                v-if="onCancel"
                type="button"
                @click="onCancel"
                class="p-1 text-muted-foreground hover:text-foreground transition-colors"
                title="Cancel"
            >
                <X class="h-5 w-5" />
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-4">
                <h3 class="text-sm font-semibold border-b pb-1 uppercase tracking-wider text-muted-foreground">Service Info</h3>

                <div class="space-y-2">
                    <Label for="site_address">Site Address</Label>
                    <Input id="site_address" v-model="form.site_address" />
                    <InputError :message="form.errors.site_address" />
                </div>

                <div class="space-y-2">
                    <Label for="subdivision">Subdivision</Label>
                    <Input id="subdivision" v-model="form.subdivision" />
                    <InputError :message="form.errors.subdivision" />
                </div>

                <div class="space-y-2">
                    <Label for="service_interval">Service Interval</Label>
                    <select
                        id="service_interval"
                        v-model="form.service_interval"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <option value="">Select Interval</option>
                        <option value="Monthly">Monthly</option>
                        <option value="Quarterly">Quarterly</option>
                        <option value="3X Per Year">3X Per Year</option>
                        <option value="Spring & Fall">Spring & Fall</option>
                        <option value="Will Call">Will Call</option>
                    </select>
                    <InputError :message="form.errors.service_interval" />
                </div>

                <div class="space-y-2">
                    <Label for="mulch_color">Mulch Color</Label>
                    <Input id="mulch_color" v-model="form.mulch_color" />
                    <InputError :message="form.errors.mulch_color" />
                </div>

                <div class="flex items-center space-x-2 pt-2">
                    <input
                        id="annual_pay"
                        v-model="form.annual_pay"
                        type="checkbox"
                        class="h-4 w-4 rounded border-gray-300"
                    />
                    <Label for="annual_pay">Annual Pay</Label>
                </div>

                <div class="flex items-center space-x-2 pt-2">
                    <input
                        id="rock_only"
                        v-model="form.rock_only"
                        type="checkbox"
                        class="h-4 w-4 rounded border-gray-300"
                    />
                    <Label for="rock_only">Rock Only</Label>
                </div>

                <div class="flex items-center space-x-2 pt-2">
                    <input
                        id="mulch_client"
                        v-model="form.mulch_client"
                        type="checkbox"
                        class="h-4 w-4 rounded border-gray-300"
                    />
                    <Label for="mulch_client">Mulch Client</Label>
                </div>

                <div class="space-y-2">
                    <Label for="sequence_order">Sequence Order</Label>
                    <Input type="number" id="sequence_order" v-model="form.sequence_order" />
                    <InputError :message="form.errors.sequence_order" />
                </div>

                <div class="space-y-2">
                    <Label for="maint_notes">Maintenance Notes</Label>
                    <textarea
                        id="maint_notes"
                        v-model="form.maint_notes"
                        rows="3"
                        class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    ></textarea>
                    <InputError :message="form.errors.maint_notes" />
                </div>
            </div>
        </div>
    </form>
</template>
