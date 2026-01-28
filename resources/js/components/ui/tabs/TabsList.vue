<script setup lang="ts">
import type { TabsListProps } from 'reka-ui'
import type { HTMLAttributes } from 'vue'
import { TabsList, useForwardProps } from 'reka-ui'
import { cn } from '@/lib/utils'

const props = defineProps<
    TabsListProps & {
        class?: HTMLAttributes['class']
    }
>()

const delegatedProps = computed(() => {
    const { class: _, ...delegated } = props

    return delegated
})

const forwardedProps = useForwardProps(delegatedProps)
</script>

<template>
    <TabsList
        v-bind="forwardedProps"
        :class="
            cn(
                'inline-flex h-10 items-center justify-center rounded-md bg-muted p-1 text-muted-foreground',
                props.class,
            )
        "
    >
        <slot />
    </TabsList>
</template>
