<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { MoreVertical, Plus, Search, X } from 'lucide-vue-next'
import { ref, watch } from 'vue'

import CustomerForm from '@/components/forms/CustomerForm.vue'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog'
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { Input } from '@/components/ui/input'
import {
    Table,
    TableBody,
    TableCell,
    TableEmpty,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItem } from '@/types'
import type { Customer } from '@/types/models'

interface PaginatedCustomers {
    data: Customer[]
    current_page: number
    last_page: number
    per_page: number
    total: number
}

const props = defineProps<{
    customers: PaginatedCustomers
    search?: string
}>()

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Customers',
        href: '/customers',
    },
]

const search = ref(props.search || '')
const isCreateDialogOpen = ref(false)

let searchTimeout: ReturnType<typeof setTimeout> | null = null

watch(search, (value) => {
    if (searchTimeout) {
        clearTimeout(searchTimeout)
    }
    searchTimeout = setTimeout(() => {
        router.get('/customers', { search: value }, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        })
    }, 300)
})

const clearSearch = () => {
    search.value = ''
}

const formatAddress = (customer: Customer): string => {
    const parts = [customer.street, customer.city, customer.state, customer.zipcode].filter(
        Boolean,
    )
    return parts.join(' ')
}

const handleCreateSuccess = (customer: Customer) => {
    // Close the dialog
    isCreateDialogOpen.value = false

    // Navigate to the newly created customer
    router.visit(`/customers/${customer.id}`)
}
</script>

<template>
    <Head title="Customers" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Customers</h1>
                <Dialog v-model:open="isCreateDialogOpen">
                    <DialogTrigger as-child>
                        <Button>
                            <Plus class="mr-2 h-4 w-4" />
                            New Customer
                        </Button>
                    </DialogTrigger>
                    <DialogContent class="sm:max-w-2xl max-h-[90vh] overflow-y-auto">
                        <DialogHeader>
                            <DialogTitle>Create New Customer</DialogTitle>
                            <DialogDescription>
                                Add a new customer to your database
                            </DialogDescription>
                        </DialogHeader>
                        <CustomerForm
                            mode="create"
                            @success="handleCreateSuccess"
                        />
                    </DialogContent>
                </Dialog>
            </div>

            <div class="rounded-md border bg-card">
                <div class="p-4">
                    <div class="relative">
                        <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                        <Input
                            v-model="search"
                            type="text"
                            placeholder="Search by name, address, email, or phone..."
                            class="pl-9 pr-9"
                        />
                        <button
                            v-if="search"
                            @click="clearSearch"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors"
                            type="button"
                        >
                            <X class="h-4 w-4" />
                        </button>
                    </div>
                </div>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Customer</TableHead>
                            <TableHead>Contact</TableHead>
                            <TableHead>Services</TableHead>
                            <TableHead class="w-[50px]"></TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableEmpty v-if="!customers.data.length" :colspan="4">
                            No customers found.
                        </TableEmpty>
                        <TableRow
                            v-for="customer in customers.data"
                            :key="customer.id"
                            class="cursor-pointer"
                            @click="
                                $inertia.visit(`/customers/${customer.id}`)
                            "
                        >
                            <TableCell>
                                <div class="flex flex-col">
                                    <span class="font-medium">
                                        {{ customer.full_name || 'N/A' }}
                                    </span>
                                    <span
                                        v-if="formatAddress(customer)"
                                        class="text-sm text-muted-foreground"
                                    >
                                        {{ formatAddress(customer) }}
                                    </span>
                                </div>
                            </TableCell>
                            <TableCell>
                                <div class="flex flex-col">
                                    <span class="font-medium">
                                        {{ customer.phone || 'N/A' }}
                                    </span>
                                    <span
                                        v-if="customer.email"
                                        class="text-sm text-muted-foreground"
                                    >
                                        {{ customer.email }}
                                    </span>
                                </div>
                            </TableCell>
                            <TableCell>
                                <div class="flex gap-2">
                                    <Badge v-if="customer.irrigation" variant="outline-blue">
                                        Irrigation
                                    </Badge>
                                    <Badge
                                        v-if="customer.maintenance"
                                        variant="outline-green"
                                    >
                                        Maintenance
                                    </Badge>
                                </div>
                            </TableCell>
                            <TableCell @click.stop>
                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child>
                                        <Button variant="ghost" size="icon">
                                            <MoreVertical class="h-4 w-4" />
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end">
                                        <DropdownMenuItem
                                            @click="
                                                $inertia.visit(
                                                    `/customers/${customer.id}`,
                                                )
                                            "
                                        >
                                            View Details
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <div
                v-if="customers.last_page > 1"
                class="flex items-center justify-between"
            >
                <div class="text-sm text-muted-foreground">
                    Showing {{ (customers.current_page - 1) * customers.per_page + 1 }}
                    to
                    {{
                        Math.min(
                            customers.current_page * customers.per_page,
                            customers.total,
                        )
                    }}
                    of {{ customers.total }} customers
                </div>
                <div class="flex gap-2">
                    <Link
                        v-if="customers.current_page > 1"
                        :href="`/customers?page=${customers.current_page - 1}`"
                        preserve-scroll
                    >
                        <Button variant="outline">Previous</Button>
                    </Link>
                    <Link
                        v-if="customers.current_page < customers.last_page"
                        :href="`/customers?page=${customers.current_page + 1}`"
                        preserve-scroll
                    >
                        <Button variant="outline">Next</Button>
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
