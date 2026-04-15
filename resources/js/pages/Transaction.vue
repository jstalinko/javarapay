<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { CreditCard, Calendar, Clock, ArrowRight, ExternalLink } from 'lucide-vue-next';

interface Transaction {
    id: number;
    project_id: number;
    merchant_ref: string;
    is_production: boolean;
    payment_method_name: string;
    amount: number;
    total_fee: number;
    total_amount: number;
    notes: string | null;
    status: string;
    paid_at: string | null;
    created_at: string;
    project: {
        name: string;
    };
}

interface PaginatedData<T> {
    data: T[];
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
    current_page: number;
    last_page: number;
    total: number;
}

const props = defineProps<{
    transactions: PaginatedData<Transaction>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Transactions', href: '/dashboard/transaction' },
];

const formatIDR = (amount: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(amount);
};

const formatDate = (dateString: string | null) => {
    if (!dateString) return '—';
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const getStatusClass = (status: string) => {
    switch (status.toLowerCase()) {
        case 'paid':
        case 'success':
            return 'bg-green-100 text-green-800 border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800';
        case 'unpaid':
        case 'pending':
            return 'bg-yellow-100 text-yellow-800 border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-400 dark:border-yellow-800';
        case 'expired':
        case 'failed':
            return 'bg-red-100 text-red-800 border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800';
        default:
            return 'bg-muted text-muted-foreground';
    }
};
</script>

<template>
    <Head title="Transactions" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex flex-col gap-1">
                <h1 class="text-2xl font-bold tracking-tight">Transactions</h1>
                <p class="text-muted-foreground text-sm">Monitor all payments across your projects.</p>
            </div>

            <div class="rounded-xl border bg-card text-card-foreground shadow-sm overflow-hidden">
                <div class="relative w-full overflow-auto">
                    <table class="w-full caption-bottom text-sm border-collapse">
                        <thead>
                            <tr class="border-b transition-colors hover:bg-muted/50 text-muted-foreground font-medium">
                                <th class="h-12 px-4 text-left align-middle font-medium w-[180px]">Project</th>
                                <th class="h-12 px-4 text-left align-middle font-medium">Ref / Environment</th>
                                <th class="h-12 px-4 text-left align-middle font-medium">Payment Method</th>
                                <th class="h-12 px-4 text-right align-middle font-medium">Amount</th>
                                <th class="h-12 px-4 text-right align-middle font-medium">Fee</th>
                                <th class="h-12 px-4 text-right align-middle font-medium">Total Bayar</th>
                                <th class="h-12 px-4 text-center align-middle font-medium">Status</th>
                                <th class="h-12 px-4 text-right align-middle font-medium">Paid At</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr v-for="tx in transactions.data" :key="tx.id" class="transition-colors hover:bg-muted/50 group">
                                <td class="p-4 align-middle">
                                    <div class="font-semibold text-foreground">{{ tx.project.name }}</div>
                                </td>
                                <td class="p-4 align-middle">
                                    <div class="flex flex-col gap-1">
                                        <span class="font-mono text-xs text-muted-foreground">{{ tx.merchant_ref }}</span>
                                        <div>
                                            <span v-if="tx.is_production" class="inline-flex items-center rounded-full border px-2 py-0.5 text-[10px] font-bold bg-primary/10 text-primary border-primary/20">PRODUCTION</span>
                                            <span v-else class="inline-flex items-center rounded-full border px-2 py-0.5 text-[10px] font-bold bg-muted text-muted-foreground">SANDBOX</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 align-middle">
                                    <div class="flex items-center gap-2">
                                        <CreditCard class="h-3.5 w-3.5 text-muted-foreground" />
                                        <span>{{ tx.payment_method_name }}</span>
                                    </div>
                                </td>
                                <td class="p-4 align-middle text-right font-medium">
                                    {{ formatIDR(tx.amount) }}
                                </td>
                                <td class="p-4 align-middle text-right text-muted-foreground">
                                    {{ formatIDR(tx.total_fee) }}
                                </td>
                                <td class="p-4 align-middle text-right font-bold text-primary">
                                    {{ formatIDR(tx.total_amount) }}
                                </td>
                                <td class="p-4 align-middle text-center">
                                    <span :class="['inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold', getStatusClass(tx.status)]">
                                        {{ tx.status.toUpperCase() }}
                                    </span>
                                </td>
                                <td class="p-4 align-middle text-right">
                                    <div class="flex flex-col items-end gap-0.5 text-xs">
                                        <div class="flex items-center gap-1 text-muted-foreground">
                                            <Calendar class="h-3 w-3" />
                                            {{ formatDate(tx.paid_at) }}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="transactions.data.length === 0">
                                <td colspan="8" class="p-12 text-center text-muted-foreground">
                                    <div class="flex flex-col items-center gap-2">
                                        <Clock class="h-8 w-8 opacity-20" />
                                        <p>No transactions found.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div v-if="transactions.links.length > 3" class="flex items-center justify-between px-4 py-4 border-t bg-muted/20">
                    <div class="text-xs text-muted-foreground">
                        Showing <span class="font-medium">{{ transactions.data.length }}</span> of <span class="font-medium">{{ transactions.total }}</span> transactions
                    </div>
                    <div class="flex gap-1">
                        <template v-for="(link, k) in transactions.links" :key="k">
                            <div v-if="link.url === null" class="inline-flex items-center justify-center rounded-md text-xs font-medium border h-8 px-3 text-muted-foreground" v-html="link.label" />
                            <Link v-else :href="link.url" class="inline-flex items-center justify-center rounded-md text-xs font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border h-8 px-3 hover:bg-accent hover:text-accent-foreground" :class="{'bg-primary text-primary-foreground border-primary hover:bg-primary/90': link.active}" v-html="link.label" preserve-scroll />
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
