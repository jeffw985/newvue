<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import type { Irrigation } from '@/types/models'


type FormMode = 'create' | 'edit'

const props = withDefaults(
    defineProps<{
        irrigation?: Partial<Irrigation>
        mode?: FormMode
        submitUrl?: string
        hideSubmitButton?: boolean
    }>(),
    {
        mode: 'edit',
        hideSubmitButton: false,
    }
)

const emit = defineEmits<{
    success: [irrigation: Irrigation]
    error: [errors: any]
}>()

const formatDateForInput = (dateString: string | null | undefined): string | undefined => {
    if (!dateString) return undefined
    return dateString.split('T')[0].split(' ')[0]
}

const form = useForm({
    cust_id: props.irrigation?.cust_id ?? undefined,
    site_address: props.irrigation?.site_address ?? '',
    subdivision: props.irrigation?.subdivision ?? '',
    controller_location: props.irrigation?.controller_location ?? '',
    turn_on: !!props.irrigation?.turn_on,
    turn_on_date: formatDateForInput(props.irrigation?.turn_on_date),
    blowout: !!props.irrigation?.blowout,
    blowout_date: formatDateForInput(props.irrigation?.blowout_date),
    backflow_brand: props.irrigation?.backflow_brand ?? '',
    backflow_model: props.irrigation?.backflow_model ?? '',
    backflow_serial: props.irrigation?.backflow_serial ?? '',
    backflow_type: props.irrigation?.backflow_type ?? '',
    backflow_location: props.irrigation?.backflow_location ?? '',
    backflow_testing: !!props.irrigation?.backflow_testing,
    backflow_test_pass: !!props.irrigation?.backflow_test_pass,
    backflow_test_date: formatDateForInput(props.irrigation?.backflow_test_date),
    pvb_ai: props.irrigation?.pvb_ai ?? '',
    pvb_ai_opened: !!props.irrigation?.pvb_ai_opened,
    pvb_cv: props.irrigation?.pvb_cv ?? '',
    pvb_cv_held: !!props.irrigation?.pvb_cv_held,
    rp_cv1: props.irrigation?.rp_cv1 ?? '',
    rp_cv1_held: !!props.irrigation?.rp_cv1_held,
    rp_cv2: props.irrigation?.rp_cv2 ?? '',
    rp_cv2_held: !!props.irrigation?.rp_cv2_held,
    rp_rv: props.irrigation?.rp_rv ?? '',
    rp_rv_opened: !!props.irrigation?.rp_rv_opened,
    site_id: props.irrigation?.site_id ?? '',
    sequence_order: props.irrigation?.sequence_order ?? undefined,
    irrig_notes: props.irrigation?.irrig_notes ?? '',
})

const submit = () => {
    const url = props.submitUrl ?? (
        props.mode === 'create'
            ? '/irrigations'
            : `/irrigations/${props.irrigation?.id}`
    )

    const method = props.mode === 'create' ? 'post' : 'put'

    form[method](url, {
        preserveScroll: true,
        headers: {
            'X-Timezone': Intl.DateTimeFormat().resolvedOptions().timeZone,
        },
        onSuccess: (page) => {
            const updatedIrrigation = (page.props.flash as any)?.irrigation as Irrigation | undefined
            emit('success', updatedIrrigation || props.irrigation as Irrigation)
        },
        onError: (errors) => {
            emit('error', errors)
        },
    })
}
</script>

