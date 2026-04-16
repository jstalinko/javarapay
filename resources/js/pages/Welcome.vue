<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { useAppearance } from '@/composables/useAppearance';
import { 
    ChevronDown, 
    ChevronUp, 
    ArrowRight, 
    Check, 
    Zap, 
    Target, 
    DollarSign, 
    Wallet, 
    Shield,
    Moon,
    Sun
} from 'lucide-vue-next';

const { appearance, updateAppearance } = useAppearance();

const isDark = computed(() => appearance.value === 'dark');

const toggleTheme = () => {
    updateAppearance(isDark.value ? 'light' : 'dark');
};

const openFaq = ref<number | null>(null);

function toggleFaq(index: number) {
    openFaq.value = openFaq.value === index ? null : index;
}

const features = [
    {
        icon: Zap,
        title: 'Aktivasi Tanpa Ribet',
        desc: 'Daftar sekarang, Anda bisa langsung menerima pembayaran hari ini juga. Tak perlu proses validasi yang memakan waktu lama.',
    },
    {
        icon: Target,
        title: 'Siap Pakai Kapan Saja',
        desc: 'Bagikan link pembayaran langsung via WhatsApp, Instagram, atau jadikan sebagai alat transaksi di website pribadi Anda.',
    },
    {
        icon: DollarSign,
        title: 'Biaya yang Transparan',
        desc: 'Setiap potongan transaksi kami tampilkan dengan jelas di awal. Tanpa embel-embel atau biaya langganan bulanan.',
    },
    {
        icon: Wallet,
        title: 'Pencairan Praktis & Cepat',
        desc: 'Tarik pendapatan Anda ke rekening bank tujuan kapan saja, nominalnya akan langsung masuk dengan cepat.',
    },
    {
        icon: Shield,
        title: 'Aman untuk Semua Pihak',
        desc: 'Mulai dari individu, freelancer, hingga pemilik bisnis dapat bertransaksi aman tanpa dipersulit oleh dokumen badan usaha.',
    },
];

const faqs = [
    {
        q: 'Mengapa JavaraPay?',
        a: 'JavaraPay adalah layanan payment link yang memudahkan Anda menerima pembayaran online. Cukup buat link pembayaran, bagikan ke pelanggan, dan terima uang langsung ke akun Anda.',
    },
    {
        q: 'Berapa biaya JavaraPay?',
        a: 'Biaya transaksi JavaraPay sangat kompetitif dan bervariasi tergantung metode pembayaran yang digunakan pelanggan. Tidak ada biaya bulanan atau biaya tersembunyi. cek disini ( /fee )',
    },
    {
        q: 'Bagaimana cara menarik dana?',
        a: 'Penarikan dana di lakukan secara otomatis dan manual, Fee penarikan bervariasi tergantung status akun. Minimum penarikan Rp 50.000 bisa di lakukan setiap hari meski pada hari libur. Waktu penarikan cepat 5-20 Menit.',
    },
    {
        q: 'Apakah JavaraPay aman?',
        a: 'Ya. JavaraPay di bawah PT JEPARA SOLUSI TEKNOLOGI menggunakan enkripsi standar industri dan bekerja sama dengan payment gateway terpercaya untuk memastikan setiap transaksi aman dan terlindungi.',
    },
    {
        q: 'Metode pembayaran apa saja yang di dukung',
        a: 'JavaraPay mendukung berbagai metode pembayaran termasuk transfer bank,virtual account, e-wallet (GoPay, OVO, DANA, ShopeePay), QRIS, kartu kredit/debit, dan minimarket.',
    },
    {
        q: 'Apakah saya perlu KYC atau dokumen usaha?',
        a: 'Tidak. JavaraPay dapat digunakan oleh siapa saja — individu, freelancer, maupun bisnis — tanpa perlu KYC atau verifikasi badan usaha.',
    },
];
</script>

