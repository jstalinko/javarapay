<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { useAppearance } from '@/composables/useAppearance';
import {
    BookOpen,
    Zap,
    Link2,
    Code2,
    Copy,
    Check,
    ChevronRight,
    Server,
    Globe,
    Webhook,
    ArrowRight,
    Terminal,
    FileJson,
    AlertCircle,
    Hash,
    Moon,
    Sun
} from 'lucide-vue-next';
import { toast } from 'vue-sonner';

const { appearance, updateAppearance } = useAppearance();

const isDark = computed(() => appearance.value === 'dark');

const toggleTheme = () => {
    updateAppearance(isDark.value ? 'light' : 'dark');
};

// Copy to clipboard state per-code block
const copiedId = ref<string | null>(null);

const copyToClipboard = (text: string, id: string) => {
    navigator.clipboard.writeText(text).then(() => {
        copiedId.value = id;
        toast.success('Disalin ke clipboard!');
        setTimeout(() => {
            copiedId.value = null;
        }, 2000);
    });
};

// Active section tracking for sidebar
const activeSection = ref('pengenalan');

const sections = [
    { id: 'pengenalan', label: 'Pengenalan', icon: BookOpen },
    { id: 'persiapan', label: 'Persiapan', icon: Zap },
    { id: 'payment-links', label: 'Payment Links', icon: Link2 },
    { id: 'api-integration', label: 'Integrasi via API', icon: Code2 },
    { id: 'payment-channels', label: 'Channel Pembayaran', icon: Server, indent: true },
    { id: 'create-transaction', label: 'Buat Transaksi', icon: FileJson, indent: true },
];