<template>
    <form @submit.prevent="submit" class="space-y-6">
        <div v-if="!hideSubmitButton" class="flex justify-end">
            <Button type="submit" size="sm" :disabled="form.processing">
                {{ form.processing ? 'Saving...' : 'Save Changes' }}
            </Button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-4">
                <h3 class="text-sm font-semibold border-b pb-1 uppercase tracking-wider text-muted-foreground">General Info</h3>

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

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="site_id">Site ID</Label>
                        <Input id="site_id" v-model="form.site_id" />
                        <InputError :message="form.errors.site_id" />
                    </div>
                    <div class="space-y-2">
                        <Label for="sequence_order">Sequence Order</Label>
                        <Input id="sequence_order" type="number" v-model="form.sequence_order" />
                        <InputError :message="form.errors.sequence_order" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 border p-3 rounded-md bg-muted/30">
                    <div class="space-y-2">
                        <div class="flex items-center space-x-2">
                            <input
                                id="turn_on"
                                v-model="form.turn_on"
                                type="checkbox"
                                class="h-4 w-4 rounded border-gray-300"
                            />
                            <Label for="turn_on">Turn On</Label>
                        </div>
                        <Input type="date" v-model="form.turn_on_date" :disabled="!form.turn_on" class="mt-2 [&::-webkit-calendar-picker-indicator]:cursor-pointer [&::-webkit-calendar-picker-indicator]:opacity-100 [&::-webkit-calendar-picker-indicator]:filter [&::-webkit-calendar-picker-indicator]:invert [&::-webkit-calendar-picker-indicator]:sepia [&::-webkit-calendar-picker-indicator]:saturate-[500%] [&::-webkit-calendar-picker-indicator]:hue-rotate-[180deg] [&::-webkit-calendar-picker-indicator]:brightness-[0.8]" />
                        <InputError :message="form.errors.turn_on_date" />
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center space-x-2">
                            <input
                                id="backflow_testing_general"
                                v-model="form.backflow_testing"
                                type="checkbox"
                                class="h-4 w-4 rounded border-gray-300"
                            />
                            <Label for="backflow_testing_general">Backflow Testing</Label>
                        </div>
                        <Input type="date" v-model="form.backflow_test_date" :disabled="!form.backflow_testing" class="mt-2 [&::-webkit-calendar-picker-indicator]:cursor-pointer [&::-webkit-calendar-picker-indicator]:opacity-100 [&::-webkit-calendar-picker-indicator]:filter [&::-webkit-calendar-picker-indicator]:invert [&::-webkit-calendar-picker-indicator]:sepia [&::-webkit-calendar-picker-indicator]:saturate-[500%] [&::-webkit-calendar-picker-indicator]:hue-rotate-[180deg] [&::-webkit-calendar-picker-indicator]:brightness-[0.8]" />
                        <InputError :message="form.errors.backflow_test_date" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 border p-3 rounded-md">
                    <div class="space-y-2">
                        <div class="flex items-center space-x-2">
                            <input
                                id="blowout"
                                v-model="form.blowout"
                                type="checkbox"
                                class="h-4 w-4 rounded border-gray-300"
                            />
                            <Label for="blowout">Blowout</Label>
                        </div>
                        <Input type="date" v-model="form.blowout_date" :disabled="!form.blowout" class="mt-2 [&::-webkit-calendar-picker-indicator]:cursor-pointer [&::-webkit-calendar-picker-indicator]:opacity-100 [&::-webkit-calendar-picker-indicator]:filter [&::-webkit-calendar-picker-indicator]:invert [&::-webkit-calendar-picker-indicator]:sepia [&::-webkit-calendar-picker-indicator]:saturate-[500%] [&::-webkit-calendar-picker-indicator]:hue-rotate-[180deg] [&::-webkit-calendar-picker-indicator]:brightness-[0.8]" />
                        <InputError :message="form.errors.blowout_date" />
                    </div>
                    <div class="space-y-2 opacity-0 select-none">
                        <Label>Placeholder</Label>
                    </div>
                </div>

                <div class="space-y-2">
                    <Label for="controller_location">Controller Location</Label>
                    <Input id="controller_location" v-model="form.controller_location" />
                    <InputError :message="form.errors.controller_location" />
                </div>
            </div>

            <div class="space-y-4">
                <h3 class="text-sm font-semibold border-b pb-1 uppercase tracking-wider text-muted-foreground">Backflow Device</h3>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="backflow_brand">Brand</Label>
                        <Input id="backflow_brand" v-model="form.backflow_brand" />
                    </div>
                    <div class="space-y-2">
                        <Label for="backflow_model">Model</Label>
                        <Input id="backflow_model" v-model="form.backflow_model" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="backflow_serial">Serial #</Label>
                        <Input id="backflow_serial" v-model="form.backflow_serial" />
                    </div>
                    <div class="space-y-2">
                        <Label for="backflow_type">Type</Label>
                        <Input id="backflow_type" v-model="form.backflow_type" />
                    </div>
                </div>

                <div class="space-y-2">
                    <Label for="backflow_location">Location</Label>
                    <Input id="backflow_location" v-model="form.backflow_location" />
                </div>

                <div class="space-y-4 border p-3 rounded-md">
                    <div class="flex items-center space-x-2">
                        <input
                            id="backflow_test_pass"
                            v-model="form.backflow_test_pass"
                            type="checkbox"
                            class="h-4 w-4 rounded border-gray-300"
                        />
                        <Label for="backflow_test_pass">Pass</Label>
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-2 border-t">
                        <div class="space-y-4">
                            <h4 class="text-xs font-bold uppercase">PVB</h4>
                            <div class="space-y-2">
                                <Label for="pvb_ai">Air Inlet</Label>
                                <Input id="pvb_ai" v-model="form.pvb_ai" placeholder="Value" />
                                <div class="flex items-center space-x-2">
                                    <input id="pvb_ai_opened" v-model="form.pvb_ai_opened" type="checkbox" class="h-3 w-3 rounded" />
                                    <Label for="pvb_ai_opened" class="text-xs">Opened</Label>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <Label for="pvb_cv">Check Valve</Label>
                                <Input id="pvb_cv" v-model="form.pvb_cv" placeholder="Value" />
                                <div class="flex items-center space-x-2">
                                    <input id="pvb_cv_held" v-model="form.pvb_cv_held" type="checkbox" class="h-3 w-3 rounded" />
                                    <Label for="pvb_cv_held" class="text-xs">Held</Label>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <h4 class="text-xs font-bold uppercase">RP</h4>
                            <div class="space-y-2">
                                <Label for="rp_cv1">CV 1</Label>
                                <Input id="rp_cv1" v-model="form.rp_cv1" placeholder="Value" />
                                <div class="flex items-center space-x-2">
                                    <input id="rp_cv1_held" v-model="form.rp_cv1_held" type="checkbox" class="h-3 w-3 rounded" />
                                    <Label for="rp_cv1_held" class="text-xs">Held</Label>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <Label for="rp_cv2">CV 2</Label>
                                <Input id="rp_cv2" v-model="form.rp_cv2" placeholder="Value" />
                                <div class="flex items-center space-x-2">
                                    <input id="rp_cv2_held" v-model="form.rp_cv2_held" type="checkbox" class="h-3 w-3 rounded" />
                                    <Label for="rp_cv2_held" class="text-xs">Held</Label>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <Label for="rp_rv">RV</Label>
                                <Input id="rp_rv" v-model="form.rp_rv" placeholder="Value" />
                                <div class="flex items-center space-x-2">
                                    <input id="rp_rv_opened" v-model="form.rp_rv_opened" type="checkbox" class="h-3 w-3 rounded" />
                                    <Label for="rp_rv_opened" class="text-xs">Opened</Label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-2">
            <Label for="irrig_notes">Irrigation Notes</Label>
            <textarea
                id="irrig_notes"
                v-model="form.irrig_notes"
                rows="4"
                class="flex min-h-[100px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
            />
            <InputError :message="form.errors.irrig_notes" />
        </div>
    </form>
</template>
