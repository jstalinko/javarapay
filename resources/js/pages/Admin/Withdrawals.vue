<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, usePage, router, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Search, Banknote, Building2, CheckCircle, Clock, XCircle, AlertCircle, FileText, Download } from 'lucide-vue-next';

interface Withdrawal {
    id: number;
    reference: string;
    amount: number;
    fee: number;
    received_amount: number;
    notes: string | null;
    status: string;
    created_at: string;
    user: {
        id: number;
        name: string;
        email: string;
        phone: string | null;
    };
    bank: {
        id: number;
        bank_name: string;
        account_name: string;
        account_number: string;
    };
}

const props = defineProps<{
    withdrawals: {
        data: Withdrawal[];
        links: any[];
    };
    filters: {
        search?: string;
        start_date?: string;
        end_date?: string;
    };
}>();

const page = usePage();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Disbursement Management',
        href: '/dashboard/admin/withdrawal',
    },
];

const selectedWithdrawal = ref<Withdrawal | null>(null);

const form = useForm({
    status: '',
    notes: '',
});

const search = ref(props.filters.search || '');
const startDate = ref(props.filters.start_date || '');
const endDate = ref(props.filters.end_date || '');

let debounceTimer: ReturnType<typeof setTimeout>;
watch([search, startDate, endDate], ([newSearch, newStart, newEnd]) => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get(route('dashboard.admin.withdrawal'), { 
            search: newSearch,
            start_date: newStart,
            end_date: newEnd
        }, { preserveState: true, replace: true, preserveScroll: true });
    }, 300);
});

const openDetails = (withdrawal: Withdrawal) => {
    selectedWithdrawal.value = withdrawal;
    form.status = withdrawal.status;
    form.notes = withdrawal.notes || '';
};

const closeDetails = () => {
    selectedWithdrawal.value = null;
    form.reset();
};

const submitUpdate = () => {
    if (!selectedWithdrawal.value) return;
    
    form.post(route('dashboard.admin.withdrawal.update', selectedWithdrawal.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeDetails();
        },
    });
};

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
};

const formatDate = (dateString: string) => {
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
            return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800';
        case 'PENDING':
            return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400 border border-yellow-200 dark:border-yellow-800';
        case 'REJECTED':
            return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 border border-red-200 dark:border-red-800';
        case 'CANCELLED':
            return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300 border border-gray-200 dark:border-gray-700';
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300';
    }
};
</script>

