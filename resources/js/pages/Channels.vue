<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Save, Search, Filter } from 'lucide-vue-next';
import { toast } from 'vue-sonner';
import { ref, computed } from 'vue';

// UI components
import { Button } from '@/components/ui/button';
import { Switch } from '@/components/ui/switch';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Input } from '@/components/ui/input';

interface Channel {
    method_code: string;
    method_name: string;
    group: string;
    image: string;
    fee_percent: string | number;
    fee_flat: number;
    active: boolean;
}

const props = defineProps<{
    project: any;
    channels: Channel[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Project', href: '/dashboard/project' },
    { title: 'Payment Channels', href: '#' },
];

const searchQuery = ref('');

const form = useForm({
    channels: props.channels.map(c => ({
        ...c,
        active: !!c.active
    })),
});

const filteredChannels = computed(() => {
    if (!searchQuery.value) return form.channels;
    return form.channels.filter(c => 
        c.method_name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        c.group.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

const formatFee = (flat: number, percent: string | number) => {
    const p = parseFloat(percent.toString());
    const f = flat;

    const formattedFlat = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(f);

    if (f !== 0 && p !== 0) {
        return `${formattedFlat} + ${p}%`;
    } else if (p !== 0) {
        return `${p}%`;
    } else if (f !== 0) {
        return formattedFlat;
    }
    return 'Free';
};

const submit = () => {
    form.post(`/dashboard/project/${props.project.id}/channels`, {
        onSuccess: () => {
            toast.success('Project channels updated successfully');
        },
        onError: () => {
            toast.error('Failed to update channels');
        }
    });
};
</script>

<template>
    <Head title="Payment Channels" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6 lg:p-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="space-y-1">
                    <h1 class="text-2xl font-black tracking-tight flex items-center gap-2">
                        Payment Channels
                        <span class="text-xs font-bold px-2 py-0.5 rounded bg-primary/10 text-primary border border-primary/20">{{ project.name }}</span>
                    </h1>
                    <p class="text-muted-foreground text-sm">Configure which payment methods are enabled for this specific project.</p>
                </div>
                <div class="flex items-center gap-2">
                    <div class="relative w-full sm:w-64">
                        <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                        <Input 
                            v-model="searchQuery"
                            placeholder="Search methods..." 
                            class="pl-9 h-10 border-sidebar-border/70 bg-card" 
                        />
                    </div>
                    <Button @click="submit" :disabled="form.processing" size="default" class="shadow-lg shadow-primary/20">
                        <Save class="mr-2 h-4 w-4" />
                        Save Configuration
                    </Button>
                </div>
            </div>

            <Card class="border-sidebar-border/70 overflow-hidden shadow-xl bg-card">
                <CardHeader class="bg-muted/30 border-b px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="space-y-1">
                            <CardTitle class="text font-bold uppercase tracking-tight">Active Methods</CardTitle>
                            <CardDescription class="text-xs">Only enabled methods will be displayed on your project's checkout page.</CardDescription>
                        </div>
                        <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-blue-500/10 border border-blue-500/20">
                            <Filter class="h-3 w-3 text-blue-500" />
                            <span class="text-[10px] font-black text-blue-500 uppercase">{{ filteredChannels.length }} Available</span>
                        </div>
                    </div>
                </CardHeader>
                <CardContent class="p-0">
                    <div class="relative w-full overflow-auto max-h-[calc(100vh-320px)] custom-scrollbar">
                        <table class="w-full text-sm border-collapse">
                            <thead class="bg-muted/50 border-b sticky top-0 z-10 backdrop-blur-md">
                                <tr class="text-left font-bold text-muted-foreground uppercase text-[10px] tracking-[0.2em]">
                                    <th class="h-12 px-6 align-middle w-24 text-center">Brand</th>
                                    <th class="h-12 px-6 align-middle">Method Name</th>
                                    <th class="h-12 px-6 align-middle">Type</th>
                                    <th class="h-12 px-6 align-middle">Fee </th>
                                    <th class="h-12 px-6 align-middle text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-sidebar-border/50">
                                <tr v-for="(channel, index) in filteredChannels" :key="channel.method_code" class="hover:bg-muted/30 transition-all duration-200 group">
                                    <td class="px-6 py-4 align-middle text-center">
                                        <div class="inline-flex items-center justify-center p-1 bg-white border border-sidebar-border/50 rounded-xl shadow-sm w-16 h-10 overflow-hidden group-hover:scale-105 transition-transform">
                                            <img v-if="channel.image" :src="'/' + channel.image" :alt="channel.method_name" class="max-h-full max-w-full object-contain" />
                                            <div v-else class="text-[8px] font-black opacity-20 uppercase">{{ channel.method_code }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 align-middle">
                                        <div class="flex flex-col">
                                            <span class="font-black text-foreground tracking-tight">{{ channel.method_name }}</span>
                                            <span class="text-[10px] text-muted-foreground font-mono uppercase opacity-60">{{ channel.method_code }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 align-middle">
                                        <span class="inline-flex items-center rounded-full bg-muted px-2.5 py-0.5 text-[10px] font-black text-muted-foreground border border-sidebar-border/70 uppercase tracking-tighter">
                                            {{ channel.group }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 align-middle font-bold text-primary tracking-tight">
                                        {{ formatFee(channel.fee_flat, channel.fee_percent) }}
                                    </td>
                                    <td class="px-6 py-4 align-middle text-right">
                                        <div class="flex justify-end pr-2">
                                            <Switch 
                                                v-model="channel.active"
                                                class="data-[state=checked]:bg-primary"
                                            />
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>
            
            <div v-if="filteredChannels.length === 0" class="flex flex-col items-center justify-center p-12 bg-card rounded-2xl border border-dashed border-sidebar-border shadow-inner">
                <AlertCircle class="h-10 w-10 text-muted-foreground opacity-20 mb-3" />
                <p class="text-sm font-bold text-muted-foreground opacity-60">No payment methods found matching your search.</p>
                <Button variant="link" @click="searchQuery = ''" class="mt-2 text-xs">Clear search</Button>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
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
</style>