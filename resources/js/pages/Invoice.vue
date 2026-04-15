<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { Clock, CheckCircle2, Copy, ExternalLink, ArrowLeft, Info, QrCode } from 'lucide-vue-next';

interface Props {
    order: {
        id: number;
        project_id: number;
        txid: string;
        amount: number;
        note: string | null;
        project: {
            name: string;
        };
    };
    transaction: {
        id: number;
        reference: string | null;
        payment_method_code: string;
        payment_method_name: string;
        amount: number;
        total_fee: number;
        total_amount: number;
        status: string;
        pay_code: string | null;
        pay_url: string | null;
        qr_url: string | null;
        expired_at: string | null;
    };
}

const props = defineProps<Props>();

const formatIDR = (value: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(value);
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

const isExpired = computed(() => {
    if (!props.transaction.expired_at) return false;
    return new Date(props.transaction.expired_at) < new Date();
});

const statusColor = computed(() => {
    switch (props.transaction.status.toLowerCase()) {
        case 'paid': return 'text-green-500';
        case 'unpaid': return 'text-yellow-500';
        case 'expired': return 'text-red-500';
        default: return 'text-muted-foreground';
    }
});

const copyToClipboard = (text: string) => {
    navigator.clipboard.writeText(text);
    // You could add a toast here
};

</script>

<template>
    <Head title="Invoice Detail" />

    <div class="min-h-screen bg-muted/40 flex items-center justify-center p-4">
        <div class="w-full max-w-md flex flex-col gap-6">
            
            <!-- Project Header -->
            <div class="text-center space-y-2">
                <p class="text-[10px] uppercase font-bold tracking-[0.3em] text-muted-foreground">Invoice from</p>
                <h1 class="text-2xl font-black text-foreground drop-shadow-sm">{{ order.project.name }}</h1>
            </div>

            <div class="bg-card border border-sidebar-border/70 rounded-3xl shadow-2xl overflow-hidden transition-all duration-300">
                <!-- Status bar -->
                <div :class="['p-4 text-center border-b border-sidebar-border/50', 
                    transaction.status === 'PAID' ? 'bg-green-500/10' : 
                    transaction.status === 'EXPIRED' ? 'bg-red-500/10' : 'bg-primary/5']">
                    <div class="flex items-center justify-center gap-2">
                        <component :is="transaction.status === 'PAID' ? CheckCircle2 : Clock" class="h-4 w-4" :class="statusColor" />
                        <span class="text-xs font-black uppercase tracking-widest" :class="statusColor">
                            {{ transaction.status }}
                        </span>
                    </div>
                </div>

                <div class="p-6 space-y-8">
                    <!-- Amount Section -->
                    <div class="text-center space-y-1">
                        <p class="text-[10px] text-muted-foreground font-black uppercase tracking-widest">Total Bayar</p>
                        <p class="text-4xl font-black text-primary tracking-tighter">{{ formatIDR(transaction.total_amount) }}</p>
                    </div>

                    <!-- Payment Details Card -->
                    <div class="space-y-4">
                        <div class="bg-muted/50 rounded-2xl p-5 space-y-4 border border-sidebar-border/40">
                            <!-- Payment Method Info -->
                            <div class="flex justify-between items-center">
                                <span class="text-[10px] text-muted-foreground font-bold uppercase tracking-tight">Metode Bayar</span>
                                <span class="text-sm font-black text-foreground">{{ transaction.payment_method_name }}</span>
                            </div>

                            <!-- Pay Code / Virtual Account -->
                            <div v-if="transaction.pay_code" class="space-y-2 pt-2 border-t border-sidebar-border/30">
                                <span class="text-[10px] text-muted-foreground font-bold uppercase tracking-tight">Kode Bayar / No. VA</span>
                                <div class="flex items-center justify-between bg-background p-3 rounded-xl border border-sidebar-border/50">
                                    <span class="font-mono text-lg font-black tracking-wider text-primary">{{ transaction.pay_code }}</span>
                                    <button @click="copyToClipboard(transaction.pay_code)" class="text-primary hover:bg-primary/10 p-1.5 rounded-lg transition-colors">
                                        <Copy class="h-4 w-4" />
                                    </button>
                                </div>
                            </div>

                            <!-- QR Code -->
                            <div v-if="transaction.qr_url" class="flex flex-col items-center space-y-3 pt-4 border-t border-sidebar-border/30">
                                <span class="text-[10px] text-muted-foreground font-bold uppercase tracking-tight">Scan QRIS</span>
                                <div class="bg-white p-3 rounded-2xl border-4 border-primary/10 shadow-lg">
                                    <img :src="transaction.qr_url" alt="QR Code" class="w-48 h-48" />
                                </div>
                                <p class="text-[9px] font-bold text-muted-foreground uppercase tracking-tighter">Bisa discan dengan semua aplikasi pembayaran</p>
                            </div>

                            <!-- Expiry Info -->
                            <div v-if="transaction.status === 'UNPAID'" class="pt-4 border-t border-sidebar-border/30 flex items-center justify-between">
                                <span class="text-[10px] text-muted-foreground font-bold uppercase tracking-tight">Batas Pembayaran</span>
                                <div class="flex items-center gap-1.5 text-destructive font-bold text-[11px]">
                                    <Clock class="h-3 w-3" />
                                    <span>{{ formatDate(transaction.expired_at) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- External Link -->
                        <div v-if="transaction.pay_url && transaction.status === 'UNPAID'" class="pt-2">
                            <a :href="transaction.pay_url" target="_blank" class="flex items-center justify-center gap-2 text-primary font-bold text-xs hover:underline">
                                Lihat Instruksi Lengkap <ExternalLink class="h-3 w-3" />
                            </a>
                        </div>
                    </div>

                    <!-- Summary Breakdown -->
                    <div class="px-2 space-y-3 text-xs border-t border-sidebar-border/30 pt-6">
                        <div class="flex justify-between items-center">
                            <span class="text-muted-foreground font-medium italic">Tagihan</span>
                            <span class="font-bold text-foreground/70">{{ formatIDR(transaction.amount) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-muted-foreground font-medium italic">Biaya Layanan</span>
                            <span class="font-bold text-foreground/70">{{ formatIDR(transaction.total_fee) }}</span>
                        </div>
                    </div>

                    <!-- CTA / Success Section -->
                    <div v-if="transaction.status === 'PAID'" class="pt-4 text-center space-y-4">
                        <div class="bg-green-500/10 p-4 rounded-2xl border border-green-500/20 text-green-700 space-y-1">
                            <p class="text-sm font-black uppercase">Pembayaran Berhasil</p>
                            <p class="text-[10px] font-bold opacity-80 italic">Terima kasih telah melakukan pembayaran.</p>
                        </div>
                    </div>

                    <div class="pt-4 pb-2">
                        <Link 
                            :href="route('home')"
                            class="w-full h-14 bg-muted text-muted-foreground font-black rounded-2xl hover:bg-muted/80 active:scale-[0.98] transition-all flex items-center justify-center gap-3 text-sm uppercase tracking-wider"
                        >
                            <ArrowLeft class="h-4 w-4" />
                            Kembali ke Beranda
                        </Link>
                        <div class="mt-6 flex flex-col items-center gap-3">
                            <div class="flex items-center gap-1.5 px-3 py-1.5 bg-muted/50 rounded-full border border-sidebar-border/30">
                                <Info class="h-3 w-3 text-primary" />
                                <span class="text-[9px] font-black uppercase tracking-tighter text-muted-foreground italic">Powered by JavaraPay Digital Solution</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes slideUp {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.bg-card {
    animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>
