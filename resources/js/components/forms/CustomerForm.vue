<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import type { Customer } from '@/types/models'

type FormMode = 'create' | 'edit'

const props = withDefaults(
    defineProps<{
        customer?: Partial<Customer>
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
    success: [customer: Customer]
    error: [errors: any]
}>()

const form = useForm({
    full_name: props.customer?.full_name ?? '',
    first: props.customer?.first ?? '',
    last: props.customer?.last ?? '',
    company: props.customer?.company ?? '',
    street: props.customer?.street ?? '',
    city: props.customer?.city ?? '',
    state: props.customer?.state ?? '',
    zipcode: props.customer?.zipcode ?? '',
    phone: props.customer?.phone ?? '',
    phone2: props.customer?.phone2 ?? '',
    email: props.customer?.email ?? '',
    email2: props.customer?.email2 ?? '',
    spouse_first: props.customer?.spouse_first ?? '',
    spouse_last: props.customer?.spouse_last ?? '',
    irrigation: props.customer?.irrigation ?? false,
    maintenance: props.customer?.maintenance ?? false,
    notes: props.customer?.notes ?? '', // Textarea also prefers strings
    area_group_id: props.customer?.area_group_id ?? null, // Keep null if this is a Select/ID
    xero_contact_id: props.customer?.xero_contact_id ?? '',
})

const submit = () => {
    // Determine submit URL
    const url = props.submitUrl ?? (
        props.mode === 'create'
            ? '/customers'
            : `/customers/${props.customer?.id}`
    )

    // Determine HTTP method
    const method = props.mode === 'create' ? 'post' : 'put'

    // Submit form
    form[method](url, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: (page) => {
            const updatedCustomer = (page.props.flash as any)?.customer as Customer | undefined
            emit('success', updatedCustomer || props.customer as Customer)
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
            <div class="space-y-2">
                <Label for="full_name">Contact Name</Label>
                <Input
                    id="full_name"
                    v-model="form.full_name"
                    type="text"
                    placeholder="Last name,  First name or Company name"
                />
                <InputError :message="form.errors.full_name" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <Label for="first">First Name</Label>
                    <Input
                        id="first"
                        v-model="form.first"
                        type="text"
                    />
                    <InputError :message="form.errors.first" />
                </div>

                <div class="space-y-2">
                    <Label for="last">Last Name</Label>
                    <Input
                        id="last"
                        v-model="form.last"
                        type="text"
                    />
                    <InputError :message="form.errors.last" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <Label for="spouse_first">Spouse First Name</Label>
                    <Input
                        id="spouse_first"
                        v-model="form.spouse_first"
                        type="text"
                    />
                    <InputError :message="form.errors.spouse_first" />
                </div>

                <div class="space-y-2">
                    <Label for="spouse_last">Spouse Last Name</Label>
                    <Input
                        id="spouse_last"
                        v-model="form.spouse_last"
                        type="text"
                    />
                    <InputError :message="form.errors.spouse_last" />
                </div>
            </div>

            <div class="space-y-2">
                <Label for="company">Company</Label>
                <Input
                    id="company"
                    v-model="form.company"
                    type="text"
                    placeholder="Name of business if applicable"
                    />
                <InputError :message="form.errors.company" />
            </div>

            <div class="space-y-2">
                <Label for="street">Street</Label>
                <Input
                    id="street"
                    v-model="form.street"
                    type="text"
                    />
                <InputError :message="form.errors.street" />
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div class="space-y-2">
                    <Label for="city">City</Label>
                    <Input
                        id="city"
                        v-model="form.city"
                        type="text"
                    />
                    <InputError :message="form.errors.city" />
                </div>

                <div class="space-y-2">
                    <Label for="state">State</Label>
                    <Input
                        id="state"
                        v-model="form.state"
                        type="text"
                    />
                    <InputError :message="form.errors.state" />
                </div>

                <div class="space-y-2">
                    <Label for="zipcode">Zipcode</Label>
                    <Input
                        id="zipcode"
                        v-model="form.zipcode"
                        type="text"
                    />
                    <InputError :message="form.errors.zipcode" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <Label for="phone">Phone</Label>
                    <Input
                        id="phone"
                        v-model="form.phone"
                        type="tel"
                     />
                    <InputError :message="form.errors.phone" />
                </div>

                <div class="space-y-2">
                    <Label for="phone2">Phone 2</Label>
                    <Input
                        id="phone2"
                        v-model="form.phone2"
                        type="tel"
                    />
                    <InputError :message="form.errors.phone2" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <Label for="email">Email</Label>
                    <Input
                        id="email"
                        v-model="form.email"
                        type="email"
                    />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="space-y-2">
                    <Label for="email2">Email 2</Label>
                    <Input
                        id="email2"
                        v-model="form.email2"
                        type="email"
                    />
                    <InputError :message="form.errors.email2" />
                </div>
            </div>

            <div class="flex gap-6">
                <div class="flex items-center space-x-2">
                    <input
                        id="irrigation"
                        v-model="form.irrigation"
                        type="checkbox"
                        class="h-4 w-4 rounded border-gray-300"
                    />
                    <Label for="irrigation" class="!mb-0">Irrigation</Label>
                </div>

                <div class="flex items-center space-x-2">
                    <input
                        id="maintenance"
                        v-model="form.maintenance"
                        type="checkbox"
                        class="h-4 w-4 rounded border-gray-300"
                    />
                    <Label for="maintenance" class="!mb-0">Maintenance</Label>
                </div>
            </div>

            <div class="space-y-2">
                <Label for="notes">Notes</Label>
                <textarea
                    id="notes"
                    v-model="form.notes"
                    class="flex min-h-[100px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    placeholder="Additional notes..."
                />
                <InputError :message="form.errors.notes" />
            </div>
        </div>

        <div v-if="!hideSubmitButton" class="flex justify-end gap-2">
            <Button type="submit" :disabled="form.processing">
                {{ form.processing ? 'Saving...' : mode === 'create' ? 'Create Customer' : 'Save Changes' }}
            </Button>
        </div>
    </form>
</template>
