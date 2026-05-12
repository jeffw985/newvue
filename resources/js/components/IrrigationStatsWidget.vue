<script setup lang="ts">
import { computed } from 'vue'

interface ServiceStats {
    total: number
    completed: number
}

interface IrrigationStats {
    turnOn: ServiceStats
    backflow: ServiceStats
    blowout: ServiceStats
}

interface Props {
    stats: IrrigationStats
}

const props = defineProps<Props>()

const getPercentage = (service: ServiceStats) => {
    if (service.total === 0) return 0
    return Math.round((service.completed / service.total) * 100)
}

const services = computed(() => [
    {
        name: 'Turn On',
        stats: props.stats.turnOn,
        percentage: getPercentage(props.stats.turnOn),
    },
    {
        name: 'Backflow Testing',
        stats: props.stats.backflow,
        percentage: getPercentage(props.stats.backflow),
    },
    {
        name: 'Blowout',
        stats: props.stats.blowout,
        percentage: getPercentage(props.stats.blowout),
    },
])
</script>

<template>
    <div class="flex h-full flex-col justify-between p-6">
        <div class="space-y-1">
            <h3 class="text-sm font-medium text-muted-foreground">
                Irrigation Services
            </h3>
        </div>

        <div class="space-y-4">
            <div v-for="service in services" :key="service.name" class="space-y-1">
                <div class="flex items-center justify-between text-sm">
                    <span class="font-medium">{{ service.name }}</span>
                    <span class="text-muted-foreground">
                        {{ service.stats.completed }} / {{ service.stats.total }}
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="h-2 flex-1 overflow-hidden rounded-full bg-secondary">
                        <div
                            class="h-full bg-primary transition-all duration-300"
                            :style="{ width: `${service.percentage}%` }"
                        />
                    </div>
                    <span class="text-xs font-medium text-muted-foreground w-10 text-right">
                        {{ service.percentage }}%
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>
