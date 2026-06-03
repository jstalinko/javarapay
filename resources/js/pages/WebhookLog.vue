<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Clock, ExternalLink, RefreshCw, Eye, Copy, Check } from 'lucide-vue-next';
import { toast } from 'vue-sonner';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

interface WebhookLog {
    id: number;
    project_id: number;
    transaction_id: number | null;
    payload: Record<string, any> | string;
    response_body: string | null;
    response_code: number | null;
    last_sent_at: string | null;
    retries: number;
    created_at: string;
    project: {
        name: string;
        webhook_url: string;
    };
}

interface PaginatedData<T> {
    data: T[];
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
    current_page: number;
    last_page: number;
    total: number;
}

const props = defineProps<{
    logs: PaginatedData<WebhookLog>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Webhook Logs', href: '/dashboard/webhook' },
];

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

const getStatusBadge = (code: number | null) => {
    if (!code) return { text: 'Pending', class: 'bg-yellow-100 text-yellow-800 border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-400 dark:border-yellow-800' };
    
    if (code >= 200 && code < 300) {
        return { text: code.toString(), class: 'bg-green-100 text-green-800 border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800' };
    }
    
    return { text: code.toString(), class: 'bg-red-100 text-red-800 border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800' };
};

const isResending = ref<number | null>(null);
const selectedLog = ref<WebhookLog | null>(null);
const isDetailOpen = ref(false);
const copiedId = ref<string | null>(null);

const openDetail = (log: WebhookLog) => {
    selectedLog.value = log;
    isDetailOpen.value = true;
};

const formatJSON = (val: any) => {
    if (!val) return '—';
    try {
        const parsed = typeof val === 'string' ? JSON.parse(val) : val;
        return JSON.stringify(parsed, null, 2);
    } catch (e) {
        return String(val);
    }
};

const copyText = (text: string, id: string) => {
    navigator.clipboard.writeText(text).then(() => {
        copiedId.value = id;
        toast.success('Disalin ke clipboard!');
        setTimeout(() => {
            copiedId.value = null;
        }, 2000);
    });
};

const resendWebhook = (logId: number) => {
    isResending.value = logId;
    router.post(`/dashboard/webhook/${logId}/resend`, {}, {
        onSuccess: () => {
            toast.success('Webhook berhasil dikirim ulang');
            if (selectedLog.value && selectedLog.value.id === logId) {
                const updated = props.logs.data.find(l => l.id === logId);
                if (updated) {
                    selectedLog.value = updated;
                }
            }
        },
        onError: () => {
            toast.error('Gagal mengirim ulang webhook');
        },
        onFinish: () => {
            isResending.value = null;
        }
    });
};
</script>

