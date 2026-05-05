<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import { Check, X } from 'lucide-vue-next'
import { ref, watch } from 'vue'

const props = defineProps<{
    modelValue: boolean | null
    irrigationId: number
    field: string
}>()

const emit = defineEmits<{
    'update:modelValue': [value: boolean]
}>()

const localValue = ref(props.modelValue ?? false)

watch(() => props.modelValue, (newValue) => {
    localValue.value = newValue ?? false
})

const handleToggle = () => {
    const newValue = !localValue.value

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
    <button
        @click.stop="handleToggle"
        :class="[
            'inline-flex items-center justify-center rounded-md p-1 transition-colors hover:bg-accent focus:outline-none focus:ring-2 focus:ring-ring',
            localValue ? 'text-green-600 hover:text-green-700' : 'text-red-600 hover:text-red-700'
        ]"
        :aria-label="localValue ? 'Mark as incomplete' : 'Mark as complete'"
    >
        <Check v-if="localValue" class="h-5 w-5" />
        <X v-else class="h-5 w-5" />
    </button>
</template>
