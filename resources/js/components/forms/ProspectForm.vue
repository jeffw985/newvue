<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import type { Prospect } from '@/types/models'

type FormMode = 'create' | 'edit'

const props = withDefaults(
    defineProps<{
        prospect?: Partial<Prospect>
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
    success: [prospect: Prospect]
    error: [errors: any]
}>()

// Helper function to format date for input[type="date"]
const formatDateForInput = (dateString: string | null | undefined): string | undefined => {
    if (!dateString) return undefined
    return dateString.split('T')[0].split(' ')[0]
}

const form = useForm({
    name: props.prospect?.name ?? '',
    date: formatDateForInput(props.prospect?.date) ?? '',
    email: props.prospect?.email ?? '',
    phone: props.prospect?.phone ?? '',
    street: props.prospect?.street ?? '',
    city: props.prospect?.city ?? '',
    state: props.prospect?.state ?? '',
    zip: props.prospect?.zip ?? '',
    work_description: props.prospect?.work_description ?? '',
    confirmation: formatDateForInput(props.prospect?.confirmation) ?? '',
})

const submit = () => {
    const url = props.submitUrl ?? (
        props.mode === 'create'
            ? '/prospects'
            : `/prospects/${props.prospect?.id}`
    )

    const method = props.mode === 'create' ? 'post' : 'put'

    form[method](url, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            emit('success', props.prospect as Prospect)
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

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-2">
                <Label for="name">Name</Label>
                <Input id="name" v-model="form.name" placeholder="Prospect Name" />
                <InputError :message="form.errors.name" />
            </div>

            <div class="space-y-2">
                <Label for="date">Date</Label>
                <Input id="date" type="date" v-model="form.date" />
                <InputError :message="form.errors.date" />
            </div>

            <div class="space-y-2">
                <Label for="email">Email</Label>
                <Input id="email" type="email" v-model="form.email" placeholder="email@example.com" />
                <InputError :message="form.errors.email" />
            </div>

            <div class="space-y-2">
                <Label for="phone">Phone</Label>
                <Input id="phone" v-model="form.phone" placeholder="Phone Number" />
                <InputError :message="form.errors.phone" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:col-span-2">
                <div class="space-y-2 md:col-span-1">
                    <Label for="street">Street</Label>
                    <Input id="street" v-model="form.street" placeholder="Street Address" />
                    <InputError :message="form.errors.street" />
                </div>
                <div class="space-y-2">
                    <Label for="city">City</Label>
                    <Input id="city" v-model="form.city" placeholder="City" />
                    <InputError :message="form.errors.city" />
                </div>
                <div class="flex gap-2">
                    <div class="space-y-2 flex-1">
                        <Label for="state">State</Label>
                        <Input id="state" v-model="form.state" placeholder="State" maxlength="2" />
                        <InputError :message="form.errors.state" />
                    </div>
                    <div class="space-y-2 flex-1">
                        <Label for="zip">Zip</Label>
                        <Input id="zip" v-model="form.zip" placeholder="Zip" />
                        <InputError :message="form.errors.zip" />
                    </div>
                </div>
            </div>

            <div class="space-y-2 md:col-span-2">
                <Label for="work_description">Work Description</Label>
                <textarea
                    id="work_description"
                    v-model="form.work_description"
                    class="flex min-h-[100px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    placeholder="Describe the work..."
                    rows="3"
                />
                <InputError :message="form.errors.work_description" />
            </div>
        </div>

    </form>
</template>
