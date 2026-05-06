<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { X } from 'lucide-vue-next'

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
        onCancel?: () => void
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
    backflow_test_pass: props.irrigation?.backflow_test_pass ?? '',
    backflow_test_date: formatDateForInput(props.irrigation?.backflow_test_date),
    pvb_ai: props.irrigation?.pvb_ai ?? '',
    pvb_ai_opened: props.irrigation?.pvb_ai_opened ?? null,
    pvb_cv: props.irrigation?.pvb_cv ?? '',
    pvb_cv_held: props.irrigation?.pvb_cv_held ?? null,
    rp_cv1: props.irrigation?.rp_cv1 ?? '',
    rp_cv1_held: props.irrigation?.rp_cv1_held ?? null,
    rp_cv2: props.irrigation?.rp_cv2 ?? '',
    rp_cv2_held: props.irrigation?.rp_cv2_held ?? null,
    rp_rv: props.irrigation?.rp_rv ?? '',
    rp_rv_opened: props.irrigation?.rp_rv_opened ?? null,
    site_id: props.irrigation?.site_id ?? '',
    sequence_order: props.irrigation?.sequence_order ?? undefined,
    irrig_notes: props.irrigation?.irrig_notes ?? '',
    required_by: formatDateForInput(props.irrigation?.required_by),
    required_reason: props.irrigation?.required_reason ?? '',
    submitted: props.irrigation?.submitted ?? '',
    billed: props.irrigation?.billed ?? '',
    clear_list: !!props.irrigation?.clear_list,
})

