<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Search, Eye, ArrowRightLeft, CreditCard, Banknote, ShieldAlert, CheckCircle, Clock, XCircle } from 'lucide-vue-next';

interface Transaction {
    id: number;
    txid: string;
    reference: string;
    merchant_ref: string;
    amount: number;
    total_fee: number;
    total_amount: number;
    payment_method_name: string;
    payment_method_code: string;
    status: string;
    is_production: boolean;
    paid_at: string | null;
    settled_at: string | null;
    created_at: string;
    project: {
        id: number;
        name: string;
        user: {
            id: number;
            name: string;
            email: string;
        }
    }
}

const props = defineProps<{
    transactions: {
        data: Transaction[];
        links: any[];
    };
    filters: {
        search?: string;
        start_date?: string;
        end_date?: string;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Global Transactions',
        href: '/dashboard/admin/transactions',
    },
];

const selectedTx = ref<Transaction | null>(null);
const search = ref(props.filters.search || '');
const startDate = ref(props.filters.start_date || '');
const endDate = ref(props.filters.end_date || '');

let debounceTimer: ReturnType<typeof setTimeout>;
watch([search, startDate, endDate], ([newSearch, newStart, newEnd]) => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get(route('dashboard.admin.transactions'), { 
            search: newSearch,
            start_date: newStart,
            end_date: newEnd
        }, { preserveState: true, replace: true, preserveScroll: true });
    }, 300);
});

const openDetails = (tx: Transaction) => {
    selectedTx.value = tx;
};

const closeDetails = () => {
    selectedTx.value = null;
};

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
};

const formatDate = (dateString: string | null) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const badgeClass = (status: string) => {
    switch (status) {
        case 'PAID':
            return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
        case 'PENDING':
            return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400';
        case 'FAILED':
        case 'EXPIRED':
        case 'CANCELLED':
            return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400';
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300';
    }
};
</script>

