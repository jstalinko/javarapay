<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed, watch } from 'vue';

const page = usePage();

const props = defineProps<{
    balance: number;
    withdraw_fee_type: string;
    withdraw_fee: number;
    banks: any[];
    withdrawals: any;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Withdrawal',
        href: '/dashboard/withdrawal',
    },
];

const form = useForm({
    amount: '',
    bank_id: props.banks.length > 0 ? props.banks[0].id : '',
});

const calculateFee = (amount: number) => {
    if (props.withdraw_fee_type === 'percent') {
        return Math.floor((amount * props.withdraw_fee) / 100);
    }
    return props.withdraw_fee;
};

const feeAmount = computed(() => {
    const amt = Number(form.amount) || 0;
    return calculateFee(amt);
});

const receivedAmount = computed(() => {
    const amt = Number(form.amount) || 0;
    const final = amt - feeAmount.value;
    return final > 0 ? final : 0;
});

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
};

const submitWithdrawal = () => {
    form.post(route('dashboard.withdrawal.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('amount');
        },
    });
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const badgeClass = (status: string) => {
    switch (status) {
        case 'PAID':
            return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
        case 'PENDING':
            return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400';
        case 'REJECTED':
            return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400';
        case 'CANCELLED':
            return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300';
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300';
    }
};
</script>