const submit = () => {
    const url = props.submitUrl ?? (
        props.mode === 'create'
            ? '/irrigations'
            : `/irrigations/${props.irrigation?.id}`
    )

    const method = props.mode === 'create' ? 'post' : 'put'

    // Transform empty strings to null for proper validation
    const transformedData = {
        ...form.data(),
        backflow_test_pass: form.backflow_test_pass === '' ? null : form.backflow_test_pass,
        pvb_ai_opened: form.pvb_ai_opened === '' ? null : form.pvb_ai_opened,
        rp_rv_opened: form.rp_rv_opened === '' ? null : form.rp_rv_opened,
        submitted: form.submitted === '' ? null : form.submitted,
        billed: form.billed === '' ? null : form.billed,
    }

    form.transform(() => transformedData)[method](url, {
        preserveScroll: true,
        headers: {
            'X-Timezone': Intl.DateTimeFormat().resolvedOptions().timeZone,
        },
        onSuccess: (page) => {
            const updatedIrrigation = (page.props.flash as any)?.irrigation as Irrigation | undefined
            emit('success', updatedIrrigation || props.irrigation as Irrigation)
        },
        onError: (errors) => {
            console.error('Irrigation form validation errors:', errors)
            console.log('Form data being submitted:', transformedData)
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
                        <div class="flex gap-2">
                            <button
                                type="button"
                                @click="form.backflow_type = ''"
                                :class="[
                                    'flex-1 px-3 py-2 text-sm rounded border transition-all',
                                    form.backflow_type === '' || form.backflow_type === null
                                        ? 'bg-muted border-muted-foreground/30 font-medium'
                                        : 'border-input hover:bg-muted/50'
                                ]"
                            >
                                None
                            </button>
                            <button
                                type="button"
                                @click="form.backflow_type = 'PVB'"
                                :class="[
                                    'flex-1 px-3 py-2 text-sm rounded border transition-all',
                                    form.backflow_type === 'PVB'
                                        ? 'bg-blue-50 border-blue-600 text-blue-700 font-medium'
                                        : 'border-input hover:bg-muted/50'
                                ]"
                            >
                                PVB
                            </button>
                            <button
                                type="button"
                                @click="form.backflow_type = 'RP'"
                                :class="[
                                    'flex-1 px-3 py-2 text-sm rounded border transition-all',
                                    form.backflow_type === 'RP'
                                        ? 'bg-orange-50 border-orange-600 text-orange-700 font-medium'
                                        : 'border-input hover:bg-muted/50'
                                ]"
                            >
                                RP
                            </button>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <Label for="backflow_location">Location</Label>
                    <Input id="backflow_location" v-model="form.backflow_location" />
                </div>

                <div class="space-y-4 border p-3 rounded-md">
                    <div class="space-y-2">
                        <Label for="backflow_test_pass">Test Result</Label>
                        <div class="flex gap-2">
                            <button
                                type="button"
                                @click="form.backflow_test_pass = ''"
                                :class="[
                                    'flex-1 px-3 py-2 text-sm rounded border transition-all',
                                    form.backflow_test_pass === '' || form.backflow_test_pass === null
                                        ? 'bg-muted border-muted-foreground/30 font-medium'
                                        : 'border-input hover:bg-muted/50'
                                ]"
                            >
                                Not Tested
                            </button>
                            <button
                                type="button"
                                @click="form.backflow_test_pass = 'Pass'"
                                :class="[
                                    'flex-1 px-3 py-2 text-sm rounded border-2 transition-all',
                                    form.backflow_test_pass === 'Pass'
                                        ? 'border-green-600 text-green-700 font-medium'
                                        : 'border-input hover:bg-muted/50'
                                ]"
                            >
                                Pass
                            </button>
                            <button
                                type="button"
                                @click="form.backflow_test_pass = 'Fail'"
                                :class="[
                                    'flex-1 px-3 py-2 text-sm rounded border-2 transition-all',
                                    form.backflow_test_pass === 'Fail'
                                        ? 'border-red-600 text-red-700 font-medium'
                                        : 'border-input hover:bg-muted/50'
                                ]"
                            >
                                Fail
                            </button>
                        </div>
                        <InputError :message="form.errors.backflow_test_pass" />
                    </div>

                    <div v-if="form.backflow_type === 'PVB' || form.backflow_type === 'RP'" class="grid grid-cols-2 gap-4 pt-2 border-t">
                        <div v-if="form.backflow_type === 'PVB'" class="space-y-4">
                            <h4 class="text-xs font-bold uppercase">PVB</h4>
                            <div class="space-y-2">
                                <Label for="pvb_ai">Air Inlet</Label>
                                <Input id="pvb_ai" v-model="form.pvb_ai" placeholder="Value" />
                                <div class="flex gap-1 mt-2">
                                    <button
                                        type="button"
                                        @click="form.pvb_ai_opened = null"
                                        :class="[
                                            'flex-1 px-2 py-1.5 text-xs rounded border transition-all',
                                            form.pvb_ai_opened === null
                                                ? 'bg-muted border-muted-foreground/30 font-medium'
                                                : 'border-input hover:bg-muted/50'
                                        ]"
                                    >
                                        Pending
                                    </button>
                                    <button
                                        type="button"
                                        @click="form.pvb_ai_opened = 'Opened'"
                                        :class="[
                                            'flex-1 px-2 py-1.5 text-xs rounded border transition-all',
                                            form.pvb_ai_opened === 'Opened'
                                                ? 'bg-green-50 border-green-600 text-green-700 font-medium'
                                                : 'border-input hover:bg-muted/50'
                                        ]"
                                    >
                                        Opened
                                    </button>
                                    <button
                                        type="button"
                                        @click="form.pvb_ai_opened = 'Did Not Open'"
                                        :class="[
                                            'flex-1 px-2 py-1.5 text-xs rounded border transition-all',
                                            form.pvb_ai_opened === 'Did Not Open'
                                                ? 'bg-red-50 border-red-600 text-red-700 font-medium'
                                                : 'border-input hover:bg-muted/50'
                                        ]"
                                    >
                                        Not Opened
                                    </button>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <Label for="pvb_cv">Check Valve</Label>
                                <Input id="pvb_cv" v-model="form.pvb_cv" placeholder="Value" />
                                <div class="flex gap-1 mt-2">
                                    <button
                                        type="button"
                                        @click="form.pvb_cv_held = null"
                                        :class="[
                                            'flex-1 px-2 py-1.5 text-xs rounded border transition-all',
                                            form.pvb_cv_held === null
                                                ? 'bg-muted border-muted-foreground/30 font-medium'
                                                : 'border-input hover:bg-muted/50'
                                        ]"
                                    >
                                        Pending
                                    </button>
                                    <button
                                        type="button"
                                        @click="form.pvb_cv_held = 'Held'"
                                        :class="[
                                            'flex-1 px-2 py-1.5 text-xs rounded border transition-all',
                                            form.pvb_cv_held === 'Held'
                                                ? 'bg-green-50 border-green-600 text-green-700 font-medium'
                                                : 'border-input hover:bg-muted/50'
                                        ]"
                                    >
                                        Held
                                    </button>
                                    <button
                                        type="button"
                                        @click="form.pvb_cv_held = 'Not Held'"
                                        :class="[
                                            'flex-1 px-2 py-1.5 text-xs rounded border transition-all',
                                            form.pvb_cv_held === 'Not Held'
                                                ? 'bg-red-50 border-red-600 text-red-700 font-medium'
                                                : 'border-input hover:bg-muted/50'
                                        ]"
                                    >
                                        Not Held
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div v-if="form.backflow_type === 'RP'" class="space-y-4">
                            <h4 class="text-xs font-bold uppercase">RP</h4>
                            <div class="space-y-2">
                                <Label for="rp_cv1">CV 1</Label>
                                <Input id="rp_cv1" v-model="form.rp_cv1" placeholder="Value" />
                                <div class="flex gap-1 mt-2">
                                    <button
                                        type="button"
                                        @click="form.rp_cv1_held = null"
                                        :class="[
                                            'flex-1 px-2 py-1.5 text-xs rounded border transition-all',
                                            form.rp_cv1_held === null
                                                ? 'bg-muted border-muted-foreground/30 font-medium'
                                                : 'border-input hover:bg-muted/50'
                                        ]"
                                    >
                                        Pending
                                    </button>
                                    <button
                                        type="button"
                                        @click="form.rp_cv1_held = 'Held'"
                                        :class="[
                                            'flex-1 px-2 py-1.5 text-xs rounded border transition-all',
                                            form.rp_cv1_held === 'Held'
                                                ? 'bg-green-50 border-green-600 text-green-700 font-medium'
                                                : 'border-input hover:bg-muted/50'
                                        ]"
                                    >
                                        Held
                                    </button>
                                    <button
                                        type="button"
                                        @click="form.rp_cv1_held = 'Not Held'"
                                        :class="[
                                            'flex-1 px-2 py-1.5 text-xs rounded border transition-all',
                                            form.rp_cv1_held === 'Not Held'
                                                ? 'bg-red-50 border-red-600 text-red-700 font-medium'
                                                : 'border-input hover:bg-muted/50'
                                        ]"
                                    >
                                        Not Held
                                    </button>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <Label for="rp_cv2">CV 2</Label>
                                <Input id="rp_cv2" v-model="form.rp_cv2" placeholder="Value" />
                                <div class="flex gap-1 mt-2">
                                    <button
                                        type="button"
                                        @click="form.rp_cv2_held = null"
                                        :class="[
                                            'flex-1 px-2 py-1.5 text-xs rounded border transition-all',
                                            form.rp_cv2_held === null
                                                ? 'bg-muted border-muted-foreground/30 font-medium'
                                                : 'border-input hover:bg-muted/50'
                                        ]"
                                    >
                                        Pending
                                    </button>
                                    <button
                                        type="button"
                                        @click="form.rp_cv2_held = 'Held'"
                                        :class="[
                                            'flex-1 px-2 py-1.5 text-xs rounded border transition-all',
                                            form.rp_cv2_held === 'Held'
                                                ? 'bg-green-50 border-green-600 text-green-700 font-medium'
                                                : 'border-input hover:bg-muted/50'
                                        ]"
                                    >
                                        Held
                                    </button>
                                    <button
                                        type="button"
                                        @click="form.rp_cv2_held = 'Not Held'"
                                        :class="[
                                            'flex-1 px-2 py-1.5 text-xs rounded border transition-all',
                                            form.rp_cv2_held === 'Not Held'
                                                ? 'bg-red-50 border-red-600 text-red-700 font-medium'
                                                : 'border-input hover:bg-muted/50'
                                        ]"
                                    >
                                        Not Held
                                    </button>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <Label for="rp_rv">RV</Label>
                                <Input id="rp_rv" v-model="form.rp_rv" placeholder="Value" />
                                <div class="flex gap-1 mt-2">
                                    <button
                                        type="button"
                                        @click="form.rp_rv_opened = null"
                                        :class="[
                                            'flex-1 px-2 py-1.5 text-xs rounded border transition-all',
                                            form.rp_rv_opened === null
                                                ? 'bg-muted border-muted-foreground/30 font-medium'
                                                : 'border-input hover:bg-muted/50'
                                        ]"
                                    >
                                        Pending
                                    </button>
                                    <button
                                        type="button"
                                        @click="form.rp_rv_opened = 'Opened'"
                                        :class="[
                                            'flex-1 px-2 py-1.5 text-xs rounded border transition-all',
                                            form.rp_rv_opened === 'Opened'
                                                ? 'bg-green-50 border-green-600 text-green-700 font-medium'
                                                : 'border-input hover:bg-muted/50'
                                        ]"
                                    >
                                        Opened
                                    </button>
                                    <button
                                        type="button"
                                        @click="form.rp_rv_opened = 'Did Not Open'"
                                        :class="[
                                            'flex-1 px-2 py-1.5 text-xs rounded border transition-all',
                                            form.rp_rv_opened === 'Did Not Open'
                                                ? 'bg-red-50 border-red-600 text-red-700 font-medium'
                                                : 'border-input hover:bg-muted/50'
                                        ]"
                                    >
                                        Not Opened
                                    </button>
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

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-4">
                <h3 class="text-sm font-semibold border-b pb-1 uppercase tracking-wider text-muted-foreground">Scheduling</h3>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="required_by">Required By</Label>
                        <Input id="required_by" type="date" v-model="form.required_by" class="[&::-webkit-calendar-picker-indicator]:cursor-pointer [&::-webkit-calendar-picker-indicator]:opacity-100 [&::-webkit-calendar-picker-indicator]:filter [&::-webkit-calendar-picker-indicator]:invert [&::-webkit-calendar-picker-indicator]:sepia [&::-webkit-calendar-picker-indicator]:saturate-[500%] [&::-webkit-calendar-picker-indicator]:hue-rotate-[180deg] [&::-webkit-calendar-picker-indicator]:brightness-[0.8]" />
                        <InputError :message="form.errors.required_by" />
                    </div>
                </div>

                <div class="space-y-2">
                    <Label for="required_reason">Required Reason</Label>
                    <Input id="required_reason" v-model="form.required_reason" />
                    <InputError :message="form.errors.required_reason" />
                </div>

                <div class="space-y-4 border p-3 rounded-md">
                    <div class="flex items-center space-x-2">
                        <input
                            id="clear_list"
                            v-model="form.clear_list"
                            type="checkbox"
                            class="h-4 w-4 rounded border-gray-300"
                        />
                        <Label for="clear_list">Clear List</Label>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <h3 class="text-sm font-semibold border-b pb-1 uppercase tracking-wider text-muted-foreground">Billing</h3>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="submitted">Submitted</Label>
                        <Input id="submitted" v-model="form.submitted" />
                        <InputError :message="form.errors.submitted" />
                    </div>
                    <div class="space-y-2">
                        <Label for="billed">Billed</Label>
                        <Input id="billed" v-model="form.billed" />
                        <InputError :message="form.errors.billed" />
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>
