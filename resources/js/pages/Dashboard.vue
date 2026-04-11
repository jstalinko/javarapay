<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Wallet, Clock, ArrowRightLeft, Boxes, Megaphone } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const props = defineProps<{
    saldo?: number;
    saldoTertunda?: number;
    totalTransaksi?: number;
    totalProject?: number;
    announcements?: { id: number; title: string; body: string; date: string }[];
}>();

const stats = [
    {
        label: 'Saldo',
        value: props.saldo ?? 0,
        icon: Wallet,
        color: 'from-emerald-500 to-teal-600',
        bgColor: 'bg-emerald-500/10',
        textColor: 'text-emerald-500',
        format: 'currency',
    },
    {
        label: 'Saldo Tertunda',
        value: props.saldoTertunda ?? 0,
        icon: Clock,
        color: 'from-amber-500 to-orange-600',
        bgColor: 'bg-amber-500/10',
        textColor: 'text-amber-500',
        format: 'currency',
    },
    {
        label: 'Total Transaksi',
        value: props.totalTransaksi ?? 0,
        icon: ArrowRightLeft,
        color: 'from-blue-500 to-indigo-600',
        bgColor: 'bg-blue-500/10',
        textColor: 'text-blue-500',
        format: 'number',
    },
    {
        label: 'Total Project',
        value: props.totalProject ?? 0,
        icon: Boxes,
        color: 'from-violet-500 to-purple-600',
        bgColor: 'bg-violet-500/10',
        textColor: 'text-violet-500',
        format: 'number',
    },
];

function formatValue(value: number, format: string) {
    if (format === 'currency') {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        }).format(value);
    }
    return new Intl.NumberFormat('id-ID').format(value);
}

const defaultAnnouncements = props.announcements ?? [
    {
        id: 1,
        title: '🎉 Selamat Datang di JavaraPay!',
        body: 'Terima kasih sudah mendaftar. Mulai buat payment link pertama Anda dan terima pembayaran dengan mudah. Kunjungi halaman Project untuk memulai.',
        date: new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }),
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
            <!-- Stats Overview -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div
                    v-for="(stat, i) in stats"
                    :key="i"
                    class="group relative overflow-hidden rounded-xl border border-sidebar-border/70 bg-card p-5 transition-all duration-300 hover:shadow-lg dark:border-sidebar-border dark:bg-card"
                >
                    <!-- Gradient accent top -->
                    <div
                        class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r opacity-80"
                        :class="stat.color"
                    />

                    <div class="flex items-start justify-between">
                        <div class="space-y-2">
                            <p class="text-sm font-medium text-muted-foreground">{{ stat.label }}</p>
                            <p class="text-2xl font-bold tracking-tight text-foreground">
                                {{ formatValue(stat.value, stat.format) }}
                            </p>
                        </div>
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-lg transition-transform duration-300 group-hover:scale-110"
                            :class="stat.bgColor"
                        >
                            <component :is="stat.icon" class="h-5 w-5" :class="stat.textColor" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Announcement Section -->
            <div class="rounded-xl border border-sidebar-border/70 bg-card dark:border-sidebar-border">
                <div class="flex items-center gap-3 border-b border-sidebar-border/70 px-6 py-4 dark:border-sidebar-border">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-500/10">
                        <Megaphone class="h-4 w-4 text-blue-500" />
                    </div>
                    <h2 class="text-base font-semibold text-foreground">Announcement</h2>
                </div>
                <div class="divide-y divide-sidebar-border/70 dark:divide-sidebar-border">
                    <div
                        v-for="ann in defaultAnnouncements"
                        :key="ann.id"
                        class="px-6 py-4 transition-colors hover:bg-muted/50"
                    >
                        <div class="mb-1 flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-foreground">{{ ann.title }}</h3>
                            <span class="text-xs text-muted-foreground">{{ ann.date }}</span>
                        </div>
                        <p class="text-sm leading-relaxed text-muted-foreground">{{ ann.body }}</p>
                    </div>
                    <div
                        v-if="defaultAnnouncements.length === 0"
                        class="px-6 py-10 text-center text-sm text-muted-foreground"
                    >
                        Belum ada pengumuman.
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