<template>
    <Head title="JavaraPay — Layanan Pembayaran Link Terpercaya">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="" />
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />
        <meta name="description" content="JavaraPay adalah layanan payment link yang memudahkan Anda menerima pembayaran online untuk keperluan apa saja tanpa repot mengurus sistem." />
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

                    <Link href="/fee" class="text-sm font-medium text-slate-600 dark:text-zinc-300 transition-colors hover:text-slate-900 dark:hover:text-white">Biaya</Link>
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

        <!-- Hero Section -->
        <main class="relative isolate pt-14 pb-20 sm:pt-24 lg:pb-32 overflow-hidden">
            <!-- Gentle Background Gradient -->
            <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80" aria-hidden="true">
                <div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-20 dark:opacity-10 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
            </div>

            <div class="mx-auto max-w-4xl px-6 lg:px-8 text-center pt-8">
                <span class="inline-flex items-center gap-2 rounded-full border border-pink-100 dark:border-pink-500/20 bg-pink-50 dark:bg-pink-500/10 px-4 py-1.5 text-sm font-semibold text-pink-700 dark:text-pink-400 shadow-sm leading-6">
                    Solusi Pembayaran Modern
                </span>
                
                <h1 class="mt-8 text-4xl sm:text-6xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-[1.1]">
                    Terima Pembayaran <br class="hidden sm:block" /> 
                    <span class="text-pink-600 dark:text-pink-400">Lebih Mudah & Dekat</span>
                </h1>
                
                <p class="mx-auto mt-6 max-w-2xl text-lg sm:text-xl text-slate-600 dark:text-zinc-400 leading-relaxed">
                    Ubah cara Anda bertransaksi dengan rekan dan pelanggan. Buat link pembayaran, kirim, lalu biarkan uang masuk ke rekening Anda tanpa proses teknis yang memusingkan.
                </p>

                <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-6">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="group inline-flex items-center justify-center w-full sm:w-auto rounded-xl bg-pink-600 px-6 py-3.5 text-base font-semibold text-white shadow-md transition-all hover:bg-pink-700 hover:shadow-lg focus:ring-4 focus:ring-pink-100 dark:focus:ring-pink-900"
                    >
                        Ke Dashboard Sekarang
                        <ArrowRight class="ml-2 h-4 w-4 transition-transform group-hover:translate-x-1" />
                    </Link>
                    <Link
                        v-else
                        :href="route('register')"
                        class="group inline-flex items-center justify-center w-full sm:w-auto rounded-xl bg-pink-600 px-6 py-3.5 text-base font-semibold text-white shadow-md transition-all hover:bg-pink-700 hover:shadow-lg focus:ring-4 focus:ring-pink-100 dark:focus:ring-pink-900"
                    >
                        Mulai Coba — Gratis
                        <ArrowRight class="ml-2 h-4 w-4 transition-transform group-hover:translate-x-1" />
                    </Link>
                    <Link
                        v-if="!$page.props.auth.user"
                        :href="route('login')"
                        class="w-full sm:w-auto text-base font-semibold leading-6 text-slate-700 dark:text-zinc-300 px-4 py-3 hover:text-pink-600 dark:hover:text-pink-400 transition-colors"
                    >
                        Sudah punya akun? Masuk
                    </Link>
                </div>
            </div>
            
            <div class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]" aria-hidden="true">
                <div class="relative left-[calc(50%+3rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-20 dark:opacity-10 sm:left-[calc(50%+36rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
            </div>
        </main>

        <!-- Brief Features Divider -->
        <div class="border-y border-slate-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 py-12">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-8 sm:grid-cols-3 text-center sm:text-left divide-y sm:divide-y-0 sm:divide-x divide-slate-200 dark:divide-zinc-800">
                    <div class="flex flex-col items-center sm:items-start pt-6 sm:pt-0 sm:pr-8">
                        <span class="text-3xl font-extrabold text-slate-900 dark:text-white">100%</span>
                        <span class="mt-2 text-sm font-medium text-slate-500 dark:text-zinc-400 uppercase tracking-wide">Gratis Pendaftaran</span>
                    </div>
                    <div class="flex flex-col items-center sm:items-start pt-6 sm:pt-0 sm:px-8 leading-6">
                        <span class="text-3xl font-extrabold text-slate-900 dark:text-white">&lt; 2<span class="text-lg">mnt</span></span>
                        <span class="mt-2 text-sm font-medium text-slate-500 dark:text-zinc-400 uppercase tracking-wide">Waktu Buka Akun</span>
                    </div>
                    <div class="flex flex-col items-center sm:items-start pt-6 sm:pt-0 sm:pl-8">
                        <span class="text-3xl font-extrabold text-slate-900 dark:text-white">24/7</span>
                        <span class="mt-2 text-sm font-medium text-slate-500 dark:text-zinc-400 uppercase tracking-wide">Dana Bisa Ditarik</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Matrix -->
        <section class="py-24 bg-slate-50 dark:bg-zinc-950" id="features">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center">
                    <h2 class="text-xl font-bold leading-7 text-pink-600 dark:text-pink-400">Mudah Digunakan</h2>
                    <p class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white sm:text-4xl">Sistem yang Ramah Pengguna</p>
                    <p class="mt-4 text-lg leading-8 text-slate-600 dark:text-zinc-400">
                        Kami menjauhkan segala hal teknis yang rumit sehingga Anda dapat sepenuhnya fokus pada bisnis dan pelanggan yang Anda layani.
                    </p>
                </div>
                <div class="mx-auto mt-16 max-w-5xl lg:mt-20">
                    <div class="grid grid-cols-1 gap-12 sm:grid-cols-2 lg:grid-cols-3">
                        <div v-for="(feat, i) in features" :key="i" class="flex flex-col items-start">
                            <div class="rounded-xl bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 shadow-sm p-3 mb-5 flex items-center justify-center">
                                <component :is="feat.icon" class="h-6 w-6 text-pink-600 dark:text-pink-400" />
                            </div>
                            <h3 class="text-lg font-bold leading-7 text-slate-900 dark:text-white">{{ feat.title }}</h3>
                            <p class="mt-2 text-base leading-7 text-slate-600 dark:text-zinc-400">{{ feat.desc }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Steps Section -->
        <section class="py-24 bg-white dark:bg-zinc-900 border-y border-slate-100 dark:border-zinc-800">
            <div class="mx-auto max-w-7xl px-6 lg:px-8 text-center">
                <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">Bagaimana Cara Kerjanya?</h2>
                <p class="mt-4 text-lg text-slate-600 dark:text-zinc-400 max-w-2xl mx-auto">Kami membuatnya sejelas mungkin untuk Anda dan pelanggan.</p>
                
                <div class="mt-16 grid grid-cols-1 gap-y-12 sm:grid-cols-3 sm:gap-x-12 relative">
                    <!-- Step 1 -->
                    <div class="relative flex flex-col items-center">
                        <div class="z-10 flex h-14 w-14 items-center justify-center rounded-full bg-pink-50 dark:bg-pink-500/10 border-2 border-pink-200 dark:border-pink-500/20 text-xl font-bold text-pink-600 dark:text-pink-400 shadow-sm">1</div>
                        <h3 class="mt-6 text-lg font-bold text-slate-900 dark:text-white">Daftar Tanpa Syarat</h3>
                        <p class="mt-2 text-slate-600 dark:text-zinc-400">Buat akun dengan gampang, cukup bermodalkan email aktif.</p>
                    </div>
                    <!-- Step 2 -->
                    <div class="relative flex flex-col items-center">
                        <div class="z-10 flex h-14 w-14 items-center justify-center rounded-full bg-pink-50 dark:bg-pink-500/10 border-2 border-pink-200 dark:border-pink-500/20 text-xl font-bold text-pink-600 dark:text-pink-400 shadow-sm">2</div>
                        <h3 class="mt-6 text-lg font-bold text-slate-900 dark:text-white">Buat Link Sekali Klik</h3>
                        <p class="mt-2 text-slate-600 dark:text-zinc-400">Isi besaran form tagihan, lalu Anda akan langsung dapat link.</p>
                    </div>
                    <!-- Step 3 -->
                    <div class="relative flex flex-col items-center">
                        <div class="z-10 flex h-14 w-14 items-center justify-center rounded-full bg-pink-50 dark:bg-pink-500/10 border-2 border-pink-200 dark:border-pink-500/20 text-xl font-bold text-pink-600 dark:text-pink-400 shadow-sm">3</div>
                        <h3 class="mt-6 text-lg font-bold text-slate-900 dark:text-white">Uang Langsung Cair</h3>
                        <p class="mt-2 text-slate-600 dark:text-zinc-400">Ketika pelanggan membayar, dana otomatis masuk dan bisa dicek real-time.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="py-24 bg-slate-50 dark:bg-zinc-950" id="faq">
            <div class="mx-auto max-w-4xl px-6 lg:px-8">
                <div class="text-center mb-14">
                    <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">Hal yang Sering Ditanyakan</h2>
                </div>
                
                <div class="space-y-4">
                    <div 
                        v-for="(faq, i) in faqs" 
                        :key="i"
                        class="overflow-hidden rounded-2xl bg-white dark:bg-zinc-900 border shadow-sm transition-all duration-200"
                        :class="openFaq === i ? 'ring-2 ring-pink-500 border-transparent dark:border-transparent shadow-md' : 'border-slate-200 dark:border-zinc-800 hover:border-slate-300 dark:hover:border-zinc-700'"
                    >
                        <button 
                            @click="toggleFaq(i)"
                            class="flex w-full items-center justify-between px-6 py-5 text-left focus:outline-none"
                        >
                            <span class="text-[1.05rem] font-bold text-slate-900 dark:text-white">{{ faq.q }}</span>
                            <span class="ml-6 flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full bg-slate-100 dark:bg-zinc-800 text-slate-500 dark:text-zinc-400">
                                <ChevronDown v-if="openFaq !== i" class="h-4 w-4" />
                                <ChevronUp v-else class="h-4 w-4" />
                            </span>
                        </button>
                        <div 
                            v-show="openFaq === i"
                            class="px-6 pb-6 pt-0"
                        >
                            <p class="text-slate-600 dark:text-zinc-400 leading-relaxed">{{ faq.a }}</p>
                            <!-- If linking to fee explicitly from the text mentioned -->
                            <div v-if="faq.a.includes('(/fee)') || faq.a.includes('( /fee )')" class="mt-3">
                                <Link href="/fee" class="text-pink-600 dark:text-pink-400 hover:text-pink-700 dark:hover:text-pink-300 font-semibold text-sm flex items-center gap-1">
                                    Cek simulasi biaya di sini <ArrowRight class="h-4 w-4" />
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pre-footer CTA -->
        <div class="bg-pink-600 dark:bg-pink-700">
            <div class="mx-auto max-w-7xl px-6 py-16 sm:py-20 lg:px-8 lg:py-24 text-center">
                <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                    <span class="block">Masih mencari layanan yang tepat?</span>
                    <span class="block mt-2 opacity-90 text-2xl font-medium">Buktikan kemudahan JavaraPay sekarang juga.</span>
                </h2>
                <div class="mt-10 flex items-center justify-center gap-4">
                    <Link
                        v-if="!$page.props.auth.user"
                        :href="route('register')"
                        class="inline-flex items-center justify-center rounded-xl bg-white dark:bg-zinc-900 px-8 py-3.5 text-base font-bold text-pink-700 dark:text-white shadow-md transition-colors hover:bg-slate-50 dark:hover:bg-zinc-800"
                    >
                        Buat Akun Anda
                    </Link>
                    <Link
                        v-else
                        :href="route('dashboard')"
                        class="inline-flex items-center justify-center rounded-xl bg-white dark:bg-zinc-900 px-8 py-3.5 text-base font-bold text-pink-700 dark:text-white shadow-md transition-colors hover:bg-slate-50 dark:hover:bg-zinc-800"
                    >
                        Alat Pembayaran
                    </Link>
                </div>
            </div>
        </div>

        <!-- Minimal Footer -->
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