<template>
    <Head title="Withdrawal" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 px-10">
            <!-- Header Section -->
            <div class="flex flex-col gap-2">
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100">Withdrawal</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Manage your balance and request cashouts to your bank account.</p>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Left Column: Balance & Form -->
                <div class="flex flex-col gap-6 lg:col-span-1">
                    <!-- Balance Summary Card -->
                    <div class="flex flex-col gap-4 rounded-2xl border border-sidebar-border/70 bg-white p-6 shadow-sm dark:border-sidebar-border dark:bg-sidebar">
                        <div class="flex items-center gap-3 text-gray-600 dark:text-gray-400">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="12" rx="2"/><path d="M12 12h.01"/><path d="M17 12h.01"/><path d="M7 12h.01"/></svg>
                            </div>
                            <span class="text-sm font-medium uppercase tracking-wider">Available Balance</span>
                        </div>
                        <div class="text-4xl font-extrabold text-gray-900 dark:text-gray-100">
                            {{ formatCurrency(props.balance) }}
                        </div>
                        
                        <div v-if="page.props.flash?.success" class="mt-2 rounded-lg bg-green-50 p-3 text-sm text-green-800 dark:bg-green-900/30 dark:text-green-400">
                            {{ page.props.flash.success }}
                        </div>
                    </div>

                    <!-- Withdrawal Form -->
                    <div class="flex flex-col gap-5 rounded-2xl border border-sidebar-border/70 bg-white p-6 shadow-sm dark:border-sidebar-border dark:bg-sidebar">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Request Withdrawal</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Min. withdrawal is {{ formatCurrency(50000) }}</p>
                        </div>
                        
                        <form @submit.prevent="submitWithdrawal" class="flex flex-col gap-4">
                            <!-- Bank Selection -->
                            <div>
                                <label for="bank" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Destination Bank</label>
                                <select 
                                    id="bank" 
                                    v-model="form.bank_id" 
                                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-indigo-500 dark:focus:ring-indigo-500"
                                    required
                                >
                                    <option value="" disabled>Select a bank account</option>
                                    <option v-for="bank in banks" :key="bank.id" :value="bank.id">
                                        {{ bank.bank_name }} - {{ bank.account_number }} ({{ bank.account_name }})
                                    </option>
                                </select>
                                <div v-if="form.errors.bank_id" class="mt-1 text-sm text-red-500">{{ form.errors.bank_id }}</div>
                                <div v-if="banks.length === 0" class="mt-2 text-sm text-red-500 bg-red-50 dark:bg-red-900/20 p-2 rounded">
                                    You need to add a bank account first! Head to the Bank settings.
                                </div>
                            </div>

                            <!-- Amount -->
                            <div>
                                <label for="amount" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Amount (IDR)</label>
                                <div class="relative w-full">
                                    <div class="pointer-events-none absolute inset-y-0 start-0 flex items-center ps-3">
                                        <span class="text-gray-500 dark:text-gray-400">Rp</span>
                                    </div>
                                    <input 
                                        type="number" 
                                        id="amount" 
                                        v-model="form.amount" 
                                        min="50000" 
                                        :max="props.balance"
                                        class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 ps-10 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-indigo-500 dark:focus:ring-indigo-500" 
                                        placeholder="50000" 
                                        required
                                    />
                                    <button 
                                        type="button" 
                                        @click="form.amount = String(props.balance)"
                                        class="absolute inset-y-0 end-0 flex items-center pe-3 text-xs font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300"
                                    >
                                        MAX
                                    </button>
                                </div>
                                <div v-if="form.errors.amount" class="mt-1 text-sm text-red-500">{{ form.errors.amount }}</div>
                            </div>

                            <!-- Breakdown -->
                            <div class="mt-2 rounded-xl bg-gray-50 p-4 dark:bg-gray-800/50 flex flex-col gap-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">Requested Amount</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ formatCurrency(Number(form.amount) || 0) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500 dark:text-gray-400 shrink-0">Fee ({{ props.withdraw_fee_type === 'percent' ? props.withdraw_fee + '%' : 'Fixed' }})</span>
                                    <span class="font-medium text-red-500 dark:text-red-400">- {{ formatCurrency(feeAmount) }}</span>
                                </div>
                                <div class="my-1 border-t border-gray-200 dark:border-gray-700"></div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-900 dark:text-gray-100">You Receive</span>
                                    <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ formatCurrency(receivedAmount) }}</span>
                                </div>
                            </div>

                            <button 
                                type="submit" 
                                :disabled="form.processing || !form.amount || Number(form.amount) < 50000 || Number(form.amount) > props.balance || banks.length === 0"
                                class="mt-2 w-full rounded-lg bg-indigo-600 px-5 py-3 text-center text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-300 disabled:cursor-not-allowed disabled:bg-gray-400 disabled:opacity-70 dark:bg-indigo-600 dark:hover:bg-indigo-700 dark:focus:ring-indigo-800 dark:disabled:bg-gray-700 transition"
                            >
                                <span v-if="form.processing">Processing...</span>
                                <span v-else>Submit Request</span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Right Column: History -->
                <div class="flex flex-col rounded-2xl border border-sidebar-border/70 bg-white shadow-sm dark:border-sidebar-border dark:bg-sidebar lg:col-span-2">
                    <div class="border-b border-gray-200 p-6 dark:border-gray-800">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Withdrawal History</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Review your past cashout requests</p>
                    </div>
                    
                    <div class="flex-1 overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-500 dark:text-gray-400">
                            <thead class="bg-gray-50 text-xs uppercase text-gray-700 dark:bg-gray-800/50 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-4">Reference</th>
                                    <th scope="col" class="px-6 py-4">Date</th>
                                    <th scope="col" class="px-6 py-4">Bank</th>
                                    <th scope="col" class="px-6 py-4 text-right">Amount</th>
                                    <th scope="col" class="px-6 py-4">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="wd in withdrawals.data" :key="wd.id" class="border-b bg-white hover:bg-gray-50 dark:border-gray-800 dark:bg-sidebar dark:hover:bg-gray-800/50">
                                    <td class="whitespace-nowrap px-6 py-4 font-medium text-gray-900 dark:text-white">
                                        {{ wd.reference }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ formatDate(wd.created_at) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900 dark:text-gray-200">{{ wd.bank?.bank_name }}</div>
                                        <div class="text-xs">{{ wd.bank?.account_number }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="font-medium text-gray-900 dark:text-gray-200">{{ formatCurrency(wd.amount) }}</div>
                                        <div class="text-xs text-red-500">- {{ formatCurrency(wd.fee) }} fee</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span :class="['rounded px-2.5 py-0.5 text-xs font-medium', badgeClass(wd.status)]">
                                            {{ wd.status }}
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="withdrawals.data.length === 0">
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                        No withdrawal history found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
