<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { Head } from '@inertiajs/vue3';
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
} from 'lucide-vue-next';
import { toast } from 'vue-sonner';

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

const channelCurl = `curl -X GET "${channelEndpoint}" \\
  -H "X-JAVARAPAY-API: YOUR_API_KEY" \\
  -H "X-JAVARAPAY-MERCHANT-CODE: YOUR_MERCHANT_CODE"`;

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
</script>

<template>
    <Head title="Dokumentasi — JavaraPay" />

    <div class="min-h-screen bg-[#09090b] text-white">
        <!-- Top Bar -->
        <header class="fixed top-0 z-50 w-full border-b border-white/5 bg-[#09090b]/80 backdrop-blur-md">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-emerald-400 to-teal-600 shadow-lg shadow-emerald-500/20">
                        <Zap class="h-4 w-4 text-white" />
                    </div>
                    <span class="text-lg font-bold tracking-tight">JavaraPay</span>
                    <span class="ml-2 rounded-full border border-emerald-500/30 bg-emerald-500/10 px-2 py-0.5 text-[11px] font-semibold uppercase tracking-wider text-emerald-400">Docs</span>
                </div>
                <a href="/dashboard" class="group flex items-center gap-1.5 rounded-lg border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium text-white/70 transition-all hover:border-emerald-500/50 hover:bg-emerald-500/10 hover:text-white">
                    Dashboard
                    <ArrowRight class="h-3.5 w-3.5 transition-transform group-hover:translate-x-0.5" />
                </a>
            </div>
        </header>

        <div class="mx-auto flex max-w-7xl gap-8 px-6 pt-24 pb-20">
            <!-- Sidebar -->
            <aside class="hidden w-60 shrink-0 lg:block">
                <div class="sticky top-24">
                    <p class="mb-3 text-[11px] font-bold uppercase tracking-widest text-white/30">Navigasi</p>
                    <nav class="flex flex-col gap-0.5">
                        <button
                            v-for="s in sections"
                            :key="s.id"
                            @click="scrollToSection(s.id)"
                            :class="[
                                'group flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left text-sm font-medium transition-all duration-150',
                                s.indent ? 'ml-4 w-[calc(100%-1rem)]' : '',
                                activeSection === s.id
                                    ? 'bg-emerald-500/10 text-emerald-400'
                                    : 'text-white/40 hover:bg-white/5 hover:text-white/80'
                            ]"
                        >
                            <component :is="s.icon" :class="['h-3.5 w-3.5 shrink-0', activeSection === s.id ? 'text-emerald-400' : 'text-white/30 group-hover:text-white/60']" />
                            {{ s.label }}
                            <ChevronRight v-if="activeSection === s.id" class="ml-auto h-3 w-3 text-emerald-400" />
                        </button>
                    </nav>

                    <!-- Quick Info Card -->
                    <div class="mt-6 rounded-xl border border-amber-500/20 bg-amber-500/5 p-4">
                        <div class="mb-2 flex items-center gap-2">
                            <AlertCircle class="h-3.5 w-3.5 text-amber-400" />
                            <span class="text-[11px] font-bold uppercase tracking-wider text-amber-400">Catatan</span>
                        </div>
                        <p class="text-[12px] leading-relaxed text-white/50">
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
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-400/20 to-teal-600/20 ring-1 ring-emerald-500/30">
                                <BookOpen class="h-5 w-5 text-emerald-400" />
                            </div>
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-widest text-emerald-500">01 — Getting Started</p>
                                <h1 class="text-2xl font-bold tracking-tight text-white">Pengenalan</h1>
                            </div>
                        </div>
                        <div class="rounded-2xl border border-white/5 bg-white/[0.02] p-6">
                            <p class="text-base leading-relaxed text-white/60">
                                <span class="font-semibold text-white">JavaraPay</span> adalah jembatan pembayaran digital
                                <span class="rounded bg-emerald-500/10 px-1.5 py-0.5 font-mono text-xs font-medium text-emerald-400">Payment Integrator</span>
                                yang memudahkan merchant untuk menerima pembayaran dari pelanggan.
                            </p>
                            <p class="mt-4 text-base leading-relaxed text-white/60">
                                Dengan JavaraPay, Anda dapat membuat payment link, mengintegrasikan via API, dan menerima notifikasi webhook — semua dalam satu platform yang mudah digunakan.
                            </p>

                            <!-- Feature Grid -->
                            <div class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-3">
                                <div class="rounded-xl border border-white/5 bg-white/[0.03] p-4">
                                    <Link2 class="mb-2 h-5 w-5 text-blue-400" />
                                    <h3 class="text-sm font-semibold text-white">Payment Links</h3>
                                    <p class="mt-1 text-xs text-white/40">Buat & bagikan link pembayaran dalam hitungan detik.</p>
                                </div>
                                <div class="rounded-xl border border-white/5 bg-white/[0.03] p-4">
                                    <Code2 class="mb-2 h-5 w-5 text-violet-400" />
                                    <h3 class="text-sm font-semibold text-white">REST API</h3>
                                    <p class="mt-1 text-xs text-white/40">Integrasi langsung dengan API untuk sistem Anda.</p>
                                </div>
                                <div class="rounded-xl border border-white/5 bg-white/[0.03] p-4">
                                    <Webhook class="mb-2 h-5 w-5 text-orange-400" />
                                    <h3 class="text-sm font-semibold text-white">Webhook</h3>
                                    <p class="mt-1 text-xs text-white/40">Terima notifikasi real-time saat pembayaran berhasil.</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- ─── PERSIAPAN ─── -->
                    <section id="persiapan" class="scroll-mt-24">
                        <div class="mb-6 flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-400/20 to-indigo-600/20 ring-1 ring-blue-500/30">
                                <Zap class="h-5 w-5 text-blue-400" />
                            </div>
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-widest text-blue-400">02 — Setup</p>
                                <h2 class="text-2xl font-bold tracking-tight text-white">Persiapan</h2>
                            </div>
                        </div>

                        <p class="mb-5 text-white/50">Sebelum menggunakan JavaraPay, Anda perlu mendaftar dan membuat sebuah project.</p>

                        <!-- Steps -->
                        <div class="flex flex-col gap-4">
                            <div class="flex gap-4 rounded-xl border border-white/5 bg-white/[0.02] p-5">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-500/20 text-sm font-bold text-blue-400">1</div>
                                <div>
                                    <h3 class="font-semibold text-white">Daftar ke JavaraPay</h3>
                                    <p class="mt-1 text-sm text-white/50">Buat akun baru di <a href="https://pay.javara.digital" target="_blank" class="text-blue-400 hover:underline">pay.javara.digital</a> untuk mendapatkan akses ke dashboard merchant.</p>
                                </div>
                            </div>
                            <div class="flex gap-4 rounded-xl border border-white/5 bg-white/[0.02] p-5">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-500/20 text-sm font-bold text-blue-400">2</div>
                                <div>
                                    <h3 class="font-semibold text-white">Buat Project</h3>
                                    <p class="mt-1 text-sm text-white/50">Project adalah identitas merchant Anda di JavaraPay. Setiap project memiliki <span class="font-mono text-xs text-white/70">API Key</span> dan <span class="font-mono text-xs text-white/70">Merchant Code</span> yang unik.</p>
                                </div>
                            </div>
                            <div class="flex gap-4 rounded-xl border border-white/5 bg-white/[0.02] p-5">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-500/20 text-sm font-bold text-blue-400">3</div>
                                <div>
                                    <h3 class="font-semibold text-white">Salin Kredensial</h3>
                                    <p class="mt-1 text-sm text-white/50">Buka detail project dan salin <span class="font-mono text-xs text-white/70">API Key</span> serta <span class="font-mono text-xs text-white/70">Merchant Code</span> — Anda akan membutuhkannya di setiap request API.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Project Info Box -->
                        <div class="mt-5 rounded-xl border border-blue-500/20 bg-blue-500/5 p-5">
                            <div class="flex items-start gap-3">
                                <Hash class="mt-0.5 h-4 w-4 shrink-0 text-blue-400" />
                                <div>
                                    <h4 class="text-sm font-semibold text-blue-300">Tentang Project</h4>
                                    <p class="mt-1 text-[13px] leading-relaxed text-white/50">
                                        Project adalah merchant Anda yang terdaftar di JavaraPay. Dengan mendaftarkan project, Anda akan mendapatkan <strong class="text-white/70">API Key</strong> dan <strong class="text-white/70">Secret Key</strong> yang digunakan untuk autentikasi saat menggunakan API JavaraPay.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- ─── PAYMENT LINKS ─── -->
                    <section id="payment-links" class="scroll-mt-24">
                        <div class="mb-6 flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-violet-400/20 to-purple-600/20 ring-1 ring-violet-500/30">
                                <Link2 class="h-5 w-5 text-violet-400" />
                            </div>
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-widest text-violet-400">03 — No Code</p>
                                <h2 class="text-2xl font-bold tracking-tight text-white">Integrasi via Payment Links</h2>
                            </div>
                        </div>
                        <p class="mb-5 text-white/50">
                            Cara tercepat untuk mulai menerima pembayaran — tanpa perlu koding sama sekali.
                        </p>

                        <div class="rounded-2xl border border-white/5 bg-white/[0.02] overflow-hidden">
                            <div class="border-b border-white/5 bg-white/[0.03] px-5 py-3">
                                <span class="text-xs font-semibold uppercase tracking-wider text-white/30">Langkah-langkah</span>
                            </div>
                            <ol class="flex flex-col divide-y divide-white/5">
                                <li class="flex items-start gap-4 p-5">
                                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-violet-500/20 text-xs font-bold text-violet-400">1</span>
                                    <div>
                                        <p class="text-sm font-medium text-white">Klik <span class="rounded bg-violet-500/20 px-2 py-0.5 font-mono text-xs font-semibold text-violet-300">"Create Payment Links"</span> di dashboard JavaraPay.</p>
                                    </div>
                                </li>
                                <li class="flex items-start gap-4 p-5">
                                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-violet-500/20 text-xs font-bold text-violet-400">2</span>
                                    <div>
                                        <p class="text-sm font-medium text-white">Masukkan <strong>jumlah nominal</strong> yang ingin ditagihkan.</p>
                                    </div>
                                </li>
                                <li class="flex items-start gap-4 p-5">
                                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-violet-500/20 text-xs font-bold text-violet-400">3</span>
                                    <div>
                                        <p class="text-sm font-medium text-white">Masukkan <strong>catatan (Notes)</strong> jika diperlukan.</p>
                                        <p class="mt-1 text-xs text-white/40">Contoh: "Pembayaran Invoice INV-001"</p>
                                    </div>
                                </li>
                                <li class="flex items-start gap-4 p-5">
                                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-violet-500/20 text-xs font-bold text-violet-400">4</span>
                                    <div>
                                        <p class="text-sm font-medium text-white">Link pembayaran siap untuk dibagikan kepada client Anda. ✅</p>
                                    </div>
                                </li>
                            </ol>
                        </div>

                        <!-- Webhook Note -->
                        <div class="mt-4 flex items-start gap-3 rounded-xl border border-orange-500/20 bg-orange-500/5 p-4">
                            <Webhook class="mt-0.5 h-4 w-4 shrink-0 text-orange-400" />
                            <p class="text-[13px] leading-relaxed text-white/50">
                                <strong class="text-orange-300">Webhook:</strong> Jika Anda mengatur <span class="font-mono text-xs text-white/70">Webhook URL</span> pada project Anda, JavaraPay akan mengirimkan notifikasi ke URL tersebut saat pembayaran berhasil.
                            </p>
                        </div>
                    </section>

                    <!-- ─── API BASE ─── -->
                    <section id="api-integration" class="scroll-mt-24">
                        <div class="mb-6 flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-rose-400/20 to-red-600/20 ring-1 ring-rose-500/30">
                                <Code2 class="h-5 w-5 text-rose-400" />
                            </div>
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-widest text-rose-400">04 — Developer</p>
                                <h2 class="text-2xl font-bold tracking-tight text-white">Integrasi via API</h2>
                            </div>
                        </div>
                        <p class="mb-5 text-white/50">Integrasikan JavaraPay langsung ke dalam aplikasi atau sistem Anda menggunakan REST API.</p>

                        <!-- Base URL -->
                        <div class="rounded-xl border border-white/5 bg-white/[0.02] overflow-hidden">
                            <div class="flex items-center justify-between border-b border-white/5 bg-white/[0.03] px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <Globe class="h-3.5 w-3.5 text-white/30" />
                                    <span class="text-[11px] font-bold uppercase tracking-wider text-white/30">Base URL</span>
                                </div>
                                <button
                                    @click="copyToClipboard(apiBase, 'base-url')"
                                    class="flex items-center gap-1.5 rounded-md border border-white/10 bg-white/5 px-2 py-1 text-[11px] font-medium text-white/40 transition-all hover:border-white/20 hover:text-white/70"
                                >
                                    <component :is="copiedId === 'base-url' ? Check : Copy" class="h-3 w-3" :class="copiedId === 'base-url' ? 'text-emerald-400' : ''" />
                                    {{ copiedId === 'base-url' ? 'Disalin!' : 'Salin' }}
                                </button>
                            </div>
                            <div class="p-4">
                                <code class="font-mono text-sm text-emerald-400">{{ apiBase }}</code>
                            </div>
                        </div>

                        <!-- Auth Headers -->
                        <div class="mt-4 rounded-xl border border-white/5 bg-white/[0.02] overflow-hidden">
                            <div class="border-b border-white/5 bg-white/[0.03] px-4 py-3">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-white/30">Headers Autentikasi</span>
                            </div>
                            <div class="divide-y divide-white/5">
                                <div class="flex items-center gap-4 px-4 py-3">
                                    <code class="w-52 shrink-0 font-mono text-xs text-amber-300">X-JAVARAPAY-API</code>
                                    <span class="text-xs text-white/40">API Key project Anda</span>
                                </div>
                                <div class="flex items-center gap-4 px-4 py-3">
                                    <code class="w-52 shrink-0 font-mono text-xs text-amber-300">X-JAVARAPAY-MERCHANT-CODE</code>
                                    <span class="text-xs text-white/40">Merchant Code project Anda</span>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- ─── PAYMENT CHANNELS ─── -->
                    <section id="payment-channels" class="scroll-mt-24">
                        <div class="mb-5">
                            <div class="flex items-center gap-2">
                                <Server class="h-4 w-4 text-teal-400" />
                                <h3 class="text-lg font-bold text-white">Channel Pembayaran</h3>
                            </div>
                            <p class="mt-1 text-sm text-white/40">Mendapatkan semua channel pembayaran yang tersedia untuk project Anda.</p>
                        </div>

                        <!-- Endpoint info -->
                        <div class="mb-4 flex flex-wrap gap-3">
                            <span class="flex items-center gap-1.5 rounded-full border border-teal-500/30 bg-teal-500/10 px-3 py-1 font-mono text-xs font-bold text-teal-400">GET</span>
                            <code class="flex items-center rounded-lg border border-white/5 bg-white/[0.03] px-3 py-1 font-mono text-xs text-white/60">{{ channelEndpoint }}</code>
                        </div>

                        <!-- cURL example -->
                        <div class="rounded-xl border border-white/5 bg-[#0f0f12] overflow-hidden">
                            <div class="flex items-center justify-between border-b border-white/5 bg-white/[0.02] px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <Terminal class="h-3.5 w-3.5 text-white/30" />
                                    <span class="text-[11px] font-medium text-white/30">cURL</span>
                                </div>
                                <button
                                    @click="copyToClipboard(channelCurl, 'channel-curl')"
                                    class="flex items-center gap-1.5 rounded-md border border-white/10 bg-white/5 px-2 py-1 text-[11px] font-medium text-white/40 transition-all hover:border-white/20 hover:text-white/70"
                                >
                                    <component :is="copiedId === 'channel-curl' ? Check : Copy" class="h-3 w-3" :class="copiedId === 'channel-curl' ? 'text-emerald-400' : ''" />
                                    {{ copiedId === 'channel-curl' ? 'Disalin!' : 'Salin' }}
                                </button>
                            </div>
                            <pre class="overflow-x-auto p-4 text-[13px] leading-relaxed text-white/70"><code>{{ channelCurl }}</code></pre>
                        </div>
                    </section>

                    <!-- ─── CREATE TRANSACTION ─── -->
                    <section id="create-transaction" class="scroll-mt-24">
                        <div class="mb-5">
                            <div class="flex items-center gap-2">
                                <FileJson class="h-4 w-4 text-pink-400" />
                                <h3 class="text-lg font-bold text-white">Membuat Transaksi</h3>
                            </div>
                            <p class="mt-1 text-sm text-white/40">Buat transaksi baru melalui API untuk memproses pembayaran pelanggan.</p>
                        </div>

                        <!-- Endpoint info -->
                        <div class="mb-4 flex flex-wrap gap-3">
                            <span class="flex items-center gap-1.5 rounded-full border border-pink-500/30 bg-pink-500/10 px-3 py-1 font-mono text-xs font-bold text-pink-400">POST</span>
                            <code class="flex items-center rounded-lg border border-white/5 bg-white/[0.03] px-3 py-1 font-mono text-xs text-white/60">{{ createEndpoint }}</code>
                        </div>

                        <!-- Request Body -->
                        <div class="mb-4 rounded-xl border border-white/5 bg-[#0f0f12] overflow-hidden">
                            <div class="flex items-center justify-between border-b border-white/5 bg-white/[0.02] px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <FileJson class="h-3.5 w-3.5 text-white/30" />
                                    <span class="text-[11px] font-medium text-white/30">Request Body (JSON)</span>
                                </div>
                                <button
                                    @click="copyToClipboard(createBody, 'create-body')"
                                    class="flex items-center gap-1.5 rounded-md border border-white/10 bg-white/5 px-2 py-1 text-[11px] font-medium text-white/40 transition-all hover:border-white/20 hover:text-white/70"
                                >
                                    <component :is="copiedId === 'create-body' ? Check : Copy" class="h-3 w-3" :class="copiedId === 'create-body' ? 'text-emerald-400' : ''" />
                                    {{ copiedId === 'create-body' ? 'Disalin!' : 'Salin' }}
                                </button>
                            </div>
                            <pre class="overflow-x-auto p-4 text-[13px] leading-relaxed"><code><span class="text-white/40">{</span>
  <span class="text-blue-300">"method_code"</span><span class="text-white/40">:</span> <span class="text-amber-300">"qris"</span><span class="text-white/40">,</span>          <span class="text-white/25">// kode channel pembayaran</span>
  <span class="text-blue-300">"merchant_ref"</span><span class="text-white/40">:</span> <span class="text-amber-300">"INV-001"</span><span class="text-white/40">,</span>      <span class="text-white/25">// referensi unik dari sistem Anda</span>
  <span class="text-blue-300">"amount"</span><span class="text-white/40">:</span> <span class="text-emerald-400">10000</span><span class="text-white/40">,</span>               <span class="text-white/25">// nominal dalam Rupiah</span>
  <span class="text-blue-300">"customer_name"</span><span class="text-white/40">:</span> <span class="text-amber-300">"Budi"</span><span class="text-white/40">,</span>
  <span class="text-blue-300">"customer_email"</span><span class="text-white/40">:</span> <span class="text-amber-300">"budi@example.com"</span><span class="text-white/40">,</span>
  <span class="text-blue-300">"customer_phone"</span><span class="text-white/40">:</span> <span class="text-amber-300">"08123456789"</span><span class="text-white/40">,</span>
  <span class="text-blue-300">"notes"</span><span class="text-white/40">:</span> <span class="text-amber-300">"Pembayaran invoice INV-001"</span>
<span class="text-white/40">}</span></code></pre>
                        </div>

                        <!-- cURL example -->
                        <div class="rounded-xl border border-white/5 bg-[#0f0f12] overflow-hidden">
                            <div class="flex items-center justify-between border-b border-white/5 bg-white/[0.02] px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <Terminal class="h-3.5 w-3.5 text-white/30" />
                                    <span class="text-[11px] font-medium text-white/30">cURL</span>
                                </div>
                                <button
                                    @click="copyToClipboard(createCurl, 'create-curl')"
                                    class="flex items-center gap-1.5 rounded-md border border-white/10 bg-white/5 px-2 py-1 text-[11px] font-medium text-white/40 transition-all hover:border-white/20 hover:text-white/70"
                                >
                                    <component :is="copiedId === 'create-curl' ? Check : Copy" class="h-3 w-3" :class="copiedId === 'create-curl' ? 'text-emerald-400' : ''" />
                                    {{ copiedId === 'create-curl' ? 'Disalin!' : 'Salin' }}
                                </button>
                            </div>
                            <pre class="overflow-x-auto p-4 text-[13px] leading-relaxed text-white/70"><code>{{ createCurl }}</code></pre>
                        </div>

                        <!-- Field Description Table -->
                        <div class="mt-5 rounded-xl border border-white/5 bg-white/[0.02] overflow-hidden">
                            <div class="border-b border-white/5 bg-white/[0.03] px-4 py-3">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-white/30">Deskripsi Field</span>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-white/5 text-left">
                                            <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider text-white/30">Field</th>
                                            <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider text-white/30">Tipe</th>
                                            <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider text-white/30">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-white/5">
                                        <tr v-for="field in [
                                            { name: 'method_code', type: 'string', desc: 'Kode metode pembayaran (contoh: qris, bca_va)' },
                                            { name: 'merchant_ref', type: 'string', desc: 'Nomor referensi unik dari sistem Anda' },
                                            { name: 'amount', type: 'integer', desc: 'Nominal pembayaran dalam Rupiah (min. 1000)' },
                                            { name: 'customer_name', type: 'string', desc: 'Nama pelanggan' },
                                            { name: 'customer_email', type: 'string', desc: 'Email pelanggan' },
                                            { name: 'customer_phone', type: 'string', desc: 'Nomor HP pelanggan' },
                                            { name: 'notes', type: 'string?', desc: 'Catatan tambahan (opsional)' },
                                        ]" :key="field.name" class="hover:bg-white/[0.015] transition-colors">
                                            <td class="px-4 py-3">
                                                <code class="font-mono text-xs text-blue-300">{{ field.name }}</code>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span :class="['rounded px-1.5 py-0.5 font-mono text-[11px] font-medium', field.type.endsWith('?') ? 'bg-white/5 text-white/30' : 'bg-amber-500/10 text-amber-300']">
                                                    {{ field.type }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-xs text-white/50">{{ field.desc }}</td>
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
    </div>
</template>