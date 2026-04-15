<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { CreditCard, Info, CheckCircle2, ChevronRight } from 'lucide-vue-next';

interface Channel {
    code: string;
    name: string;
    type: string;
    fee_percent: string | number;
    fee_flat: number;
    icon_url: string;
    active: boolean;
}

const props = defineProps<{
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
    channels: Channel[];
}>();

const selectedChannel = ref<Channel | null>(null);

const formatIDR = (value: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(value);
};

const adminFee = computed(() => {
    if (!selectedChannel.value) return 0;
    const p = parseFloat(selectedChannel.value.fee_percent.toString());
    const f = selectedChannel.value.fee_flat;
    return Math.ceil((props.order.amount * (p / 100)) + f);
});

const totalPay = computed(() => {
    return props.order.amount + adminFee.value;
});

const selectChannel = (channel: Channel) => {
    selectedChannel.value = channel;
};


const calculateChannelFee = (channel: Channel) => {
    const p = parseFloat(channel.fee_percent.toString());
    const f = channel.fee_flat;
    return Math.ceil((props.order.amount * (p / 100)) + f);
};

const handlePay = () => {
    if (!selectedChannel.value) return;

    router.post(route('payment.pay', { project_id: props.order.project_id, txid: props.order.txid }), {
        method_code: selectedChannel.value.code
    });
};

const groupedChannels = computed(() => {
    const groups: Record<string, Channel[]> = {};
    props.channels.forEach(channel => {
        if (!groups[channel.type]) {
            groups[channel.type] = [];
        }
        groups[channel.type].push(channel);
    });
    return groups;
});

</script>

