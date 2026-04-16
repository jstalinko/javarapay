<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { defineProps } from 'vue';
import { CreditCard, Banknote, LayoutDashboard, ArrowRightLeft } from 'lucide-vue-next';

const props = defineProps<{
    totalTransactions: number;
    totalAmountTransactions: number;
    totalWithdrawals: number;
    totalAmountWithdrawals: number;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Admin Dashboard',
        href: '/dashboard/admin',
    },
];

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
};

const formatNumber = (value: number) => {
    return new Intl.NumberFormat('id-ID').format(value);
};
</script>

<template>
    <Head title="Admin Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 px-10">
            <!-- Header Section -->
            <div class="flex flex-col gap-2">
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100">Administrator Overview</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Platform-wide statistics and metrics.</p>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                
                <!-- Total Transactions (Count) -->
                <div class="flex flex-col gap-3 rounded-2xl border border-sidebar-border/70 bg-white p-6 shadow-sm transition hover:shadow-md dark:border-sidebar-border dark:bg-sidebar">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                            <ArrowRightLeft class="h-6 w-6" />
                        </div>
                        <h3 class="text-sm font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Transactions</h3>
                    </div>
                    <div class="mt-2 text-4xl font-extrabold text-gray-900 dark:text-gray-100">
                        {{ formatNumber(totalTransactions) }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Globally recorded transactions</div>
                </div>

                <!-- Total Amount Transactions -->
                <div class="flex flex-col gap-3 rounded-2xl border border-sidebar-border/70 bg-white p-6 shadow-sm transition hover:shadow-md dark:border-sidebar-border dark:bg-sidebar">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                            <CreditCard class="h-6 w-6" />
                        </div>
                        <h3 class="text-sm font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Tx Amount (Sum)</h3>
                    </div>
                    <div class="mt-2 text-2xl font-extrabold text-gray-900 dark:text-gray-100 md:text-3xl">
                        {{ formatCurrency(totalAmountTransactions) }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Total value of PAID transactions</div>
                </div>

                <!-- Total Withdrawals (Count) -->
                <div class="flex flex-col gap-3 rounded-2xl border border-sidebar-border/70 bg-white p-6 shadow-sm transition hover:shadow-md dark:border-sidebar-border dark:bg-sidebar">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400">
                            <Banknote class="h-6 w-6" />
                        </div>
                        <h3 class="text-sm font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Withdrawals</h3>
                    </div>
                    <div class="mt-2 text-4xl font-extrabold text-gray-900 dark:text-gray-100">
                        {{ formatNumber(totalWithdrawals) }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Total cashout requests processed</div>
                </div>

                <!-- Total Amount Withdrawals -->
                <div class="flex flex-col gap-3 rounded-2xl border border-sidebar-border/70 bg-white p-6 shadow-sm transition hover:shadow-md dark:border-sidebar-border dark:bg-sidebar">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-purple-50 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400">
                            <LayoutDashboard class="h-6 w-6" />
                        </div>
                        <h3 class="text-sm font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Withdrawals (Sum)</h3>
                    </div>
                    <div class="mt-2 text-2xl font-extrabold text-gray-900 dark:text-gray-100 md:text-3xl">
                        {{ formatCurrency(totalAmountWithdrawals) }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Total value of PENDING & PAID cashouts</div>
                </div>

            </div>
            
            <div class="mt-4 rounded-xl border border-sidebar-border/70 bg-gray-50 p-6 dark:border-sidebar-border dark:bg-sidebar/50">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Use the sidebar navigation to manage specific administrative tasks like Project Review, User Management, and global Transaction tables.
                </p>
            </div>
            
        </div>
    </AppLayout>
</template>
