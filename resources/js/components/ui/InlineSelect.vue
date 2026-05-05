<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

const props = defineProps<{
    modelValue: string | null
    options: { value: string; label: string }[]
    irrigationId: number
    field: 'submitted' | 'billed'
}>()

const emit = defineEmits<{
    'update:modelValue': [value: string | null]
}>()

const localValue = ref(props.modelValue)

watch(() => props.modelValue, (newValue) => {
    localValue.value = newValue
})

const handleChange = (event: Event) => {
    const target = event.target as HTMLSelectElement
    const newValue = target.value || null

    localValue.value = newValue
    emit('update:modelValue', newValue)

    // Send update to backend
    router.patch(
        `/irrigations/${props.irrigationId}/field`,
        {
            field: props.field,
            value: newValue,
        },
        {
            preserveScroll: true,
            preserveState: true,
        }
    )
}
</script>

<template>
    <select
        :value="localValue || ''"
        @change="handleChange"
        class="w-full border border-border rounded-md px-2 py-1 text-sm bg-background hover:bg-accent focus:outline-none focus:ring-2 focus:ring-ring"
        @click.stop
    >
        <option value="">Select...</option>
        <option
            v-for="option in options"
            :key="option.value"
            :value="option.value"
        >
            {{ option.label }}
        </option>
    </select>
</template>
