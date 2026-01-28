<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import type { Sheet } from '@/types/models'

type FormMode = 'create' | 'edit'

const props = withDefaults(
    defineProps<{
        sheet?: Partial<Sheet>
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
    success: [sheet: Sheet]
    error: [errors: any]
}>()

// Helper function to format date for input[type="date"]
const formatDateForInput = (dateString: string | null | undefined): string => {
    if (!dateString) return ''
    // Extract just the date part (YYYY-MM-DD) from datetime string
    return dateString.split('T')[0].split(' ')[0]
}

const form = useForm({
    begin_date: formatDateForInput(props.sheet?.begin_date),
    end_date: formatDateForInput(props.sheet?.end_date),
    sheet_link: props.sheet?.sheet_link ?? '',
})

const submit = () => {
    // Determine submit URL
    const url = props.submitUrl ?? (
        props.mode === 'create'
            ? '/sheets'
            : `/sheets/${props.sheet?.id}`
    )

    // Determine HTTP method
    const method = props.mode === 'create' ? 'post' : 'put'

    // Submit form
    form[method](url, {
        preserveScroll: true,
        headers: {
            'X-Timezone': Intl.DateTimeFormat().resolvedOptions().timeZone,
        },
        onSuccess: (page) => {
            emit('success', page.props.sheet as Sheet)
        },
        onError: (errors) => {
            emit('error', errors)
        },
    })
}
</script>

<template>
    <form @submit.prevent="submit" class="space-y-6">
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <Label for="begin_date">Begin Date</Label>
                    <Input
                        id="begin_date"
                        v-model="form.begin_date"
                        type="date"
                        class="[&::-webkit-calendar-picker-indicator]:cursor-pointer [&::-webkit-calendar-picker-indicator]:opacity-100 [&::-webkit-calendar-picker-indicator]:filter [&::-webkit-calendar-picker-indicator]:invert [&::-webkit-calendar-picker-indicator]:sepia [&::-webkit-calendar-picker-indicator]:saturate-[500%] [&::-webkit-calendar-picker-indicator]:hue-rotate-[180deg] [&::-webkit-calendar-picker-indicator]:brightness-[0.8]"
                    />
                    <InputError :message="form.errors.begin_date" />
                </div>

                <div class="space-y-2">
                    <Label for="end_date">End Date</Label>
                    <Input
                        id="end_date"
                        v-model="form.end_date"
                        type="date"
                        class="[&::-webkit-calendar-picker-indicator]:cursor-pointer [&::-webkit-calendar-picker-indicator]:opacity-100 [&::-webkit-calendar-picker-indicator]:filter [&::-webkit-calendar-picker-indicator]:invert [&::-webkit-calendar-picker-indicator]:sepia [&::-webkit-calendar-picker-indicator]:saturate-[500%] [&::-webkit-calendar-picker-indicator]:hue-rotate-[180deg] [&::-webkit-calendar-picker-indicator]:brightness-[0.8]"
                    />
                    <InputError :message="form.errors.end_date" />
                </div>
            </div>

            <div class="space-y-2">
                <Label for="sheet_link">Sheet Link</Label>
                <Input
                    id="sheet_link"
                    v-model="form.sheet_link"
                    type="text"
                />
                <InputError :message="form.errors.sheet_link" />
            </div>
        </div>

        <div v-if="!hideSubmitButton" class="flex justify-end gap-2">
            <Button type="submit" :disabled="form.processing">
                {{ form.processing ? 'Saving...' : mode === 'create' ? 'Create Sheet' : 'Save Changes' }}
            </Button>
        </div>
    </form>
</template>
