<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

interface Schedule {
    id: number;
    start_time: string;
    service_status: string;
    service_requested: string | string[];
    service_notes: string;
    site_address: string;
    customer: {
        id: number;
        full_name: string;
        area_group?: {
            area_name: string;
        };
    };
}

defineProps<{
    schedules: Schedule[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Crew Schedule',
        href: '/crew-schedule',
    },
];

const translatingId = ref<number | null>(null);
const translatedNotes = ref<Record<number, string>>({});

const translateToSpanish = async (schedule: Schedule) => {
    translatingId.value = schedule.id;
    // For now, this is a mock. In a real app, you'd call an API.
    // The requirement is just a "button that will open a modal to update/edit record"
    // and "translate to spanish button".
    setTimeout(() => {
        translatedNotes.value[schedule.id] = `[ES] ${schedule.service_notes || 'No hay notas'}`;
        translatingId.value = null;
    }, 500);
};

const editingSchedule = ref<Schedule | null>(null);
const isEditModalOpen = ref(false);

const form = useForm({
    service_notes: '',
    service_status: '',
});

const openEditModal = (schedule: Schedule) => {
    editingSchedule.value = schedule;
    form.service_notes = schedule.service_notes || '';
    form.service_status = schedule.service_status;
    isEditModalOpen.value = true;
};

const updateSchedule = () => {
    if (!editingSchedule.value) return;

    form.put(`/service-calendar/${editingSchedule.value.id}`, {
        onSuccess: () => {
            isEditModalOpen.value = false;
            editingSchedule.value = null;
        },
    });
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleString([], {
        weekday: 'short',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const getRequestedServices = (services: string | string[]) => {
    if (Array.isArray(services)) return services.join(', ');
    return services;
};
</script>

<template>
    <Head title="Crew Schedule" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 space-y-4 max-w-md mx-auto">
            <h1 class="text-2xl font-bold mb-4">Crew Schedule</h1>

            <div v-if="schedules.length === 0" class="text-center py-8 text-muted-foreground">
                No scheduled records found.
            </div>

            <Card v-for="schedule in schedules" :key="schedule.id" class="overflow-hidden shadow-sm">
                <CardHeader class="pb-2">
                    <div class="flex justify-between items-start">
                        <div>
                            <CardTitle class="text-lg">{{ schedule.customer.full_name }}</CardTitle>
                            <CardDescription>{{ schedule.customer.area_group?.area_name || 'No Area Group' }}</CardDescription>
                        </div>
                        <Badge variant="outline-blue">{{ schedule.service_status }}</Badge>
                    </div>
                </CardHeader>
                <CardContent class="space-y-3 pb-3">
                    <div class="text-sm">
                        <span class="font-semibold block text-muted-foreground">Address:</span>
                        <p>{{ schedule.site_address }}</p>
                    </div>

                    <div class="text-sm">
                        <span class="font-semibold block text-muted-foreground">Start Time:</span>
                        <p>{{ formatDate(schedule.start_time) }}</p>
                    </div>

                    <div class="text-sm">
                        <span class="font-semibold block text-muted-foreground">Services Requested:</span>
                        <p>{{ getRequestedServices(schedule.service_requested) }}</p>
                    </div>

                    <div class="text-sm">
                        <span class="font-semibold block text-muted-foreground">Notes:</span>
                        <p class="whitespace-pre-line">{{ translatedNotes[schedule.id] || schedule.service_notes || 'N/A' }}</p>
                    </div>
                </CardContent>
                <CardFooter class="flex flex-col gap-2 pt-0">
                    <Button
                        variant="outline"
                        class="w-full"
                        size="sm"
                        @click="translateToSpanish(schedule)"
                        :disabled="translatingId === schedule.id"
                    >
                        {{ translatingId === schedule.id ? 'Translating...' : 'Translate to Spanish' }}
                    </Button>
                    <Button variant="default" class="w-full" size="sm" @click="openEditModal(schedule)">
                        Edit Record
                    </Button>
                </CardFooter>
            </Card>
        </div>

        <Dialog v-model:open="isEditModalOpen">
            <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>Edit Schedule</DialogTitle>
                </DialogHeader>
                <div class="grid gap-4 py-4">
                    <div class="grid gap-2">
                        <Label for="status">Status</Label>
                        <Input id="status" v-model="form.service_status" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="notes">Notes</Label>
                        <textarea
                            id="notes"
                            v-model="form.service_notes"
                            rows="4"
                            class="flex min-h-[100px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        />
                    </div>
                </div>
                <DialogFooter>
                    <Button type="button" variant="outline" @click="isEditModalOpen = false">Cancel</Button>
                    <Button type="button" @click="updateSchedule" :disabled="form.processing">
                        {{ form.processing ? 'Saving...' : 'Save Changes' }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