<template>
    <Head title="Disbursement Management" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 px-10">
            <!-- Header -->
            <div class="flex flex-col gap-2">
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100">Disbursements & Withdrawals</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Manage user cashout requests and update payout statuses.</p>
            </div>
            
            <div v-if="page.props.flash?.success" class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800 dark:border-green-900/50 dark:bg-green-900/30 dark:text-green-400 flex items-center gap-2">
                <CheckCircle class="h-4 w-4" /> {{ page.props.flash.success }}
            </div>

            <!-- List View -->
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
                            placeholder="Search globally by reference or username..." 
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
                                <th scope="col" class="px-6 py-4">Reference & Status</th>
                                <th scope="col" class="px-6 py-4">User</th>
                                <th scope="col" class="px-6 py-4">Destination Bank</th>
                                <th scope="col" class="px-6 py-4 text-right">Settlement Output</th>
                                <th scope="col" class="px-6 py-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="wd in withdrawals.data" :key="wd.id" class="border-b bg-white hover:bg-gray-50 dark:border-gray-800 dark:bg-sidebar dark:hover:bg-gray-800/50">
                                <td class="px-6 py-4">
                                    <div class="font-mono text-sm font-semibold text-gray-900 dark:text-white mb-1">
                                        {{ wd.reference }}
                                    </div>
                                    <span :class="['rounded px-2.5 py-0.5 text-xs font-bold tracking-wide', badgeClass(wd.status)]">
                                        {{ wd.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ wd.user.name }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">{{ wd.user.email }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900 dark:text-white flex items-center gap-1.5">
                                        <Building2 class="h-3.5 w-3.5 text-gray-400" />
                                        {{ wd.bank?.bank_name }}
                                    </div>
                                    <div class="text-xs font-mono mt-0.5 text-gray-500">{{ wd.bank?.account_number }}</div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="font-bold text-gray-900 dark:text-gray-100 flex justify-end gap-1 items-center">
                                        {{ formatCurrency(wd.received_amount) }}
                                    </div>
                                    <div class="text-[11px] text-gray-500 mt-0.5">{{ formatDate(wd.created_at) }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <button 
                                        @click="openDetails(wd)" 
                                        class="inline-flex items-center gap-2 rounded-lg bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100 border border-indigo-100 dark:bg-indigo-900/30 dark:border-indigo-800 dark:text-indigo-400 dark:hover:bg-indigo-900/50 transition shadow-sm"
                                    >
                                        <FileText class="h-4 w-4" />
                                        Process
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="withdrawals.data.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center space-y-3">
                                        <div class="rounded-full bg-gray-100 p-3 dark:bg-gray-800">
                                            <CheckCircle class="h-6 w-6 text-gray-400" />
                                        </div>
                                        <p class="text-base font-medium text-gray-900 dark:text-gray-100">Zero Pending Disbursements</p>
                                        <p class="text-sm">There are no withdrawals matching the current filters.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="withdrawals.links && withdrawals.links.length > 3" class="flex items-center justify-center border-t border-gray-200 p-4 dark:border-gray-800 sm:justify-between">
                    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700 dark:text-gray-400">
                                Showing
                                <span class="font-medium text-gray-900 dark:text-white">{{ withdrawals.data.length }}</span>
                                results
                            </p>
                        </div>
                        <div>
                            <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                                <template v-for="(link, i) in withdrawals.links" :key="i">
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

        <!-- Details & Process Modal -->
        <div v-if="selectedWithdrawal" class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-black/50 p-4 md:p-0">
            <div class="relative w-full max-w-4xl rounded-2xl bg-white shadow-xl dark:bg-gray-900 border border-gray-200 dark:border-gray-800 transition-all flex flex-col max-h-[90vh]">
                
                <div class="flex shrink-0 items-center justify-between border-b border-gray-200 p-5 dark:border-gray-800">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <Download class="h-5 w-5 text-indigo-500" /> Process Disbursement
                    </h3>
                    <button @click="closeDetails" type="button" class="ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-800 dark:hover:text-white transition">
                        <XCircle class="h-5 w-5" />
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>

                <div class="flex flex-col md:flex-row flex-1 overflow-hidden">
                    <!-- Left Column: Details -->
                    <div class="md:w-3/5 overflow-y-auto border-r border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/10 p-6 space-y-6">
                        
                        <!-- Financial Focus Box -->
                        <div class="rounded-xl bg-white p-5 border border-indigo-100 shadow-sm dark:bg-gray-800/50 dark:border-indigo-900/30">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-sm font-semibold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Total Settlement Output</span>
                                <span :class="['rounded px-2.5 py-0.5 text-xs font-bold tracking-wide', badgeClass(selectedWithdrawal.status)]">{{ selectedWithdrawal.status }}</span>
                            </div>
                            <div class="text-4xl font-black text-gray-900 dark:text-gray-100 mb-4">{{ formatCurrency(selectedWithdrawal.received_amount) }}</div>
                            
                            <div class="grid grid-cols-2 gap-4 border-t border-gray-100 dark:border-gray-700 pt-4 mt-2">
                                <div>
                                    <span class="block text-xs uppercase text-gray-500">Gross Deduction</span>
                                    <span class="block font-medium text-gray-900 dark:text-white mt-0.5">{{ formatCurrency(selectedWithdrawal.amount) }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="block text-xs uppercase text-gray-500">Platform Fee</span>
                                    <span class="block font-medium text-red-500 mt-0.5">- {{ formatCurrency(selectedWithdrawal.fee) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Routing Target -->
                        <div>
                            <h4 class="mb-3 text-sm font-semibold uppercase tracking-wider text-gray-500 flex items-center gap-2">
                                <Building2 class="h-4 w-4" /> Bank Account Target
                            </h4>
                            <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 overflow-hidden">
                                <table class="w-full text-sm">
                                    <tbody>
                                        <tr class="border-b border-gray-200 dark:border-gray-700">
                                            <td class="bg-gray-50 px-4 py-3 font-medium text-gray-600 dark:bg-gray-800/80 dark:text-gray-400 w-1/3">Bank Institution</td>
                                            <td class="px-4 py-3 font-bold text-indigo-600 dark:text-indigo-400">{{ selectedWithdrawal.bank?.bank_name }}</td>
                                        </tr>
                                        <tr class="border-b border-gray-200 dark:border-gray-700">
                                            <td class="bg-gray-50 px-4 py-3 font-medium text-gray-600 dark:bg-gray-800/80 dark:text-gray-400">Account Owner</td>
                                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100 font-medium">{{ selectedWithdrawal.bank?.account_name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="bg-gray-50 px-4 py-3 font-medium text-gray-600 dark:bg-gray-800/80 dark:text-gray-400">Account Number</td>
                                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100 font-mono text-lg tracking-wider">{{ selectedWithdrawal.bank?.account_number }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Applicant Identity -->
                        <div>
                            <h4 class="mb-3 text-sm font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-200 pb-2 dark:border-gray-800">Owning User</h4>
                            <div class="flex items-center justify-between text-sm">
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ selectedWithdrawal.user.name }}</div>
                                    <div class="text-gray-500">{{ selectedWithdrawal.user.email }}</div>
                                </div>
                                <div class="text-right text-gray-500">
                                    <div>{{ selectedWithdrawal.user.phone || 'No phone recorded' }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- System Timestamps -->
                        <div class="flex justify-between items-center text-xs text-gray-400 border-t border-gray-200 dark:border-gray-800 pt-4">
                            <span>Reference: <span class="font-mono text-gray-600 dark:text-gray-300">{{ selectedWithdrawal.reference }}</span></span>
                            <span>Requested: {{ formatDate(selectedWithdrawal.created_at) }}</span>
                        </div>
                    </div>

                    <!-- Right Column: Admin Actions -->
                    <div class="md:w-2/5 p-6 flex flex-col bg-white dark:bg-gray-900 overflow-y-auto">
                        <form @submit.prevent="submitUpdate" class="flex flex-col flex-1 h-full">
                            <h4 class="mb-5 text-sm font-semibold uppercase tracking-wider text-gray-900 flex items-center gap-2 dark:text-white">
                                Manual Execution
                            </h4>

                            <!-- Status Override -->
                            <div class="mb-5 space-y-1">
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">New Payment Status</label>
                                <p class="text-xs text-gray-500 mb-2">Changing to PAID asserts money has been transferred.</p>
                                <select 
                                    id="status" 
                                    v-model="form.status" 
                                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-3 text-sm font-semibold text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:focus:border-indigo-500 transition"
                                >
                                    <option value="PENDING">PENDING (In Queue)</option>
                                    <option value="PAID">PAID (Clear)</option>
                                    <option value="REJECTED">REJECTED (Deny)</option>
                                    <option value="CANCELLED">CANCELLED (Stop)</option>
                                </select>
                                <div v-if="form.errors.status" class="mt-1 text-xs text-red-500">{{ form.errors.status }}</div>
                            </div>

                            <!-- Administrator Notes -->
                            <div class="mb-auto">
                                <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Internal Reference Notes</label>
                                <p class="text-xs text-gray-500 mb-2">Provide context for your action (e.g., Transfer ID from mutasi, or rejection reason).</p>
                                <textarea 
                                    id="notes" 
                                    v-model="form.notes" 
                                    rows="4" 
                                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-3 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500 dark:focus:border-indigo-500" 
                                    placeholder="Write a clear internal note..."
                                ></textarea>
                                <div v-if="form.errors.notes" class="mt-1 text-xs text-red-500">{{ form.errors.notes }}</div>
                            </div>

                            <!-- Submission Block -->
                            <div class="mt-6 border-t border-gray-100 dark:border-gray-800 pt-5">
                                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-3 mb-4 flex gap-2">
                                    <AlertCircle class="h-4 w-4 text-amber-600 shrink-0 mt-0.5" />
                                    <p class="text-[11px] text-amber-800 dark:text-amber-400">Ensure the exact value of <strong class="font-bold border-b border-amber-300">{{ formatCurrency(selectedWithdrawal.received_amount) }}</strong> has been mapped properly to <span class="font-mono bg-amber-100/50 px-1 rounded">{{ selectedWithdrawal.bank?.account_number }}</span> before marking as PAID.</p>
                                </div>
                                <div class="flex gap-3">
                                    <button 
                                        @click="closeDetails" 
                                        type="button" 
                                        class="flex-1 rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition"
                                    >
                                        Cancel
                                    </button>
                                    <button 
                                        type="submit" 
                                        :disabled="form.processing"
                                        class="flex-[2] inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 py-3 text-sm font-bold text-white hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-300 disabled:opacity-50 dark:focus:ring-indigo-800 transition shadow-lg shadow-indigo-500/20"
                                    >
                                        <CheckCircle v-if="!form.processing" class="h-4 w-4" /> 
                                        {{ form.processing ? 'Saving...' : 'Commit Status Override' }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>

    </AppLayout>
</template>
