<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Pencil, Plus, Save, Trash2, X } from 'lucide-vue-next';
import { ref } from 'vue';

import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Table,
    TableBody,
    TableCell,
    TableEmpty,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import type { AreaGroup } from '@/types/models';

// eslint-disable-next-line @typescript-eslint/no-unused-vars
const props = defineProps<{
    areaGroups: AreaGroup[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Area Groups',
        href: '/area-groups',
    },
];

const editingId = ref<number | null>(null);
const isAddingNew = ref(false);

const editForm = useForm({
    area_name: '',
});

const newForm = useForm({
    area_name: '',
});

const startEdit = (areaGroup: AreaGroup) => {
    editingId.value = areaGroup.id;
    editForm.area_name = areaGroup.area_name;
};

const cancelEdit = () => {
    editingId.value = null;
    editForm.reset();
};

const saveEdit = (areaGroup: AreaGroup) => {
    editForm.put(`/area-groups/${areaGroup.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            editingId.value = null;
            editForm.reset();
        },
    });
};

const startAddNew = () => {
    isAddingNew.value = true;
    newForm.reset();
};

const cancelAddNew = () => {
    isAddingNew.value = false;
    newForm.reset();
};

const saveNew = () => {
    newForm.post('/area-groups', {
        preserveScroll: true,
        onSuccess: () => {
            isAddingNew.value = false;
            newForm.reset();
        },
    });
};

const deleteAreaGroup = (areaGroup: AreaGroup) => {
    if (confirm(`Are you sure you want to delete "${areaGroup.area_name}"?`)) {
        router.delete(`/area-groups/${areaGroup.id}`, {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Area Groups" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Area Groups</h1>
                <Button @click="startAddNew" :disabled="isAddingNew">
                    <Plus class="mr-2 h-4 w-4" />
                    Add Area Group
                </Button>
            </div>

            <div class="rounded-md border bg-card">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Area Name</TableHead>
                            <TableHead class="w-[150px] text-right"
                                >Actions</TableHead
                            >
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <!-- Add New Row -->
                        <TableRow v-if="isAddingNew" class="bg-muted/50">
                            <TableCell>
                                <Input
                                    v-model="newForm.area_name"
                                    placeholder="Enter area name"
                                    @keyup.enter="saveNew"
                                    @keyup.esc="cancelAddNew"
                                    autofocus
                                />
                            </TableCell>
                            <TableCell class="text-right">
                                <div class="flex justify-end gap-2">
                                    <Button
                                        @click="saveNew"
                                        size="sm"
                                        :disabled="
                                            newForm.processing ||
                                            !newForm.area_name
                                        "
                                    >
                                        <Save class="h-4 w-4" />
                                    </Button>
                                    <Button
                                        @click="cancelAddNew"
                                        size="sm"
                                        variant="ghost"
                                        :disabled="newForm.processing"
                                    >
                                        <X class="h-4 w-4" />
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>

                        <TableEmpty
                            v-if="!areaGroups.length && !isAddingNew"
                            :colspan="2"
                        >
                            No area groups found.
                        </TableEmpty>

                        <!-- Existing Rows -->
                        <TableRow
                            v-for="areaGroup in areaGroups"
                            :key="areaGroup.id"
                        >
                            <TableCell>
                                <template v-if="editingId === areaGroup.id">
                                    <Input
                                        v-model="editForm.area_name"
                                        @keyup.enter="saveEdit(areaGroup)"
                                        @keyup.esc="cancelEdit"
                                        autofocus
                                    />
                                </template>
                                <template v-else>
                                    {{ areaGroup.area_name }}
                                </template>
                            </TableCell>
                            <TableCell class="text-right">
                                <template v-if="editingId === areaGroup.id">
                                    <div class="flex justify-end gap-2">
                                        <Button
                                            @click="saveEdit(areaGroup)"
                                            size="sm"
                                            :disabled="
                                                editForm.processing ||
                                                !editForm.area_name
                                            "
                                        >
                                            <Save class="h-4 w-4" />
                                        </Button>
                                        <Button
                                            @click="cancelEdit"
                                            size="sm"
                                            variant="ghost"
                                            :disabled="editForm.processing"
                                        >
                                            <X class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </template>
                                <template v-else>
                                    <div class="flex justify-end gap-2">
                                        <Button
                                            @click="startEdit(areaGroup)"
                                            size="sm"
                                            variant="ghost"
                                            :disabled="isAddingNew"
                                        >
                                            <Pencil class="h-4 w-4" />
                                        </Button>
                                        <Button
                                            @click="deleteAreaGroup(areaGroup)"
                                            size="sm"
                                            variant="ghost"
                                            :disabled="isAddingNew"
                                        >
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </template>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </AppLayout>
</template>
