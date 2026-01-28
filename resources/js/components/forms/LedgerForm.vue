<!--suppress ES6UnusedImports, GrazieInspection -->
<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { computed } from 'vue'

import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import type { Ledger, Customer, Sheet } from '@/types/models'

type FormMode = 'create' | 'edit'

const props = withDefaults(
    defineProps<{
        ledger?: Partial<Ledger>
        mode?: FormMode
        submitUrl?: string
        hideSubmitButton?: boolean
        customers?: Customer[]
        sheets?: Sheet[]
    }>(),
    {
        mode: 'edit',
        hideSubmitButton: false,
        customers: () => [],
        sheets: () => [],
    }
)

const emit = defineEmits<{
    success: [ledger: Ledger]
    error: [errors: any]
}>()

// Helper function to format date for input[type="date"]
const formatDateForInput = (dateString: string | null | undefined): string | undefined => {
    if (!dateString) return undefined
    // Extract just the date part (YYYY-MM-DD) from datetime string
    return dateString.split('T')[0].split(' ')[0]
}

// Helper function to format date for display
const formatDateForDisplay = (dateString: string | null | undefined): string => {
    if (!dateString) return ''
    const date = new Date(dateString)
    return date.toLocaleDateString()
}

const displaySheets = computed(() => {
    if (props.mode === 'create') {
        return props.sheets.slice(0, 5)
    }

    // In edit mode, ensure the currently linked sheet is in the list
    // even if it's not in the most recent ones (though we now provide all sheets)
    return props.sheets
})

const form = useForm({
    cust_id: props.ledger?.cust_id ?? undefined,
    work_date: formatDateForInput(props.ledger?.work_date) ?? undefined,
    sheet_number: props.ledger?.sheet_number ?? undefined,
    times: props.ledger?.times ?? undefined,
    hours: props.ledger?.hours ?? undefined,
    work_type: props.ledger?.work_type ?? 'Site Work',
    work_performed: props.ledger?.work_performed ?? undefined,
    job_notes: props.ledger?.job_notes ?? undefined,
    job_code: props.ledger?.job_code ?? undefined,
    sheet_link: props.ledger?.sheet_link ?? undefined,
    billed: props.ledger?.billed ?? false,
})

const submit = () => {
    // Determine submit URL
    const url = props.submitUrl ?? (
        props.mode === 'create'
            ? '/ledgers'
            : `/ledgers/${props.ledger?.id}`
    )

    // Determine HTTP method
    const method = props.mode === 'create' ? 'post' : 'put'

    // Submit form
    form[method](url, {
        preserveScroll: true,
        preserveState: true,
        headers: {
            'X-Timezone': Intl.DateTimeFormat().resolvedOptions().timeZone,
        },
        onSuccess: (page) => {
            const updatedLedger = (page.props.flash as any)?.ledger as Ledger | undefined
            emit('success', updatedLedger || props.ledger as Ledger)
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

        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <Label for="cust_id">Customer</Label>
                    <select
                        id="cust_id"
                        v-model="form.cust_id"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <option :value="undefined">Select a customer</option>
                        <option
                            v-for="customer in customers"
                            :key="customer.id"
                            :value="customer.id"
                        >
                            {{ customer.full_name }}
                        </option>
                    </select>
                    <InputError :message="form.errors.cust_id" />
                </div>

                <div class="space-y-2">
                    <Label for="work_date">Work Date</Label>
                    <Input
                        id="work_date"
                        v-model="form.work_date!"
                        type="date"
                        class="[&::-webkit-calendar-picker-indicator]:cursor-pointer [&::-webkit-calendar-picker-indicator]:opacity-100 [&::-webkit-calendar-picker-indicator]:filter [&::-webkit-calendar-picker-indicator]:invert [&::-webkit-calendar-picker-indicator]:sepia [&::-webkit-calendar-picker-indicator]:saturate-[500%] [&::-webkit-calendar-picker-indicator]:hue-rotate-[180deg] [&::-webkit-calendar-picker-indicator]:brightness-[0.8]"
                    />
                    <InputError :message="form.errors.work_date" />
                </div>
            </div>

            <div class="space-y-2">
                <Label for="sheet_number">Sheet Number</Label>
                <Input
                    id="sheet_number"
                    v-model="form.sheet_number!"
                    type="text"
                />
                <InputError :message="form.errors.sheet_number" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <Label for="times">Times</Label>
                    <Input
                        id="times"
                        v-model="form.times!"
                        type="text"
                    />
                    <InputError :message="form.errors.times" />
                </div>

                <div class="space-y-2">
                    <Label for="hours">Hours</Label>
                    <Input
                        id="hours"
                        v-model="form.hours!"
                        type="number"
                        step="0.01"
                        placeholder="0.00"
                    />
                    <InputError :message="form.errors.hours" />
                </div>
            </div>

            <div class="space-y-2">
                <Label for="work_type">Work Type</Label>
                <select
                    id="work_type"
                    v-model="form.work_type"
                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    <option value="Site Work">Site Work</option>
                    <option value="Irrigation">Irrigation</option>
                    <option value="Maintenance">Maintenance</option>
                </select>
                <InputError :message="form.errors.work_type" />
            </div>

            <div class="space-y-2">
                <Label for="work_performed">Work Performed</Label>
                <textarea
                    id="work_performed"
                    v-model="form.work_performed"
                    class="flex min-h-[100px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    placeholder="Description of work performed..."
                />
                <InputError :message="form.errors.work_performed" />
            </div>

            <div class="space-y-2">
                <Label for="job_notes">Job Notes</Label>
                <textarea
                    id="job_notes"
                    v-model="form.job_notes"
                    class="flex min-h-[100px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    placeholder="Additional job notes..."
                />
                <InputError :message="form.errors.job_notes" />
            </div>

            <div class="space-y-2">
                <Label for="job_code">Job Code</Label>
                <Input
                    id="job_code"
                    v-model="form.job_code"
                    type="text"
                />
                <InputError :message="form.errors.job_code" />
            </div>

            <div class="space-y-2">
                <Label for="sheet_link">Sheet</Label>
                <select
                    id="sheet_link"
                    v-model="form.sheet_link"
                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    <option :value="undefined">Select a sheet</option>
                    <option
                        v-for="sheet in displaySheets"
                        :key="sheet.id"
                        :value="sheet.id"
                    >
                        {{ formatDateForDisplay(sheet.begin_date) }} - {{ formatDateForDisplay(sheet.end_date) }}
                    </option>
                </select>
                <InputError :message="form.errors.sheet_link" />
            </div>

            <div class="flex items-center space-x-2">
                <input
                    id="billed"
                    v-model="form.billed"
                    type="checkbox"
                    class="h-4 w-4 rounded border-gray-300"
                />
                <Label for="billed" class="!mb-0">Billed</Label>
            </div>
        </div>
    </form>
</template>