<template>
    <Head title="Webhook Logs" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex flex-col gap-1">
                <h1 class="text-2xl font-bold tracking-tight">Webhook Logs</h1>
                <p class="text-muted-foreground text-sm">Monitor webhook deliveries to your projects.</p>
            </div>

            <div class="rounded-xl border bg-card text-card-foreground shadow-sm overflow-hidden">
                <div class="relative w-full overflow-auto">
                    <table class="w-full caption-bottom text-sm border-collapse">
                        <thead>
                            <tr class="border-b transition-colors hover:bg-muted/50 text-muted-foreground font-medium">
                                <th class="h-12 px-4 text-left align-middle font-medium w-[180px]">Project</th>
                                <th class="h-12 px-4 text-left align-middle font-medium">URL</th>
                                <th class="h-12 px-4 text-center align-middle font-medium">Status Code</th>
                                <th class="h-12 px-4 text-right align-middle font-medium">Retries</th>
                                <th class="h-12 px-4 text-right align-middle font-medium">Last Sent At</th>
                                <th class="h-12 px-4 text-right align-middle font-medium">Created At</th>
                                <th class="h-12 px-4 text-center align-middle font-medium w-[150px]">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr v-for="log in logs.data" :key="log.id" class="transition-colors hover:bg-muted/50 group">
                                <td class="p-4 align-middle">
                                    <div class="font-semibold text-foreground">{{ log.project ? log.project.name : 'Unknown' }}</div>
                                </td>
                                <td class="p-4 align-middle">
                                    <div class="flex items-center gap-2 max-w-[200px] truncate">
                                        <span class="text-xs text-muted-foreground truncate" :title="log.project ? log.project.webhook_url : ''">
                                            {{ log.project ? log.project.webhook_url : '—' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="p-4 align-middle text-center">
                                    <span :class="['inline-flex items-center rounded-full border px-2.5 py-0.5 text-[11px] font-semibold', getStatusBadge(log.response_code).class]">
                                        {{ getStatusBadge(log.response_code).text }}
                                    </span>
                                </td>
                                <td class="p-4 align-middle text-right">
                                    <div class="flex items-center justify-end gap-1 text-muted-foreground">
                                        <RefreshCw class="h-3 w-3" />
                                        <span>{{ log.retries }}</span>
                                    </div>
                                </td>
                                <td class="p-4 align-middle text-right">
                                    <div class="flex flex-col items-end gap-0.5 text-xs text-muted-foreground">
                                        <span>{{ formatDate(log.last_sent_at) }}</span>
                                    </div>
                                </td>
                                <td class="p-4 align-middle text-right">
                                    <div class="flex flex-col items-end gap-0.5 text-xs">
                                        <div class="flex items-center gap-1 text-muted-foreground">
                                            <Clock class="h-3 w-3" />
                                            {{ formatDate(log.created_at) }}
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 align-middle text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <Button variant="outline" size="sm" class="h-8 px-2.5" @click="openDetail(log)">
                                            <Eye class="h-3.5 w-3.5 mr-1" />
                                            Detail
                                        </Button>
                                        <Button 
                                            variant="outline" 
                                            size="sm" 
                                            class="h-8 px-2.5 border-indigo-200 text-indigo-600 hover:bg-indigo-50 dark:border-indigo-800 dark:text-indigo-400 dark:hover:bg-indigo-950/30" 
                                            @click.stop="resendWebhook(log.id)"
                                            :disabled="isResending === log.id"
                                        >
                                            <RefreshCw class="h-3.5 w-3.5" :class="{'animate-spin': isResending === log.id}" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="logs.data.length === 0">
                                <td colspan="7" class="p-12 text-center text-muted-foreground">
                                    <div class="flex flex-col items-center gap-2">
                                        <ExternalLink class="h-8 w-8 opacity-20" />
                                        <p>No webhook logs found.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div v-if="logs.links.length > 3" class="flex items-center justify-between px-4 py-4 border-t bg-muted/20">
                    <div class="text-xs text-muted-foreground">
                        Showing <span class="font-medium">{{ logs.data.length }}</span> of <span class="font-medium">{{ logs.total }}</span> logs
                    </div>
                    <div class="flex gap-1">
                        <template v-for="(link, k) in logs.links" :key="k">
                            <div v-if="link.url === null" class="inline-flex items-center justify-center rounded-md text-xs font-medium border h-8 px-3 text-muted-foreground" v-html="link.label" />
                            <Link v-else :href="link.url" class="inline-flex items-center justify-center rounded-md text-xs font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border h-8 px-3 hover:bg-accent hover:text-accent-foreground" :class="{'bg-primary text-primary-foreground border-primary hover:bg-primary/90': link.active}" v-html="link.label" preserve-scroll />
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Modal -->
        <Dialog v-model:open="isDetailOpen">
            <DialogContent class="sm:max-w-[700px] max-h-[85vh] overflow-y-auto">
                <DialogHeader>
                    <DialogTitle class="flex items-center gap-2">
                        <span>Detail Webhook Log</span>
                        <span v-if="selectedLog" :class="['inline-flex items-center rounded-full border px-2.5 py-0.5 text-[11px] font-semibold', getStatusBadge(selectedLog.response_code).class]">
                            {{ getStatusBadge(selectedLog.response_code).text }}
                        </span>
                    </DialogTitle>
                </DialogHeader>

                <div v-if="selectedLog" class="space-y-4 py-4 text-sm">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-muted-foreground tracking-wider">Project</span>
                            <div class="font-medium text-foreground mt-0.5">{{ selectedLog.project?.name || 'Unknown' }}</div>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-muted-foreground tracking-wider">Retries</span>
                            <div class="font-medium text-foreground mt-0.5">{{ selectedLog.retries }}</div>
                        </div>
                        <div class="col-span-2">
                            <span class="text-[10px] uppercase font-bold text-muted-foreground tracking-wider">Webhook URL</span>
                            <div class="font-mono text-xs text-muted-foreground break-all mt-0.5">{{ selectedLog.project?.webhook_url || '—' }}</div>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-muted-foreground tracking-wider">Created At</span>
                            <div class="font-medium text-foreground mt-0.5 text-xs">{{ formatDate(selectedLog.created_at) }}</div>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-muted-foreground tracking-wider">Last Sent At</span>
                            <div class="font-medium text-foreground mt-0.5 text-xs">{{ formatDate(selectedLog.last_sent_at) }}</div>
                        </div>
                    </div>

                    <hr class="border-border" />

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-[10px] uppercase font-bold text-muted-foreground tracking-wider">Request Payload (JSON)</span>
                            <Button variant="ghost" size="sm" class="h-6 px-2 text-xs" @click="copyText(formatJSON(selectedLog.payload), 'payload')">
                                <component :is="copiedId === 'payload' ? Check : Copy" class="h-3 w-3 mr-1" />
                                {{ copiedId === 'payload' ? 'Copied' : 'Copy' }}
                            </Button>
                        </div>
                        <pre class="bg-muted p-3 rounded-lg text-xs font-mono overflow-x-auto max-h-[150px] border"><code>{{ formatJSON(selectedLog.payload) }}</code></pre>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-[10px] uppercase font-bold text-muted-foreground tracking-wider">Response Body</span>
                            <Button variant="ghost" size="sm" class="h-6 px-2 text-xs" @click="copyText(formatJSON(selectedLog.response_body), 'response_body')">
                                <component :is="copiedId === 'response_body' ? Check : Copy" class="h-3 w-3 mr-1" />
                                {{ copiedId === 'response_body' ? 'Copied' : 'Copy' }}
                            </Button>
                        </div>
                        <pre class="bg-muted p-3 rounded-lg text-xs font-mono overflow-x-auto max-h-[150px] border"><code>{{ formatJSON(selectedLog.response_body) }}</code></pre>
                    </div>
                </div>

                <DialogFooter class="flex sm:justify-between items-center gap-2">
                    <Button variant="outline" @click="isDetailOpen = false">Tutup</Button>
                    <Button 
                        v-if="selectedLog"
                        @click="resendWebhook(selectedLog.id)" 
                        :disabled="isResending === selectedLog.id"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white"
                    >
                        <RefreshCw class="mr-2 h-4 w-4" :class="{'animate-spin': isResending === selectedLog.id}" />
                        {{ isResending === selectedLog.id ? 'Mengirim...' : 'Kirim Ulang' }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
