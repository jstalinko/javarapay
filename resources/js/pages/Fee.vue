<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { useAppearance } from '@/composables/useAppearance';
import { 
    Sun, Moon, Search, Layers, Clock, ArrowDownToLine, 
    ArrowRight, Wallet, Percent, ShieldCheck, HelpCircle,
    Info, DollarSign, Image as ImageIcon
} from 'lucide-vue-next';

interface PaymentMethod {
    id: number;
    group: string | null;
    method_code: string;
    method_name: string;
    gateway: string;
    image: string | null;
    description: string | null;
    is_qris: boolean | number;
    min_amount: number | null;
    max_amount: number | null;
    fee_percent: number | string;
    fee_flat: number;
    active: boolean | number;
}

const props = defineProps<{
    paymentMethods: PaymentMethod[];
}>();

const { appearance, updateAppearance } = useAppearance();
const isDark = computed(() => appearance.value === 'dark');

const toggleTheme = () => {
    updateAppearance(isDark.value ? 'light' : 'dark');
};

const search = ref('');
const activeTab = ref('All');

const tabs = [
    { name: 'Semua', value: 'All' },
    { name: 'Virtual Account', value: 'Virtual Account' },
    { name: 'E-Wallet', value: 'E-Wallet' },
    { name: 'Gerai Ritel', value: 'Convenience Store' },
    { name: 'Lainnya', value: 'Other' }
];

const filteredMethods = computed(() => {
    return props.paymentMethods.filter(m => {
        // Search filter
        const term = search.value.toLowerCase();
        const matchesSearch = m.method_name.toLowerCase().includes(term) || 
                             m.method_code.toLowerCase().includes(term) ||
                             (m.group && m.group.toLowerCase().includes(term));
        
        // Tab filter
        if (activeTab.value === 'All') return matchesSearch;
        
        if (activeTab.value === 'Other') {
            return matchesSearch && !['Virtual Account', 'E-Wallet', 'Convenience Store'].includes(m.group || '');
        }
        
        return matchesSearch && (m.group === activeTab.value);
    });
});

const formatCurrency = (val: number | null) => {
    if (val === null) return 'Unlimited';
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0
    }).format(val);
};

const getImageUrl = (imagePath: string | null) => {
    if (!imagePath) return null;
    if (imagePath.startsWith('http://') || imagePath.startsWith('https://') || imagePath.startsWith('/')) {
        return imagePath;
    }
    return `/` + imagePath;
};
</script>

