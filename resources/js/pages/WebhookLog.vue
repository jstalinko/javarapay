<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Clock, ExternalLink, RefreshCw } from 'lucide-vue-next';

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
                            </tr>
                            <tr v-if="logs.data.length === 0">
                                <td colspan="6" class="p-12 text-center text-muted-foreground">
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
    </AppLayout>
</template>
