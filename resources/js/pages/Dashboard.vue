<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Wallet, Clock, ArrowRightLeft, Boxes, Megaphone, Plus, User, Copy, ExternalLink } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

// UI components
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

const props = defineProps<{
    saldo: number;
    saldoTertunda: number;
    totalTransaksi: number;
    totalProject: number;
    orders: any; // Paginated orders
    projects: any[]; // User projects
    announcements?: { id: number; title: string; body: string; date: string }[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
];

const isModalOpen = ref(false);

const form = useForm({
    project_id: '',
    amount: 1000,
    note: '',
});

const openModal = () => {
    if (props.projects.length === 0) {
        toast.error('Please create a project first.');
        return;
    }
    form.reset();
    if (props.projects.length > 0) {
        form.project_id = props.projects[0].id.toString();
    }
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
};

const submit = () => {
    form.post('/dashboard/order', {
        onSuccess: () => {
            toast.success('Payment link created successfully');
            closeModal();
        },
        onError: () => {
            toast.error('Failed to create payment link');
        }
    });
};

const formatIDR = (value: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(value);
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
};

const copyToClipboard = (order: any) => {
    const url = `${window.location.origin}/payment/${order.project_id}/${order.txid}`;
    navigator.clipboard.writeText(url);
    toast.success('Payment link copied to clipboard');
};

const visitLink = (order: any) => {
    const url = `${window.location.origin}/payment/${order.project_id}/${order.txid}`;
    window.open(url, '_blank');
};

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
                <!-- Saldo -->
                <Card class="relative overflow-hidden border-sidebar-border/70 group hover:shadow-md transition-all duration-300">
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-emerald-500 to-teal-600" />
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium text-muted-foreground">Saldo</CardTitle>
                        <div class="h-8 w-8 rounded-lg bg-emerald-500/10 flex items-center justify-center transition-transform group-hover:scale-110">
                            <Wallet class="h-4 w-4 text-emerald-500" />
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ formatIDR(saldo) }}</div>
                    </CardContent>
                </Card>

                <!-- Saldo Tertunda -->
                <Card class="relative overflow-hidden border-sidebar-border/70 group hover:shadow-md transition-all duration-300">
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-amber-500 to-orange-600" />
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium text-muted-foreground">Saldo Tertunda</CardTitle>
                        <div class="h-8 w-8 rounded-lg bg-amber-500/10 flex items-center justify-center transition-transform group-hover:scale-110">
                            <Clock class="h-4 w-4 text-amber-500" />
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ formatIDR(saldoTertunda) }}</div>
                    </CardContent>
                </Card>

                <!-- Total Transaksi -->
                <Card class="relative overflow-hidden border-sidebar-border/70 group hover:shadow-md transition-all duration-300">
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-blue-500 to-indigo-600" />
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium text-muted-foreground">Total Transaksi</CardTitle>
                        <div class="h-8 w-8 rounded-lg bg-blue-500/10 flex items-center justify-center transition-transform group-hover:scale-110">
                            <ArrowRightLeft class="h-4 w-4 text-blue-500" />
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ totalTransaksi }}</div>
                    </CardContent>
                </Card>

                <!-- Total Project -->
                <Card class="relative overflow-hidden border-sidebar-border/70 group hover:shadow-md transition-all duration-300">
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-violet-500 to-purple-600" />
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium text-muted-foreground">Total Project</CardTitle>
                        <div class="h-8 w-8 rounded-lg bg-violet-500/10 flex items-center justify-center transition-transform group-hover:scale-110">
                            <Boxes class="h-4 w-4 text-violet-500" />
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ totalProject }}</div>
                    </CardContent>
                </Card>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Left: Orders Table -->
                <div class="lg:col-span-2 flex flex-col gap-4">
                    <Card class="border-sidebar-border/70 bg-card overflow-hidden">
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 border-b bg-muted/20 px-6 py-4">
                            <CardTitle class="text-base font-bold">Payment Links</CardTitle>
                            <Button @click="openModal" size="sm">
                                <Plus class="mr-2 h-4 w-4" />
                                Create payment link
                            </Button>
                        </CardHeader>
                        <CardContent class="p-0">
                            <div class="relative w-full overflow-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-muted/50 border-b">
                                        <tr class="text-left font-medium text-muted-foreground uppercase text-[10px] tracking-wider">
                                            <th class="h-10 px-6 align-middle">Project</th>
                                            <th class="h-10 px-6 align-middle">Amount</th>
                                            <th class="h-10 px-6 align-middle">Note</th>
                                            <th class="h-10 px-6 align-middle text-right">Date</th>
                                            <th class="h-10 px-6 align-middle text-right">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-sidebar-border/70">
                                        <tr v-for="order in orders.data" :key="order.id" class="hover:bg-muted/30 transition-colors">
                                            <td class="px-6 py-4 align-middle font-medium">{{ order.project.name }}</td>
                                            <td class="px-6 py-4 align-middle font-semibold text-primary">{{ formatIDR(order.amount) }}</td>
                                            <td class="px-6 py-4 align-middle truncate max-w-[200px]" :title="order.note">
                                                <span class="text-muted-foreground">{{ order.note || '—' }}</span>
                                            </td>
                                            <td class="px-6 py-4 align-middle text-right text-muted-foreground whitespace-nowrap">{{ formatDate(order.created_at) }}</td>
                                            <td class="px-6 py-4 align-middle text-right">
                                                <div class="flex items-center justify-end gap-2">
                                                    <Button variant="outline" size="icon" class="h-8 w-8" @click="copyToClipboard(order)" title="Copy Link">
                                                        <Copy class="h-3.5 w-3.5" />
                                                    </Button>
                                                    <Button variant="outline" size="icon" class="h-8 w-8" @click="visitLink(order)" title="Visit Link">
                                                        <ExternalLink class="h-3.5 w-3.5" />
                                                    </Button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr v-if="orders.data.length === 0">
                                            <td colspan="5" class="p-12 text-center text-muted-foreground">No payment links created yet.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            <div v-if="orders.links.length > 3" class="flex items-center justify-between px-6 py-3 border-t bg-muted/10">
                                <p class="text-[10px] text-muted-foreground uppercase font-bold">Page {{ orders.current_page }} / {{ orders.last_page }}</p>
                                <div class="flex gap-1">
                                    <template v-for="(link, i) in orders.links" :key="i">
                                        <div v-if="link.url === null" class="h-8 px-3 rounded border border-sidebar-border/70 flex items-center text-xs opacity-50" v-html="link.label"></div>
                                        <Link v-else :href="link.url" class="h-8 px-3 rounded border border-sidebar-border/70 flex items-center text-xs hover:bg-muted transition-colors" :class="{'bg-primary text-primary-foreground border-primary': link.active}" v-html="link.label" preserve-scroll />
                                    </template>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Right: Announcements -->
                <div class="flex flex-col gap-4">
                    <Card class="border-sidebar-border/70 bg-card h-full">
                        <CardHeader class="border-b bg-muted/20 px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-500/10">
                                    <Megaphone class="h-4 w-4 text-blue-500" />
                                </div>
                                <CardTitle class="text-base font-bold">Announcements</CardTitle>
                            </div>
                        </CardHeader>
                        <CardContent class="p-0">
                            <div class="divide-y divide-sidebar-border/70">
                                <div
                                    v-for="ann in defaultAnnouncements"
                                    :key="ann.id"
                                    class="px-6 py-5 transition-colors hover:bg-muted/50"
                                >
                                    <div class="mb-2 flex items-center justify-between">
                                        <h3 class="text-sm font-bold text-foreground">{{ ann.title }}</h3>
                                        <span class="text-[10px] text-muted-foreground uppercase font-black tracking-tighter">{{ ann.date }}</span>
                                    </div>
                                    <p class="text-xs leading-relaxed text-muted-foreground/80">{{ ann.body }}</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>

        <!-- Create Payment Link Modal -->
        <Dialog v-model:open="isModalOpen">
            <DialogContent class="sm:max-w-[425px]">
                <form @submit.prevent="submit">
                    <DialogHeader>
                        <DialogTitle>Create payment link</DialogTitle>
                    </DialogHeader>
                    
                    <div class="grid gap-4 py-4">
                        <div class="grid gap-2">
                            <Label for="project">Select Project</Label>
                            <select id="project" v-model="form.project_id" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                                <option v-for="project in projects" :key="project.id" :value="project.id">{{ project.name }}</option>
                            </select>
                            <span v-if="form.errors.project_id" class="text-xs text-red-500">{{ form.errors.project_id }}</span>
                        </div>

                        <div class="grid gap-2">
                            <Label for="amount">Amount (IDR)</Label>
                            <Input id="amount" type="number" v-model="form.amount" min="1000" required />
                            <p class="text-[10px] text-muted-foreground">Minimum transaction: IDR 1,000</p>
                            <span v-if="form.errors.amount" class="text-xs text-red-500">{{ form.errors.amount }}</span>
                        </div>

                        <div class="grid gap-2">
                            <Label for="note">Note (Optional)</Label>
                            <Input id="note" v-model="form.note" placeholder="pembelian produk abc..." />
                            <span v-if="form.errors.note" class="text-xs text-red-500">{{ form.errors.note }}</span>
                        </div>
                    </div>

                    <DialogFooter>
                        <Button variant="outline" @click="closeModal" type="button">Cancel</Button>
                        <Button type="submit" :disabled="form.processing">
                            <span v-if="form.processing">Creating...</span>
                            <span v-else>Create Link</span>
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