<template>
    <Head title="Payment Invoice" />

    <div class="min-h-screen bg-muted/40 flex items-center justify-center p-4">
        <div class="w-full max-w-md flex flex-col gap-6">
            
            <!-- Project Header -->
            <div class="text-center space-y-2">
                <p class="text-[10px] uppercase font-bold tracking-[0.3em] text-muted-foreground">Invoice from</p>
                <h1 class="text-2xl font-black text-foreground drop-shadow-sm">{{ order.project.name }}</h1>
            </div>

            <div class="bg-card border border-sidebar-border/70 rounded-3xl shadow-2xl overflow-hidden transition-all duration-300">
                <!-- Summary bar -->
                <div class="bg-primary/5 border-b border-sidebar-border/50 p-6 text-center space-y-1">
                    <p class="text-[10px] text-muted-foreground font-black uppercase tracking-widest">Total Tagihan</p>
                    <p class="text-4xl font-black text-primary tracking-tighter">{{ formatIDR(totalPay) }}</p>
                </div>

                <div class="p-6 space-y-8">
                    <!-- Details section -->
                    <div class="space-y-4">
                        <div class="bg-muted/50 rounded-2xl p-5 space-y-4 text-xs border border-sidebar-border/40">
                            <div class="flex justify-between items-start gap-4">
                                <span class="text-muted-foreground font-bold uppercase tracking-tight">Note</span>
                                <span class="font-bold text-right italic text-foreground/80">{{ order.note || 'Pembelian Layanan' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-muted-foreground font-bold uppercase tracking-tight">Transaction ID</span>
                                <span class="font-mono bg-background px-2 py-0.5 rounded border border-sidebar-border/50 text-[10px] font-black tracking-tighter">{{ order.txid }}</span>
                            </div>
                        </div>

                        <!-- Summary Breakdown -->
                        <div class="px-2 space-y-3 text-[13px]">
                            <div class="flex justify-between items-center">
                                <span class="text-muted-foreground font-medium">Tagihan</span>
                                <span class="font-bold">{{ formatIDR(order.amount) }}</span>
                            </div>
                            <div class="flex justify-between items-center" v-if="selectedChannel">
                                <span class="text-muted-foreground font-medium">Biaya Admin</span>
                                <span class="font-bold text-destructive">+ {{ formatIDR(adminFee) }}</span>
                            </div>
                            <div class="pt-3 border-t border-dashed flex justify-between items-center" v-if="selectedChannel">
                                <span class="font-black text-sm uppercase tracking-tighter">Total Bayar</span>
                                <span class="text-xl font-black text-primary tracking-tight">{{ formatIDR(totalPay) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Methods Section -->
                    <div class="space-y-4">
                        <div class="flex items-center gap-2 px-1">
                            <div class="bg-primary/10 p-1.5 rounded-lg">
                                <CreditCard class="h-4 w-4 text-primary" />
                            </div>
                            <h3 class="text-sm font-black uppercase tracking-tight">Metode Bayar</h3>
                        </div>

                        <div class="space-y-6 max-h-[380px] overflow-y-auto pr-2 custom-scrollbar">
                            <div v-for="(group, type) in groupedChannels" :key="type" class="space-y-3">
                                <h4 class="text-[9px] font-black uppercase tracking-[0.2em] text-muted-foreground/50 px-1">{{ type }}</h4>
                                <div class="grid gap-2">
                                    <button 
                                        v-for="channel in group" 
                                        :key="channel.code"
                                        @click="selectChannel(channel)"
                                        class="flex items-center justify-between p-4 rounded-2xl border border-sidebar-border/70 transition-all duration-300 group text-left bg-card/50"
                                        :class="selectedChannel?.code === channel.code ? 'border-primary border-2 bg-primary/5 shadow-md shadow-primary/5' : 'hover:border-primary/40 hover:bg-muted/50'"
                                    >
                                        <div class="flex items-center gap-4">
                                            <div class="bg-white p-1.5 rounded-xl border border-sidebar-border/30 shadow-sm flex items-center justify-center w-14 h-9">
                                                <img v-if="channel.icon_url" :src="channel.icon_url" :alt="channel.name" class="max-h-full max-w-full object-contain grayscale-[0.3] group-hover:grayscale-0 transition-all" />
                                                <div v-else class="text-[8px] font-black opacity-20 uppercase">{{ channel.code }}</div>
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-[13px] font-black tracking-tight leading-none mb-1">{{ channel.name }}</span>
                                                <span class="text-[10px] text-muted-foreground font-bold">+ {{ formatIDR(calculateChannelFee(channel)) }} Fee</span>
                                            </div>
                                        </div>
                                        <div v-if="selectedChannel?.code === channel.code" class="bg-primary rounded-full p-1 shadow-lg shadow-primary/30">
                                            <CheckCircle2 class="h-3 w-3 text-white" />
                                        </div>
                                        <ChevronRight v-else class="h-4 w-4 text-muted-foreground group-hover:text-primary transition-all group-hover:translate-x-0.5" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- CTA Section -->
                    <div class="pt-4 pb-2">
                        <button 
                            @click="handlePay"
                            class="w-full h-16 bg-primary text-primary-foreground font-black rounded-2xl shadow-xl shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-50 disabled:grayscale disabled:cursor-not-allowed flex items-center justify-center gap-3 group text-base uppercase tracking-wider"
                            :disabled="!selectedChannel"
                        >
                            <span v-if="!selectedChannel">Pilih Metode Bayar</span>
                            <template v-else>
                                <span>Lanjutkan Pembayaran</span>
                                <ChevronRight class="h-6 w-6 group-hover:translate-x-1 transition-transform" />
                            </template>
                        </button>
                        <div class="mt-6 flex flex-col items-center gap-3">
                            <div class="flex items-center gap-1.5 px-3 py-1.5 bg-muted/50 translate-y-[-50%] rounded-full border border-sidebar-border/30">
                                <Info class="h-3 w-3 text-primary" />
                                <span class="text-[9px] font-black uppercase tracking-tighter text-muted-foreground italic">Secure Payment Processed by Tripay</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="text-center">
                <p class="text-[9px] text-muted-foreground uppercase font-black tracking-[0.5em] opacity-40">Powered by JavaraPay Digital Solution</p>
            </div>
        </div>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.05);
    border-radius: 10px;
}
.dark .custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.05);
}

@keyframes slideUp {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.bg-card {
    animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>