const scrollToSection = (id: string) => {
    const el = document.getElementById(id);
    if (el) {
        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    activeSection.value = id;
};

const handleScroll = () => {
    const els = sections.map(s => document.getElementById(s.id)).filter(Boolean) as HTMLElement[];
    for (let i = els.length - 1; i >= 0; i--) {
        if (els[i].getBoundingClientRect().top <= 120) {
            activeSection.value = sections[i].id;
            break;
        }
    }
};

onMounted(() => window.addEventListener('scroll', handleScroll));
onUnmounted(() => window.removeEventListener('scroll', handleScroll));

// --- Code Snippets ---
const apiBase = 'https://pay.javara.digital/api/';
const channelEndpoint = 'https://pay.javara.digital/api/channel';
const createEndpoint = 'https://pay.javara.digital/api/transaction/create';

const activeTabChannel = ref('curl');
const activeTabCreate = ref('curl');

const channelCurl = `curl -X GET "${channelEndpoint}" \\
  -H "X-JAVARAPAY-API: YOUR_API_KEY" \\
  -H "X-JAVARAPAY-MERCHANT-CODE: YOUR_MERCHANT_CODE"`;

const channelPhp = `<?php
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => '${channelEndpoint}',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'X-JAVARAPAY-API: YOUR_API_KEY',
    'X-JAVARAPAY-MERCHANT-CODE: YOUR_MERCHANT_CODE'
  ),
));

$response = curl_exec($curl);
curl_close($curl);
echo $response;`;

const channelNode = `const axios = require('axios');

let config = {
  method: 'get',
  url: '${channelEndpoint}',
  headers: { 
    'X-JAVARAPAY-API': 'YOUR_API_KEY', 
    'X-JAVARAPAY-MERCHANT-CODE': 'YOUR_MERCHANT_CODE'
  }
};

axios.request(config)
.then((response) => {
  console.log(JSON.stringify(response.data));
})
.catch((error) => {
  console.log(error);
});`;

const createBody = `{
  "method_code": "qris",
  "merchant_ref": "INV-001",
  "amount": 10000,
  "customer_name": "Budi",
  "customer_email": "budi@example.com",
  "customer_phone": "08123456789",
  "notes": "Pembayaran invoice INV-001"
}`;

const createCurl = `curl -X POST "${createEndpoint}" \\
  -H "X-JAVARAPAY-API: YOUR_API_KEY" \\
  -H "X-JAVARAPAY-MERCHANT-CODE: YOUR_MERCHANT_CODE" \\
  -H "Content-Type: application/json" \\
  -d '${createBody}'`;

const createPhp = `<?php
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => '${createEndpoint}',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => '${createBody.replace(/\n/g, '\n  ')}',
  CURLOPT_HTTPHEADER => array(
    'X-JAVARAPAY-API: YOUR_API_KEY',
    'X-JAVARAPAY-MERCHANT-CODE: YOUR_MERCHANT_CODE',
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);
curl_close($curl);
echo $response;`;

const createNode = `const axios = require('axios');

let data = JSON.stringify(${createBody.replace(/\n/g, '\n  ')});

let config = {
  method: 'post',
  maxBodyLength: Infinity,
  url: '${createEndpoint}',
  headers: { 
    'X-JAVARAPAY-API': 'YOUR_API_KEY', 
    'X-JAVARAPAY-MERCHANT-CODE': 'YOUR_MERCHANT_CODE', 
    'Content-Type': 'application/json'
  },
  data: data
};

axios.request(config)
.then((response) => {
  console.log(JSON.stringify(response.data));
})
.catch((error) => {
  console.log(error);
});`;
</script>

<template>
    <Head title="Dokumentasi — JavaraPay" />

    <div class="min-h-screen bg-slate-50 dark:bg-zinc-950 font-sans text-slate-800 dark:text-zinc-100 selection:bg-indigo-100 dark:selection:bg-indigo-900/50 selection:text-indigo-900 dark:selection:text-indigo-300">
        
        <!-- Navbar from Welcome.vue -->
        <nav class="sticky top-0 z-50 w-full border-b border-slate-200 dark:border-zinc-800 bg-white/90 dark:bg-zinc-950/90 backdrop-blur-md">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-6 lg:px-8">
                <a href="/" class="flex items-center gap-2 transition-opacity hover:opacity-80">
                    <svg viewBox="0 0 28 28" fill="none" class="h-8 w-8" xmlns="http://www.w3.org/2000/svg">
                        <rect width="28" height="28" rx="8" fill="#6366f1" />
                        <path d="M8 8h4v8a4 4 0 01-4-4V8z" fill="#fff" opacity=".9"/>
                        <path d="M14 8h6v2h-4v4a4 4 0 01-2-3.5V8z" fill="#fff" opacity=".7"/>
                    </svg>
                    <span class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">JavaraPay</span>
                    <span class="ml-2 rounded-full border border-indigo-200 dark:border-indigo-500/30 bg-indigo-50 dark:bg-indigo-500/10 px-2 py-0.5 text-[11px] font-semibold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Docs</span>
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
                        class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-all hover:bg-indigo-700 hover:shadow"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link :href="route('login')" class="hidden text-sm font-medium text-slate-600 dark:text-zinc-300 transition-colors hover:text-slate-900 dark:hover:text-white sm:block">
                            Masuk
                        </Link>
                        <Link :href="route('register')" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-all hover:bg-indigo-700 hover:shadow">
                            Daftar Gratis
                        </Link>
                    </template>
                </div>
            </div>
        </nav>

        <div class="mx-auto flex max-w-7xl gap-8 px-6 pt-12 pb-20">
            <!-- Sidebar -->
            <aside class="hidden w-60 shrink-0 lg:block">
                <div class="sticky top-24">
                    <p class="mb-3 text-[11px] font-bold uppercase tracking-widest text-slate-500 dark:text-zinc-500">Navigasi</p>
                    <nav class="flex flex-col gap-0.5">
                        <button
                            v-for="s in sections"
                            :key="s.id"
                            @click="scrollToSection(s.id)"
                            :class="[
                                'group flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left text-sm font-medium transition-all duration-150',
                                s.indent ? 'ml-4 w-[calc(100%-1rem)]' : '',
                                activeSection === s.id
                                    ? 'bg-indigo-100 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400'
                                    : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-200 dark:hover:bg-zinc-800 hover:text-slate-900 dark:hover:text-white'
                            ]"
                        >
                            <component :is="s.icon" :class="['h-3.5 w-3.5 shrink-0', activeSection === s.id ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-zinc-500 group-hover:text-slate-600 dark:group-hover:text-zinc-300']" />
                            {{ s.label }}
                            <ChevronRight v-if="activeSection === s.id" class="ml-auto h-3 w-3 text-indigo-600 dark:text-indigo-400" />
                        </button>
                    </nav>

                    <!-- Quick Info Card -->
                    <div class="mt-6 rounded-xl border border-amber-200 dark:border-amber-500/20 bg-amber-50 dark:bg-amber-500/5 p-4">
                        <div class="mb-2 flex items-center gap-2">
                            <AlertCircle class="h-3.5 w-3.5 text-amber-600 dark:text-amber-400" />
                            <span class="text-[11px] font-bold uppercase tracking-wider text-amber-700 dark:text-amber-400">Catatan</span>
                        </div>
                        <p class="text-[12px] leading-relaxed text-amber-800/80 dark:text-amber-200/50">
                            Aktifkan Webhook URL pada project Anda untuk menerima notifikasi transaksi otomatis.
                        </p>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="min-w-0 flex-1">
                <div class="flex max-w-3xl flex-col gap-16">

                    <!-- ─── PENGENALAN ─── -->
                    <section id="pengenalan" class="scroll-mt-24">
                        <div class="mb-6 flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-100 to-indigo-200 dark:from-indigo-400/20 dark:to-indigo-600/20 ring-1 ring-indigo-200 dark:ring-indigo-500/30">
                                <BookOpen class="h-5 w-5 text-indigo-600 dark:text-indigo-400" />
                            </div>
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-widest text-indigo-600 dark:text-indigo-500">01 — Getting Started</p>
                                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Pengenalan</h1>
                            </div>
                        </div>
                        <div class="rounded-2xl border border-slate-200 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 p-6 shadow-sm dark:shadow-none">
                            <p class="text-base leading-relaxed text-slate-600 dark:text-zinc-400">
                                <span class="font-semibold text-slate-900 dark:text-white">JavaraPay</span> adalah jembatan pembayaran digital
                                <span class="rounded bg-indigo-50 dark:bg-indigo-500/10 px-1.5 py-0.5 font-mono text-xs font-medium text-indigo-700 dark:text-indigo-400">Payment Integrator</span>
                                yang memudahkan merchant untuk menerima pembayaran dari pelanggan.
                            </p>
                            <p class="mt-4 text-base leading-relaxed text-slate-600 dark:text-zinc-400">
                                Dengan JavaraPay, Anda dapat membuat payment link, mengintegrasikan via API, dan menerima notifikasi webhook — semua dalam satu platform yang mudah digunakan.
                            </p>

                            <!-- Feature Grid -->
                            <div class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-3">
                                <div class="rounded-xl border border-slate-200 dark:border-zinc-800 bg-slate-50 dark:bg-zinc-900 p-4">
                                    <Link2 class="mb-2 h-5 w-5 text-blue-500 dark:text-blue-400" />
                                    <h3 class="text-sm font-semibold text-slate-800 dark:text-white">Payment Links</h3>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-zinc-500">Buat & bagikan link pembayaran dalam hitungan detik.</p>
                                </div>
                                <div class="rounded-xl border border-slate-200 dark:border-zinc-800 bg-slate-50 dark:bg-zinc-900 p-4">
                                    <Code2 class="mb-2 h-5 w-5 text-violet-500 dark:text-violet-400" />
                                    <h3 class="text-sm font-semibold text-slate-800 dark:text-white">REST API</h3>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-zinc-500">Integrasi langsung dengan API untuk sistem Anda.</p>
                                </div>
                                <div class="rounded-xl border border-slate-200 dark:border-zinc-800 bg-slate-50 dark:bg-zinc-900 p-4">
                                    <Webhook class="mb-2 h-5 w-5 text-orange-500 dark:text-orange-400" />
                                    <h3 class="text-sm font-semibold text-slate-800 dark:text-white">Webhook</h3>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-zinc-500">Terima notifikasi real-time saat pembayaran berhasil.</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- ─── PERSIAPAN ─── -->
                    <section id="persiapan" class="scroll-mt-24">
                        <div class="mb-6 flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-400/20 dark:to-indigo-600/20 ring-1 ring-blue-200 dark:ring-blue-500/30">
                                <Zap class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                            </div>
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-widest text-blue-600 dark:text-blue-400">02 — Setup</p>
                                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Persiapan</h2>
                            </div>
                        </div>

                        <p class="mb-5 text-slate-600 dark:text-zinc-400">Sebelum menggunakan JavaraPay, Anda perlu mendaftar dan membuat sebuah project.</p>

                        <!-- Steps -->
                        <div class="flex flex-col gap-4">
                            <div class="flex gap-4 rounded-xl border border-slate-200 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 p-5 shadow-sm dark:shadow-none">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-500/20 text-sm font-bold text-blue-600 dark:text-blue-400">1</div>
                                <div>
                                    <h3 class="font-semibold text-slate-800 dark:text-white">Daftar ke JavaraPay</h3>
                                    <p class="mt-1 text-sm text-slate-600 dark:text-zinc-400">Buat akun baru di <a href="https://pay.javara.digital" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:underline">pay.javara.digital</a> untuk mendapatkan akses ke dashboard merchant.</p>
                                </div>
                            </div>
                            <div class="flex gap-4 rounded-xl border border-slate-200 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 p-5 shadow-sm dark:shadow-none">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-500/20 text-sm font-bold text-blue-600 dark:text-blue-400">2</div>
                                <div>
                                    <h3 class="font-semibold text-slate-800 dark:text-white">Buat Project</h3>
                                    <p class="mt-1 text-sm text-slate-600 dark:text-zinc-400">Project adalah identitas merchant Anda di JavaraPay. Setiap project memiliki <span class="font-mono text-xs text-slate-700 dark:text-zinc-300">API Key</span> dan <span class="font-mono text-xs text-slate-700 dark:text-zinc-300">Merchant Code</span> yang unik.</p>
                                </div>
                            </div>
                            <div class="flex gap-4 rounded-xl border border-slate-200 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 p-5 shadow-sm dark:shadow-none">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-500/20 text-sm font-bold text-blue-600 dark:text-blue-400">3</div>
                                <div>
                                    <h3 class="font-semibold text-slate-800 dark:text-white">Salin Kredensial</h3>
                                    <p class="mt-1 text-sm text-slate-600 dark:text-zinc-400">Buka detail project dan salin <span class="font-mono text-xs text-slate-700 dark:text-zinc-300">API Key</span> serta <span class="font-mono text-xs text-slate-700 dark:text-zinc-300">Merchant Code</span> — Anda akan membutuhkannya di setiap request API.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Project Info Box -->
                        <div class="mt-5 rounded-xl border border-blue-200 dark:border-blue-500/20 bg-blue-50 dark:bg-blue-500/5 p-5">
                            <div class="flex items-start gap-3">
                                <Hash class="mt-0.5 h-4 w-4 shrink-0 text-blue-600 dark:text-blue-400" />
                                <div>
                                    <h4 class="text-sm font-semibold text-blue-700 dark:text-blue-300">Tentang Project</h4>
                                    <p class="mt-1 text-[13px] leading-relaxed text-blue-800/80 dark:text-blue-200/60">
                                        Project adalah merchant Anda yang terdaftar di JavaraPay. Dengan mendaftarkan project, Anda akan mendapatkan <strong class="text-slate-800 dark:text-zinc-300">API Key</strong> dan <strong class="text-slate-800 dark:text-zinc-300">Secret Key</strong> yang digunakan untuk autentikasi saat menggunakan API JavaraPay.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- ─── PAYMENT LINKS ─── -->
                    <section id="payment-links" class="scroll-mt-24">
                        <div class="mb-6 flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-violet-100 to-purple-200 dark:from-violet-400/20 dark:to-purple-600/20 ring-1 ring-violet-200 dark:ring-violet-500/30">
                                <Link2 class="h-5 w-5 text-violet-600 dark:text-violet-400" />
                            </div>
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-widest text-violet-600 dark:text-violet-400">03 — No Code</p>
                                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Integrasi via Payment Links</h2>
                            </div>
                        </div>
                        <p class="mb-5 text-slate-600 dark:text-zinc-400">
                            Cara tercepat untuk mulai menerima pembayaran — tanpa perlu koding sama sekali.
                        </p>

                        <div class="rounded-2xl border border-slate-200 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 shadow-sm dark:shadow-none overflow-hidden">
                            <div class="border-b border-slate-200 dark:border-zinc-800 bg-slate-50 dark:bg-zinc-900 px-5 py-3">
                                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-zinc-500">Langkah-langkah</span>
                            </div>
                            <ol class="flex flex-col divide-y divide-slate-200 dark:divide-zinc-800">
                                <li class="flex items-start gap-4 p-5">
                                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-violet-100 dark:bg-violet-500/20 text-xs font-bold text-violet-600 dark:text-violet-400">1</span>
                                    <div>
                                        <p class="text-sm font-medium text-slate-800 dark:text-zinc-200">Klik <span class="rounded bg-violet-100 dark:bg-violet-500/20 px-2 py-0.5 font-mono text-xs font-semibold text-violet-700 dark:text-violet-300">"Create Payment Links"</span> di dashboard JavaraPay.</p>
                                    </div>
                                </li>
                                <li class="flex items-start gap-4 p-5">
                                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-violet-100 dark:bg-violet-500/20 text-xs font-bold text-violet-600 dark:text-violet-400">2</span>
                                    <div>
                                        <p class="text-sm font-medium text-slate-800 dark:text-zinc-200">Masukkan <strong>jumlah nominal</strong> yang ingin ditagihkan.</p>
                                    </div>
                                </li>
                                <li class="flex items-start gap-4 p-5">
                                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-violet-100 dark:bg-violet-500/20 text-xs font-bold text-violet-600 dark:text-violet-400">3</span>
                                    <div>
                                        <p class="text-sm font-medium text-slate-800 dark:text-zinc-200">Masukkan <strong>catatan (Notes)</strong> jika diperlukan.</p>
                                        <p class="mt-1 text-xs text-slate-500 dark:text-zinc-400">Contoh: "Pembayaran Invoice INV-001"</p>
                                    </div>
                                </li>
                                <li class="flex items-start gap-4 p-5">
                                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-violet-100 dark:bg-violet-500/20 text-xs font-bold text-violet-600 dark:text-violet-400">4</span>
                                    <div>
                                        <p class="text-sm font-medium text-slate-800 dark:text-zinc-200">Link pembayaran siap untuk dibagikan kepada client Anda. ✅</p>
                                    </div>
                                </li>
                            </ol>
                        </div>

                        <!-- Webhook Note -->
                        <div class="mt-4 flex items-start gap-3 rounded-xl border border-orange-200 dark:border-orange-500/20 bg-orange-50 dark:bg-orange-500/5 p-4">
                            <Webhook class="mt-0.5 h-4 w-4 shrink-0 text-orange-500 dark:text-orange-400" />
                            <p class="text-[13px] leading-relaxed text-orange-800/80 dark:text-orange-200/60">
                                <strong class="text-orange-700 dark:text-orange-300">Webhook:</strong> Jika Anda mengatur <span class="font-mono text-xs text-slate-700 dark:text-zinc-300">Webhook URL</span> pada project Anda, JavaraPay akan mengirimkan notifikasi ke URL tersebut saat pembayaran berhasil.
                            </p>
                        </div>
                    </section>

                    <!-- ─── API BASE ─── -->
                    <section id="api-integration" class="scroll-mt-24">
                        <div class="mb-6 flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-rose-100 to-red-200 dark:from-rose-400/20 dark:to-red-600/20 ring-1 ring-rose-200 dark:ring-rose-500/30">
                                <Code2 class="h-5 w-5 text-rose-600 dark:text-rose-400" />
                            </div>
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-widest text-rose-600 dark:text-rose-400">04 — Developer</p>
                                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Integrasi via API</h2>
                            </div>
                        </div>
                        <p class="mb-5 text-slate-600 dark:text-zinc-400">Integrasikan JavaraPay langsung ke dalam aplikasi atau sistem Anda menggunakan REST API.</p>

                        <!-- Base URL -->
                        <div class="rounded-xl border border-slate-200 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 shadow-sm dark:shadow-none overflow-hidden">
                            <div class="flex items-center justify-between border-b border-slate-200 dark:border-zinc-800 bg-slate-50 dark:bg-zinc-900 px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <Globe class="h-3.5 w-3.5 text-slate-500 dark:text-zinc-400" />
                                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">Base URL</span>
                                </div>
                                <button
                                    @click="copyToClipboard(apiBase, 'base-url')"
                                    class="flex items-center gap-1.5 rounded-md border border-slate-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-2 py-1 text-[11px] font-medium text-slate-600 dark:text-zinc-400 transition-all hover:border-slate-300 dark:hover:border-zinc-600 hover:text-slate-900 dark:hover:text-zinc-200 shadow-sm"
                                >
                                    <component :is="copiedId === 'base-url' ? Check : Copy" class="h-3 w-3" :class="copiedId === 'base-url' ? 'text-indigo-600 dark:text-indigo-400' : ''" />
                                    {{ copiedId === 'base-url' ? 'Disalin!' : 'Salin' }}
                                </button>
                            </div>
                            <div class="p-4">
                                <code class="font-mono text-sm text-indigo-600 dark:text-indigo-400">{{ apiBase }}</code>
                            </div>
                        </div>

                        <!-- Auth Headers -->
                        <div class="mt-4 rounded-xl border border-slate-200 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 shadow-sm dark:shadow-none overflow-hidden">
                            <div class="border-b border-slate-200 dark:border-zinc-800 bg-slate-50 dark:bg-zinc-900 px-4 py-3">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">Headers Autentikasi</span>
                            </div>
                            <div class="divide-y divide-slate-200 dark:divide-zinc-800">
                                <div class="flex items-center gap-4 px-4 py-3">
                                    <code class="w-52 shrink-0 font-mono text-xs text-slate-800 dark:text-zinc-200">X-JAVARAPAY-API</code>
                                    <span class="text-xs text-slate-500 dark:text-zinc-400">API Key project Anda</span>
                                </div>
                                <div class="flex items-center gap-4 px-4 py-3">
                                    <code class="w-52 shrink-0 font-mono text-xs text-slate-800 dark:text-zinc-200">X-JAVARAPAY-MERCHANT-CODE</code>
                                    <span class="text-xs text-slate-500 dark:text-zinc-400">Merchant Code project Anda</span>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- ─── PAYMENT CHANNELS ─── -->
                    <section id="payment-channels" class="scroll-mt-24">
                        <div class="mb-5">
                            <div class="flex items-center gap-2">
                                <Server class="h-4 w-4 text-teal-600 dark:text-teal-400" />
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Channel Pembayaran</h3>
                            </div>
                            <p class="mt-1 text-sm text-slate-500 dark:text-zinc-400">Mendapatkan semua channel pembayaran yang tersedia untuk project Anda.</p>
                        </div>

                        <!-- Endpoint info -->
                        <div class="mb-4 flex flex-wrap gap-3">
                            <span class="flex items-center gap-1.5 rounded-full border border-teal-200 dark:border-teal-500/30 bg-teal-50 dark:bg-teal-500/10 px-3 py-1 font-mono text-xs font-bold text-teal-700 dark:text-teal-400">GET</span>
                            <code class="flex items-center rounded-lg border border-slate-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 px-3 py-1 font-mono text-xs text-slate-600 dark:text-zinc-400">{{ channelEndpoint }}</code>
                        </div>

                        <!-- Code example tabs -->
                        <div class="rounded-xl border border-slate-200 dark:border-zinc-800 bg-slate-50 dark:bg-[#0f0f12] overflow-hidden">
                            <div class="flex items-center justify-between border-b border-slate-200 dark:border-zinc-800 bg-white dark:bg-white/[0.02] px-4 py-3">
                                <div class="flex items-center gap-4">
                                    <div class="flex items-center gap-2">
                                        <Terminal class="h-3.5 w-3.5 text-slate-500 dark:text-zinc-500" />
                                        <span class="text-[11px] font-medium text-slate-600 dark:text-zinc-400">Contoh Kode</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <button @click="activeTabChannel = 'curl'" :class="activeTabChannel === 'curl' ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-slate-500 dark:text-zinc-500 hover:text-slate-700 dark:hover:text-zinc-300'" class="text-[11px] uppercase tracking-wider transition-colors">cURL</button>
                                        <button @click="activeTabChannel = 'php'" :class="activeTabChannel === 'php' ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-slate-500 dark:text-zinc-500 hover:text-slate-700 dark:hover:text-zinc-300'" class="text-[11px] uppercase tracking-wider transition-colors">PHP</button>
                                        <button @click="activeTabChannel = 'node'" :class="activeTabChannel === 'node' ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-slate-500 dark:text-zinc-500 hover:text-slate-700 dark:hover:text-zinc-300'" class="text-[11px] uppercase tracking-wider transition-colors">Node.js</button>
                                    </div>
                                </div>
                                <button
                                    @click="copyToClipboard(activeTabChannel === 'curl' ? channelCurl : (activeTabChannel === 'php' ? channelPhp : channelNode), 'channel-code')"
                                    class="flex items-center gap-1.5 rounded-md border border-slate-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-2 py-1 text-[11px] font-medium text-slate-600 dark:text-zinc-400 transition-all hover:border-slate-300 dark:hover:border-zinc-600 hover:text-slate-900 dark:hover:text-zinc-200 shadow-sm"
                                >
                                    <component :is="copiedId === 'channel-code' ? Check : Copy" class="h-3 w-3" :class="copiedId === 'channel-code' ? 'text-indigo-600 dark:text-indigo-400' : ''" />
                                    {{ copiedId === 'channel-code' ? 'Disalin!' : 'Salin' }}
                                </button>
                            </div>
                            <pre v-show="activeTabChannel === 'curl'" class="overflow-x-auto p-4 text-[13px] leading-relaxed text-slate-800 dark:text-zinc-300"><code>{{ channelCurl }}</code></pre>
                            <pre v-show="activeTabChannel === 'php'" class="overflow-x-auto p-4 text-[13px] leading-relaxed text-slate-800 dark:text-zinc-300"><code>{{ channelPhp }}</code></pre>
                            <pre v-show="activeTabChannel === 'node'" class="overflow-x-auto p-4 text-[13px] leading-relaxed text-slate-800 dark:text-zinc-300"><code>{{ channelNode }}</code></pre>
                        </div>
                    </section>

                    <!-- ─── CREATE TRANSACTION ─── -->
                    <section id="create-transaction" class="scroll-mt-24">
                        <div class="mb-5">
                            <div class="flex items-center gap-2">
                                <FileJson class="h-4 w-4 text-pink-600 dark:text-pink-400" />
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Membuat Transaksi</h3>
                            </div>
                            <p class="mt-1 text-sm text-slate-500 dark:text-zinc-400">Buat transaksi baru melalui API untuk memproses pembayaran pelanggan.</p>
                        </div>

                        <!-- Endpoint info -->
                        <div class="mb-4 flex flex-wrap gap-3">
                            <span class="flex items-center gap-1.5 rounded-full border border-pink-200 dark:border-pink-500/30 bg-pink-50 dark:bg-pink-500/10 px-3 py-1 font-mono text-xs font-bold text-pink-700 dark:text-pink-400">POST</span>
                            <code class="flex items-center rounded-lg border border-slate-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 px-3 py-1 font-mono text-xs text-slate-600 dark:text-zinc-400">{{ createEndpoint }}</code>
                        </div>

                        <!-- Request Body -->
                        <div class="mb-4 rounded-xl border border-slate-200 dark:border-zinc-800 bg-slate-50 dark:bg-[#0f0f12] overflow-hidden">
                            <div class="flex items-center justify-between border-b border-slate-200 dark:border-zinc-800 bg-white dark:bg-white/[0.02] px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <FileJson class="h-3.5 w-3.5 text-slate-500 dark:text-zinc-500" />
                                    <span class="text-[11px] font-medium text-slate-600 dark:text-zinc-400">Request Body (JSON)</span>
                                </div>
                                <button
                                    @click="copyToClipboard(createBody, 'create-body')"
                                    class="flex items-center gap-1.5 rounded-md border border-slate-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-2 py-1 text-[11px] font-medium text-slate-600 dark:text-zinc-400 transition-all hover:border-slate-300 dark:hover:border-zinc-600 hover:text-slate-900 dark:hover:text-zinc-200 shadow-sm"
                                >
                                    <component :is="copiedId === 'create-body' ? Check : Copy" class="h-3 w-3" :class="copiedId === 'create-body' ? 'text-indigo-600 dark:text-indigo-400' : ''" />
                                    {{ copiedId === 'create-body' ? 'Disalin!' : 'Salin' }}
                                </button>
                            </div>
                            <pre class="overflow-x-auto p-4 text-[13px] leading-relaxed"><code><span class="text-slate-500 dark:text-zinc-500">{</span>
  <span class="text-blue-600 dark:text-blue-300">"method_code"</span><span class="text-slate-500 dark:text-zinc-500">:</span> <span class="text-amber-600 dark:text-amber-300">"qris"</span><span class="text-slate-500 dark:text-zinc-500">,</span>          <span class="text-slate-400 dark:text-zinc-500">// kode channel pembayaran</span>
  <span class="text-blue-600 dark:text-blue-300">"merchant_ref"</span><span class="text-slate-500 dark:text-zinc-500">:</span> <span class="text-amber-600 dark:text-amber-300">"INV-001"</span><span class="text-slate-500 dark:text-zinc-500">,</span>      <span class="text-slate-400 dark:text-zinc-500">// referensi unik dari sistem Anda</span>
  <span class="text-blue-600 dark:text-blue-300">"amount"</span><span class="text-slate-500 dark:text-zinc-500">:</span> <span class="text-indigo-600 dark:text-indigo-400">10000</span><span class="text-slate-500 dark:text-zinc-500">,</span>               <span class="text-slate-400 dark:text-zinc-500">// nominal dalam Rupiah</span>
  <span class="text-blue-600 dark:text-blue-300">"customer_name"</span><span class="text-slate-500 dark:text-zinc-500">:</span> <span class="text-amber-600 dark:text-amber-300">"Budi"</span><span class="text-slate-500 dark:text-zinc-500">,</span>
  <span class="text-blue-600 dark:text-blue-300">"customer_email"</span><span class="text-slate-500 dark:text-zinc-500">:</span> <span class="text-amber-600 dark:text-amber-300">"budi@example.com"</span><span class="text-slate-500 dark:text-zinc-500">,</span>
  <span class="text-blue-600 dark:text-blue-300">"customer_phone"</span><span class="text-slate-500 dark:text-zinc-500">:</span> <span class="text-amber-600 dark:text-amber-300">"08123456789"</span><span class="text-slate-500 dark:text-zinc-500">,</span>
  <span class="text-blue-600 dark:text-blue-300">"notes"</span><span class="text-slate-500 dark:text-zinc-500">:</span> <span class="text-amber-600 dark:text-amber-300">"Pembayaran invoice INV-001"</span>
<span class="text-slate-500 dark:text-zinc-500">}</span></code></pre>
                        </div>

                        <!-- Code example tabs -->
                        <div class="rounded-xl border border-slate-200 dark:border-zinc-800 bg-slate-50 dark:bg-[#0f0f12] overflow-hidden">
                            <div class="flex items-center justify-between border-b border-slate-200 dark:border-zinc-800 bg-white dark:bg-white/[0.02] px-4 py-3">
                                <div class="flex items-center gap-4">
                                    <div class="flex items-center gap-2">
                                        <Terminal class="h-3.5 w-3.5 text-slate-500 dark:text-zinc-500" />
                                        <span class="text-[11px] font-medium text-slate-600 dark:text-zinc-400">Contoh Kode</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <button @click="activeTabCreate = 'curl'" :class="activeTabCreate === 'curl' ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-slate-500 dark:text-zinc-500 hover:text-slate-700 dark:hover:text-zinc-300'" class="text-[11px] uppercase tracking-wider transition-colors">cURL</button>
                                        <button @click="activeTabCreate = 'php'" :class="activeTabCreate === 'php' ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-slate-500 dark:text-zinc-500 hover:text-slate-700 dark:hover:text-zinc-300'" class="text-[11px] uppercase tracking-wider transition-colors">PHP</button>
                                        <button @click="activeTabCreate = 'node'" :class="activeTabCreate === 'node' ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-slate-500 dark:text-zinc-500 hover:text-slate-700 dark:hover:text-zinc-300'" class="text-[11px] uppercase tracking-wider transition-colors">Node.js</button>
                                    </div>
                                </div>
                                <button
                                    @click="copyToClipboard(activeTabCreate === 'curl' ? createCurl : (activeTabCreate === 'php' ? createPhp : createNode), 'create-code')"
                                    class="flex items-center gap-1.5 rounded-md border border-slate-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-2 py-1 text-[11px] font-medium text-slate-600 dark:text-zinc-400 transition-all hover:border-slate-300 dark:hover:border-zinc-600 hover:text-slate-900 dark:hover:text-zinc-200 shadow-sm"
                                >
                                    <component :is="copiedId === 'create-code' ? Check : Copy" class="h-3 w-3" :class="copiedId === 'create-code' ? 'text-indigo-600 dark:text-indigo-400' : ''" />
                                    {{ copiedId === 'create-code' ? 'Disalin!' : 'Salin' }}
                                </button>
                            </div>
                            <pre v-show="activeTabCreate === 'curl'" class="overflow-x-auto p-4 text-[13px] leading-relaxed text-slate-800 dark:text-zinc-300"><code>{{ createCurl }}</code></pre>
                            <pre v-show="activeTabCreate === 'php'" class="overflow-x-auto p-4 text-[13px] leading-relaxed text-slate-800 dark:text-zinc-300"><code>{{ createPhp }}</code></pre>
                            <pre v-show="activeTabCreate === 'node'" class="overflow-x-auto p-4 text-[13px] leading-relaxed text-slate-800 dark:text-zinc-300"><code>{{ createNode }}</code></pre>
                        </div>

                        <!-- Field Description Table -->
                        <div class="mt-5 rounded-xl border border-slate-200 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 shadow-sm dark:shadow-none overflow-hidden">
                            <div class="border-b border-slate-200 dark:border-zinc-800 bg-slate-50 dark:bg-zinc-900 px-4 py-3">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-500">Deskripsi Field</span>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-slate-200 dark:border-zinc-800 text-left">
                                            <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-500">Field</th>
                                            <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-500">Tipe</th>
                                            <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-500">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-zinc-800">
                                        <tr v-for="field in [
                                            { name: 'method_code', type: 'string', desc: 'Kode metode pembayaran (contoh: qris, bca_va)' },
                                            { name: 'merchant_ref', type: 'string', desc: 'Nomor referensi unik dari sistem Anda' },
                                            { name: 'amount', type: 'integer', desc: 'Nominal pembayaran dalam Rupiah (min. 1000)' },
                                            { name: 'customer_name', type: 'string', desc: 'Nama pelanggan' },
                                            { name: 'customer_email', type: 'string', desc: 'Email pelanggan' },
                                            { name: 'customer_phone', type: 'string', desc: 'Nomor HP pelanggan' },
                                            { name: 'notes', type: 'string?', desc: 'Catatan tambahan (opsional)' },
                                        ]" :key="field.name" class="hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors">
                                            <td class="px-4 py-3">
                                                <code class="font-mono text-xs text-slate-800 dark:text-zinc-200">{{ field.name }}</code>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span :class="['rounded px-1.5 py-0.5 font-mono text-[11px] font-medium border', field.type.endsWith('?') ? 'bg-slate-100 dark:bg-zinc-800 text-slate-500 dark:text-zinc-400 border-slate-200 dark:border-zinc-700' : 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 border-indigo-200 dark:border-indigo-500/20']">
                                                    {{ field.type }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-xs text-slate-600 dark:text-zinc-400">{{ field.desc }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>

                    <!-- Footer spacer -->
                    <div class="h-10" />

                </div>
            </main>
        </div>

        <!-- Minimal Footer from Welcome.vue -->
        <footer class="bg-white dark:bg-zinc-950 py-10 border-t border-slate-200 dark:border-zinc-800">
            <div class="mx-auto max-w-7xl px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2 opacity-60 grayscale dark:opacity-80">
                    <svg viewBox="0 0 28 28" fill="none" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg"><rect width="28" height="28" rx="8" fill="#6366f1" /><path d="M8 8h4v8a4 4 0 01-4-4V8z" fill="#fff" opacity=".9"/><path d="M14 8h6v2h-4v4a4 4 0 01-2-3.5V8z" fill="#fff" opacity=".7"/></svg>
                    <span class="font-bold text-slate-800 dark:text-zinc-400">JavaraPay</span>
                </div>
                <p class="text-sm text-slate-500 dark:text-zinc-500">
                    &copy; {{ new Date().getFullYear() }} PT Jepara Solusi Teknologi. All rights reserved.
                </p>
            </div>
        </footer>

    </div>
</template>