<template>
    <Head title="Global Transactions" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 px-10">
            <!-- Header -->
            <div class="flex flex-col gap-2">
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100">Global Transactions</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Monitor platform-wide real-time transactions spanning all users and projects.</p>
            </div>

            <!-- Transaction List -->
            <div class="flex flex-col rounded-2xl border border-sidebar-border/70 bg-white shadow-sm dark:border-sidebar-border dark:bg-sidebar">
                <!-- Toolbar -->
                <div class="flex flex-col gap-4 border-b border-gray-200 p-4 dark:border-gray-800 md:flex-row md:items-center md:justify-between">
                    <div class="relative w-full md:max-w-md">
                        <div class="pointer-events-none absolute inset-y-0 start-0 flex items-center ps-3">
                            <Search class="h-4 w-4 text-gray-400" />
                        </div>
                        <input 
                            type="text" 
                            v-model="search"
                            class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 ps-10 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-indigo-500 dark:focus:ring-indigo-500" 
                            placeholder="Search TXID, Reference, Project Name, or User..." 
                        />
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <input 
                            type="date" 
                            title="Start Date"
                            v-model="startDate"
                            class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" 
                        />
                        <span class="text-gray-500">-</span>
                        <input 
                            type="date" 
                            title="End Date"
                            v-model="endDate"
                            class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" 
                        />
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-500 dark:text-gray-400">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-700 dark:bg-gray-800/50 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-4">Transaction Details</th>
                                <th scope="col" class="px-6 py-4">Source (Project & User)</th>
                                <th scope="col" class="px-6 py-4 text-right">Requested Amount</th>
                                <th scope="col" class="px-6 py-4 text-center">Status</th>
                                <th scope="col" class="px-6 py-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="tx in transactions.data" :key="tx.id" class="border-b bg-white hover:bg-gray-50 dark:border-gray-800 dark:bg-sidebar dark:hover:bg-gray-800/50">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900 dark:text-white flex items-center gap-2">
                                        <ArrowRightLeft class="h-3.5 w-3.5 text-gray-400" /> 
                                        {{ tx.txid }}
                                    </div>
                                    <div class="text-xs font-mono mt-0.5 text-gray-500">Ref: {{ tx.merchant_ref }}</div>
                                    <div class="text-[10px] mt-1 tracking-widest uppercase text-indigo-500 font-bold">{{ tx.payment_method_name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900 dark:text-white flex items-center gap-1">
                                        {{ tx.project?.name || 'Unknown Project' }}
                                        <span v-if="tx.is_production" class="ml-1 w-2 h-2 bg-indigo-500 rounded-full" title="Production"></span>
                                        <span v-else class="ml-1 w-2 h-2 bg-amber-500 rounded-full" title="Sandbox"></span>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-0.5">By: {{ tx.project?.user?.name || 'Unknown User' }}</div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="font-bold text-gray-900 dark:text-gray-100">{{ formatCurrency(tx.amount) }}</div>
                                    <div class="text-xs text-gray-500">+ {{ formatCurrency(tx.total_fee) }} fee</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span :class="['rounded px-2.5 py-0.5 text-xs font-medium', badgeClass(tx.status)]">
                                        {{ tx.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <button 
                                        @click="openDetails(tx)" 
                                        class="inline-flex items-center gap-2 rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition"
                                    >
                                        <Eye class="h-4 w-4" />
                                        View
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="transactions.data.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    No transactions found matching your records.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="transactions.links && transactions.links.length > 3" class="flex items-center justify-center border-t border-gray-200 p-4 dark:border-gray-800 sm:justify-between">
                    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700 dark:text-gray-400">
                                Showing
                                <span class="font-medium text-gray-900 dark:text-white">{{ transactions.data.length }}</span>
                                results
                            </p>
                        </div>
                        <div>
                            <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                                <template v-for="(link, i) in transactions.links" :key="i">
                                    <div 
                                        v-if="link.url === null" 
                                        class="cursor-not-allowed relative inline-flex items-center border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400"
                                        v-html="link.label" 
                                    ></div>
                                    <Link 
                                        v-else 
                                        :href="link.url"
                                        :class="[
                                            link.active ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600 dark:bg-indigo-900/30 dark:border-indigo-500 dark:text-indigo-400' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-700',
                                            'relative inline-flex items-center border px-4 py-2 text-sm font-medium'
                                        ]"
                                        v-html="link.label"
                                    ></Link>
                                </template>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details Modal -->
        <div v-if="selectedTx" class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-black/50 p-4 md:p-0">
            <div class="relative w-full max-w-2xl rounded-2xl bg-white shadow-xl dark:bg-gray-900 border border-gray-200 dark:border-gray-800 transition-all">
                
                <div class="flex items-center justify-between border-b border-gray-200 p-5 dark:border-gray-800">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <ArrowRightLeft class="h-5 w-5 text-indigo-500" /> Transaction Record
                    </h3>
                    <button @click="closeDetails" type="button" class="ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-800 dark:hover:text-white">
                        <svg class="h-3 w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>

                <div class="p-6 space-y-6 max-h-[70vh] overflow-y-auto">
                    <!-- Status Banner -->
                    <div class="flex items-center justify-between rounded-xl p-4" :class="[
                        selectedTx.status === 'PAID' ? 'bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-900/50' : 
                        (selectedTx.status === 'PENDING' ? 'bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-100 dark:border-yellow-900/50' : 'bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-900/50')
                    ]">
                        <div>
                            <span class="text-xs uppercase tracking-widest font-bold" :class="[
                                selectedTx.status === 'PAID' ? 'text-green-800 dark:text-green-500' : 
                                (selectedTx.status === 'PENDING' ? 'text-yellow-800 dark:text-yellow-500' : 'text-red-800 dark:text-red-500')
                            ]">
                                Current Status
                            </span>
                            <div class="mt-1 text-2xl font-black">{{ selectedTx.status }}</div>
                        </div>
                        <div v-if="selectedTx.status === 'PAID'">
                            <CheckCircle class="h-10 w-10 text-green-500 opacity-80" />
                        </div>
                        <div v-else-if="selectedTx.status === 'PENDING'">
                            <Clock class="h-10 w-10 text-yellow-500 opacity-80" />
                        </div>
                        <div v-else>
                            <XCircle class="h-10 w-10 text-red-500 opacity-80" />
                        </div>
                    </div>

                    <!-- Grid Info -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-800/30">
                            <span class="text-[10px] sm:text-xs uppercase tracking-wider text-gray-500">Global TXID</span>
                            <div class="mt-1 font-mono text-sm font-semibold text-gray-900 dark:text-gray-100 truncate w-full">{{ selectedTx.txid }}</div>
                        </div>
                        <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-800/30">
                            <span class="text-[10px] sm:text-xs uppercase tracking-wider text-gray-500">Merchant Reference</span>
                            <div class="mt-1 font-mono text-sm font-semibold text-gray-900 dark:text-gray-100 w-full truncate">{{ selectedTx.merchant_ref }}</div>
                        </div>
                    </div>

                    <!-- Routing Source -->
                    <div>
                        <h4 class="mb-3 text-sm font-semibold uppercase tracking-wider text-gray-500 flex items-center gap-2 border-b border-gray-200 dark:border-gray-800 pb-2">
                            Source Identity
                        </h4>
                        <div class="grid grid-cols-2 gap-x-4 gap-y-3 text-sm">
                            <div><span class="text-gray-500 block mb-0.5">Owning User</span> <span class="font-medium text-gray-900 dark:text-white">{{ selectedTx.project?.user?.name }} ({{ selectedTx.project?.user?.email }})</span></div>
                            <div><span class="text-gray-500 block mb-0.5">Origin Project</span> <span class="font-medium text-gray-900 dark:text-white">{{ selectedTx.project?.name }}</span></div>
                            <div class="col-span-2"><span class="text-gray-500 block mb-0.5">Environment Route</span> 
                                <span v-if="selectedTx.is_production" class="inline-flex items-center rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10 dark:bg-indigo-900/30 dark:text-indigo-400 dark:ring-indigo-400/20">Production Endpoints</span>
                                <span v-else class="inline-flex items-center rounded-md bg-amber-50 px-2 py-1 text-xs font-medium text-amber-800 ring-1 ring-inset ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-500 dark:ring-amber-500/20">Sandbox Environment</span>
                            </div>
                        </div>
                    </div>

                    <!-- Financial Payload -->
                    <div>
                        <h4 class="mb-3 text-sm font-semibold uppercase tracking-wider text-gray-500 flex items-center gap-2 border-b border-gray-200 dark:border-gray-800 pb-2">
                            <Banknote class="h-4 w-4" /> Financial Payload
                        </h4>
                        <div class="space-y-2 text-sm bg-gray-50 dark:bg-gray-800/50 p-4 rounded-xl border border-gray-100 dark:border-gray-800">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Method</span>
                                <span class="font-medium uppercase tracking-wider text-gray-900 dark:text-white">{{ selectedTx.payment_method_name }} ({{ selectedTx.payment_method_code }})</span>
                            </div>
                            <div class="flex justify-between mt-2">
                                <span class="text-gray-500">Base Amount</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ formatCurrency(selectedTx.amount) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Gateway Fee</span>
                                <span class="font-medium text-red-500">+ {{ formatCurrency(selectedTx.total_fee) }}</span>
                            </div>
                            <div class="my-2 border-t border-gray-200 dark:border-gray-700"></div>
                            <div class="flex justify-between items-center text-lg">
                                <span class="font-bold text-gray-900 dark:text-white">Gross Total</span>
                                <span class="font-black text-indigo-600 dark:text-indigo-400">{{ formatCurrency(selectedTx.total_amount) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Timestamps -->
                    <div v-if="selectedTx.paid_at || selectedTx.settled_at" class="grid grid-cols-2 gap-4 text-xs">
                        <div v-if="selectedTx.paid_at" class="rounded-lg border border-gray-200 p-3 dark:border-gray-800">
                            <div class="text-gray-500 mb-1 tracking-wider uppercase font-semibold">Time of Payment</div>
                            <div class="text-gray-900 dark:text-gray-100 font-mono">{{ formatDate(selectedTx.paid_at) }}</div>
                        </div>
                        <div v-if="selectedTx.settled_at" class="rounded-lg border border-gray-200 p-3 dark:border-gray-800">
                            <div class="text-gray-500 mb-1 tracking-wider uppercase font-semibold">Time of Settlement</div>
                            <div class="text-gray-900 dark:text-gray-100 font-mono">{{ formatDate(selectedTx.settled_at) }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="border-t border-gray-200 bg-gray-50 p-4 text-right dark:border-gray-800 dark:bg-gray-900/50 rounded-b-2xl">
                    <button 
                        @click="closeDetails" 
                        type="button" 
                        class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:hover:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700"
                    >
                        Close View
                    </button>
                </div>
            </div>
        </div>

    </AppLayout>
</template>