<template>
    <Head title="Tarif & Biaya Layanan — JavaraPay">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="" />
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />
        <meta name="description" content="Informasi lengkap tarif dan biaya transaksi serta ketentuan pencairan dana di JavaraPay." />
    </Head>

    <div class="min-h-screen bg-slate-50 dark:bg-zinc-950 font-sans text-slate-800 dark:text-zinc-100 selection:bg-pink-100 dark:selection:bg-pink-900/50 selection:text-pink-900 dark:selection:text-pink-300">
        
        <!-- Navbar -->
        <nav class="sticky top-0 z-50 w-full border-b border-slate-200 dark:border-zinc-800 bg-white/90 dark:bg-zinc-950/90 backdrop-blur-md">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-6 lg:px-8">
                <a href="/" class="flex items-center gap-2 transition-opacity hover:opacity-80">
                   <img src="/javarapay-text-bg.png" class="w-40 rounded-full"/>
                </a>
                
                <div class="flex items-center gap-4 sm:gap-6">
                    <button 
                        @click="toggleTheme" 
                        class="p-2 mr-2 rounded-full text-slate-500 hover:bg-slate-100 hover:text-slate-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-white transition-colors"
                        aria-label="Toggle Dark Mode"
                    >
                        <Sun v-if="isDark" class="h-5 w-5" />
                        <Moon v-else class="h-5 w-5" />
                    </button>

                    <Link href="/fee" class="text-sm font-semibold text-pink-600 dark:text-pink-400">Biaya</Link>
                    <Link href="/docs" class="text-sm font-medium text-slate-600 dark:text-zinc-300 transition-colors hover:text-slate-900 dark:hover:text-white">Bantuan</Link>

                    <div class="h-5 w-px bg-slate-300 dark:bg-zinc-700 hidden sm:block"></div>
                    
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="inline-flex items-center justify-center rounded-lg bg-pink-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-all hover:bg-pink-700 hover:shadow"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link :href="route('login')" class="hidden text-sm font-medium text-slate-600 dark:text-zinc-300 transition-colors hover:text-slate-900 dark:hover:text-white sm:block">
                            Masuk
                        </Link>
                        <Link :href="route('register')" class="inline-flex items-center justify-center rounded-lg bg-pink-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-all hover:bg-pink-700 hover:shadow">
                            Daftar Gratis
                        </Link>
                    </template>
                </div>
            </div>
        </nav>

        <!-- Header Hero Section -->
        <header class="relative overflow-hidden py-16 lg:py-20 bg-white dark:bg-zinc-900 border-b border-slate-200 dark:border-zinc-800">
            <div class="absolute inset-y-0 right-0 -z-10 w-full max-w-7xl transform-gpu overflow-hidden blur-3xl opacity-20 dark:opacity-10 pointer-events-none">
                <div class="aspect-[1155/678] w-[72rem] bg-gradient-to-tr from-[#ff80b5] to-[#9089fc]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
            </div>

            <div class="mx-auto max-w-4xl px-6 text-center">
                <span class="inline-flex items-center gap-2 rounded-full border border-pink-100 dark:border-pink-500/20 bg-pink-50 dark:bg-pink-500/10 px-4 py-1.5 text-xs font-semibold text-pink-700 dark:text-pink-400 shadow-sm leading-6 uppercase tracking-wider">
                    Transparan & Terjangkau
                </span>
                <h1 class="mt-6 text-3xl sm:text-5xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-none">
                    Biaya Layanan & Pembayaran
                </h1>
                <p class="mt-4 text-base sm:text-lg text-slate-600 dark:text-zinc-400 max-w-2xl mx-auto leading-relaxed">
                    Sistem pemotongan tagihan yang transparan dihitung per transaksi sukses. Tanpa biaya pendaftaran, biaya bulanan, atau biaya integrasi.
                </p>
            </div>
        </header>

        <!-- Terms Summary Cards (Withdrawal Policy & Hold Period) -->
        <section class="max-w-7xl mx-auto px-6 lg:px-8 mt-12">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Settlement Hold Card -->
                <div class="relative overflow-hidden rounded-2xl border border-slate-200 dark:border-zinc-800 bg-white dark:bg-zinc-900/60 p-6 shadow-sm flex items-start gap-4 transition hover:shadow-md">
                    <div class="h-12 w-12 rounded-xl bg-pink-50 dark:bg-pink-950/40 flex items-center justify-center flex-shrink-0">
                        <Clock class="h-6 w-6 text-pink-600 dark:text-pink-400" />
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Penyelesaian Saldo (Settlement)</h3>
                        <p class="text-sm text-slate-600 dark:text-zinc-400 mt-2 leading-relaxed">
                            Saldo transaksi yang sukses akan masuk ke status <strong class="text-pink-600 dark:text-pink-400">tertunda (pending balance) selama 5 hari</strong> sebelum dipindahkan secara otomatis ke saldo utama yang dapat Anda cairkan (withdraw).
                        </p>
                    </div>
                </div>

                <!-- Withdrawal Terms Card -->
                <div class="relative overflow-hidden rounded-2xl border border-slate-200 dark:border-zinc-800 bg-white dark:bg-zinc-900/60 p-6 shadow-sm flex items-start gap-4 transition hover:shadow-md">
                    <div class="h-12 w-12 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 flex items-center justify-center flex-shrink-0">
                        <ArrowDownToLine class="h-6 w-6 text-indigo-600 dark:text-indigo-400" />
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Ketentuan Penarikan (Withdrawal)</h3>
                        <p class="text-sm text-slate-600 dark:text-zinc-400 mt-2 leading-relaxed">
                            Pencairan dana ke seluruh rekening bank di Indonesia dapat dilakukan dengan <strong class="text-indigo-600 dark:text-indigo-400">minimum Rp 50.000</strong> per penarikan, dikenakan biaya flat sebesar <strong class="text-indigo-600 dark:text-indigo-400">Rp 10.000</strong> per transaksi.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Payment Channel Table Section -->
        <main class="max-w-7xl mx-auto px-6 lg:px-8 py-12">
            <div class="flex flex-col gap-6">
                <!-- Filters & Search Toolbar -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <!-- Tab Filters -->
                    <div class="flex items-center gap-1.5 overflow-x-auto pb-2 md:pb-0 scrollbar-none">
                        <button 
                            v-for="tab in tabs" 
                            :key="tab.value"
                            @click="activeTab = tab.value"
                            :class="[
                                'px-4 py-2 text-xs sm:text-sm font-semibold rounded-xl transition whitespace-nowrap',
                                activeTab === tab.value 
                                    ? 'bg-pink-600 text-white shadow-md' 
                                    : 'bg-white hover:bg-slate-100 text-slate-600 border border-slate-200 dark:bg-zinc-900 dark:text-zinc-300 dark:border-zinc-800 dark:hover:bg-zinc-800'
                            ]"
                        >
                            {{ tab.name }}
                        </button>
                    </div>

                    <!-- Search Input -->
                    <div class="relative w-full md:max-w-sm">
                        <div class="pointer-events-none absolute inset-y-0 start-0 flex items-center ps-3">
                            <Search class="h-4 w-4 text-gray-400" />
                        </div>
                        <input 
                            type="text" 
                            v-model="search"
                            class="block w-full rounded-xl border border-slate-300 bg-white p-2.5 ps-10 text-xs sm:text-sm text-slate-900 focus:border-pink-500 focus:ring-pink-500 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white dark:placeholder-zinc-400 dark:focus:border-pink-500 dark:focus:ring-pink-500 shadow-sm" 
                            placeholder="Cari channel pembayaran..." 
                        />
                    </div>
                </div>

                <!-- Channels Table Card -->
                <div class="rounded-2xl border border-slate-200 bg-white dark:border-zinc-800 dark:bg-zinc-900/40 backdrop-blur-md overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs sm:text-sm text-slate-500 dark:text-zinc-400">
                            <thead class="bg-slate-50/70 text-[11px] sm:text-xs uppercase text-slate-700 dark:bg-zinc-800/40 dark:text-zinc-300 border-b border-slate-200 dark:border-zinc-800">
                                <tr>
                                    <th scope="col" class="px-6 py-4.5 font-bold">Metode Pembayaran</th>
                                    <th scope="col" class="px-6 py-4.5 font-bold">Grup</th>
                                    <th scope="col" class="px-6 py-4.5 font-bold">Biaya Flat (IDR)</th>
                                    <th scope="col" class="px-6 py-4.5 font-bold">Biaya Persen (%)</th>
                                    <th scope="col" class="px-6 py-4.5 font-bold">Limit Transaksi</th>
                                    <th scope="col" class="px-6 py-4.5 font-bold text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-zinc-800">
                                <tr v-for="method in filteredMethods" :key="method.id" class="bg-white hover:bg-slate-50/50 dark:bg-transparent dark:hover:bg-zinc-800/20 transition-colors">
                                    <!-- Logo and Name -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-8 w-11 flex-shrink-0 rounded-lg bg-gray-50 dark:bg-zinc-800 flex items-center justify-center p-1.5 overflow-hidden border border-slate-200 dark:border-zinc-700">
                                                <img 
                                                    v-if="method.image" 
                                                    :src="getImageUrl(method.image)" 
                                                    :alt="method.method_name" 
                                                    class="h-full w-full object-contain"
                                                />
                                                <ImageIcon v-else class="h-5 w-5 text-gray-400" />
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-900 dark:text-white">
                                                    {{ method.method_name }}
                                                </div>
                                                <div class="font-mono text-[10px] sm:text-xs text-slate-500 dark:text-zinc-500 mt-0.5">
                                                    {{ method.method_code }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <!-- Group -->
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center rounded-md bg-slate-100 dark:bg-zinc-800 px-2 py-0.5 text-xs font-semibold text-slate-800 dark:text-zinc-300">
                                            {{ method.group || 'Lainnya' }}
                                        </span>
                                    </td>
                                    <!-- Flat Fee -->
                                    <td class="px-6 py-4 font-semibold text-slate-900 dark:text-white">
                                        <span v-if="method.fee_flat > 0" class="flex items-center gap-1">
                                            {{ formatCurrency(method.fee_flat) }}
                                        </span>
                                        <span v-else class="text-emerald-600 dark:text-emerald-400 uppercase text-xs font-bold tracking-wide">Bebas</span>
                                    </td>
                                    <!-- Percentage Fee -->
                                    <td class="px-6 py-4 font-semibold text-slate-900 dark:text-white">
                                        <span v-if="Number(method.fee_percent) > 0" class="flex items-center gap-0.5">
                                            {{ method.fee_percent }}%
                                        </span>
                                        <span v-else class="text-emerald-600 dark:text-emerald-400 uppercase text-xs font-bold tracking-wide">Bebas</span>
                                    </td>
                                    <!-- Transaction Limits -->
                                    <td class="px-6 py-4 text-xs font-medium text-slate-900 dark:text-zinc-300 space-y-0.5">
                                        <div>Min: {{ formatCurrency(method.min_amount) }}</div>
                                        <div class="text-slate-500 dark:text-zinc-500">Max: {{ formatCurrency(method.max_amount) }}</div>
                                    </td>
                                    <!-- Active Status Indicator -->
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-400 px-2.5 py-0.5 text-xs font-semibold">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span>
                                            Tersedia
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="filteredMethods.length === 0">
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-zinc-500">
                                        Tidak ada channel pembayaran yang ditemukan.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>

        <!-- Call to Action Banner -->
        <section class="max-w-7xl mx-auto px-6 lg:px-8 pb-20">
            <div class="relative overflow-hidden rounded-3xl bg-pink-600 dark:bg-pink-700 px-6 py-12 text-center sm:px-12 shadow-xl">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-white">Mulai Terima Pembayaran Hari Ini</h2>
                <p class="mx-auto mt-4 max-w-xl text-sm sm:text-base text-pink-100 leading-relaxed">
                    Dapatkan akses ke seluruh channel pembayaran di atas instan setelah pendaftaran. Integrasikan toko online Anda dengan mudah.
                </p>
                <div class="mt-8 flex justify-center gap-4">
                    <Link 
                        v-if="!$page.props.auth.user"
                        href="/register" 
                        class="rounded-xl bg-white px-6 py-3 text-sm font-bold text-pink-700 hover:bg-slate-50 shadow-md transition-colors"
                    >
                        Daftar Gratis Sekarang
                    </Link>
                    <Link 
                        v-else
                        href="/dashboard" 
                        class="rounded-xl bg-white px-6 py-3 text-sm font-bold text-pink-700 hover:bg-slate-50 shadow-md transition-colors"
                    >
                        Masuk ke Dashboard
                    </Link>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-white dark:bg-zinc-950 py-10 border-t border-slate-200 dark:border-zinc-800">
            <div class="mx-auto max-w-7xl px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2 opacity-60 grayscale dark:opacity-80">
                    <svg viewBox="0 0 28 28" fill="none" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg"><rect width="28" height="28" rx="8" fill="#ec4899" /><path d="M8 8h4v8a4 4 0 01-4-4V8z" fill="#fff" opacity=".9"/><path d="M14 8h6v2h-4v4a4 4 0 01-2-3.5V8z" fill="#fff" opacity=".7"/></svg>
                    <span class="font-bold text-slate-800 dark:text-zinc-400">JavaraPay</span>
                </div>
                <p class="text-sm text-slate-500 dark:text-zinc-500">
                    &copy; {{ new Date().getFullYear() }} PT Jepara Solusi Teknologi. All rights reserved.
                </p>
            </div>
        </footer>

    </div>
</template>