<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, usePage, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { 
    Plus, Edit, Trash2, Search, CheckCircle, XCircle, 
    Image as ImageIcon, HelpCircle, Layers, Settings,
    FileText, DollarSign, Percent, ToggleLeft, ShieldAlert,
    RefreshCw
} from 'lucide-vue-next';

interface PaymentChannel {
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
    created_at?: string;
}

const props = defineProps<{
    paymentChannels: PaymentChannel[];
}>();

const page = usePage();
const search = ref('');
const isModalOpen = ref(false);
const isDeleteOpen = ref(false);
const modalMode = ref<'create' | 'edit'>('create');
const selectedChannel = ref<PaymentChannel | null>(null);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Payment Channels',
        href: '/dashboard/admin/payment-channels',
    },
];

// Form setup using Inertia's useForm
const form = useForm({
    group: '',
    method_code: '',
    method_name: '',
    gateway: 'tripay',
    image: '',
    image_file: null as File | null,
    description: '',
    is_qris: false,
    min_amount: null as number | null,
    max_amount: null as number | null,
    fee_percent: 0,
    fee_flat: 0,
    active: true,
});

// Search filter
const filteredChannels = computed(() => {
    if (!search.value) return props.paymentChannels;
    const term = search.value.toLowerCase();
    return props.paymentChannels.filter(c => 
        c.method_name.toLowerCase().includes(term) || 
        c.method_code.toLowerCase().includes(term) ||
        (c.group && c.group.toLowerCase().includes(term)) ||
        c.gateway.toLowerCase().includes(term)
    );
});

// File input handler
const handleImageUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        form.image_file = target.files[0];
    }
};

// Open create modal
const openCreateModal = () => {
    modalMode.value = 'create';
    selectedChannel.value = null;
    form.reset();
    form.clearErrors();
    form.group = 'E-Wallet';
    form.gateway = 'tripay';
    form.is_qris = false;
    form.active = true;
    form.fee_percent = 0;
    form.fee_flat = 0;
    isModalOpen.value = true;
};

// Open edit modal
const openEditModal = (channel: PaymentChannel) => {
    modalMode.value = 'edit';
    selectedChannel.value = channel;
    form.clearErrors();
    
    form.group = channel.group || 'E-Wallet';
    form.method_code = channel.method_code;
    form.method_name = channel.method_name;
    form.gateway = channel.gateway;
    form.image = channel.image || '';
    form.image_file = null;
    form.description = channel.description || '';
    form.is_qris = !!channel.is_qris;
    form.min_amount = channel.min_amount;
    form.max_amount = channel.max_amount;
    form.fee_percent = Number(channel.fee_percent);
    form.fee_flat = channel.fee_flat;
    form.active = !!channel.active;
    
    isModalOpen.value = true;
};

// Open delete modal
const openDeleteModal = (channel: PaymentChannel) => {
    selectedChannel.value = channel;
    isDeleteOpen.value = true;
};

// Close modals
const closeModal = () => {
    isModalOpen.value = false;
    form.reset();
};

const closeDeleteModal = () => {
    isDeleteOpen.value = false;
    selectedChannel.value = null;
};

// Form submit
const submitForm = () => {
    if (modalMode.value === 'create') {
        form.post(route('dashboard.admin.payment-channels.store'), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    } else if (selectedChannel.value) {
        form.post(route('dashboard.admin.payment-channels.update', selectedChannel.value.id), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    }
};

// Confirm delete
const confirmDelete = () => {
    if (!selectedChannel.value) return;
    
    form.delete(route('dashboard.admin.payment-channels.delete', selectedChannel.value.id), {
        preserveScroll: true,
        onSuccess: () => closeDeleteModal(),
    });
};

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

const isSyncing = ref(false);
const syncChannels = () => {
    isSyncing.value = true;
    router.post(route('dashboard.admin.payment-channels.sync'), {}, {
        preserveScroll: true,
        onFinish: () => {
            isSyncing.value = false;
        }
    });
};

const toggleStatus = (channel: PaymentChannel) => {
    router.post(route('dashboard.admin.payment-channels.update', channel.id), {
        group: channel.group,
        method_code: channel.method_code,
        method_name: channel.method_name,
        gateway: channel.gateway,
        image: channel.image || '',
        description: channel.description || '',
        is_qris: !!channel.is_qris,
        min_amount: channel.min_amount,
        max_amount: channel.max_amount,
        fee_percent: Number(channel.fee_percent),
        fee_flat: channel.fee_flat,
        active: !channel.active,
    }, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Payment Channels" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 px-10">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex flex-col gap-1">
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100">Payment Channels</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Manage payment gateways, transaction fees, and operational limits.</p>
                </div>
                <div class="flex items-center gap-3">
                    <button 
                        @click="syncChannels"
                        :disabled="isSyncing"
                        class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50"
                    >
                        <RefreshCw :class="['h-4 w-4', isSyncing ? 'animate-spin' : '']" />
                        Sync All Projects
                    </button>
                    <button 
                        @click="openCreateModal"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 shadow-md hover:shadow-lg transition-all dark:bg-indigo-600 dark:hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        <Plus class="h-4.5 w-4.5" />
                        Create Channel
                    </button>
                </div>
            </div>

            <!-- Toast Messages -->
            <div v-if="page.props.flash?.success" class="flex items-center gap-3 rounded-xl bg-green-50 p-4 text-sm text-green-800 border border-green-200/50 dark:bg-green-950/30 dark:text-green-400 dark:border-green-900/30">
                <CheckCircle class="h-5 w-5 text-green-600 dark:text-green-400" />
                <span class="font-medium">{{ page.props.flash.success }}</span>
            </div>

            <div v-if="page.props.flash?.error" class="flex items-center gap-3 rounded-xl bg-red-50 p-4 text-sm text-red-800 border border-red-200/50 dark:bg-red-950/30 dark:text-red-400 dark:border-red-900/30">
                <XCircle class="h-5 w-5 text-red-600 dark:text-red-400" />
                <span class="font-medium">{{ page.props.flash.error }}</span>
            </div>

            <!-- Main Table Card -->
            <div class="flex flex-col rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900/40 backdrop-blur-md overflow-hidden">
                <!-- Table toolbar -->
                <div class="flex items-center justify-between border-b border-gray-200 p-4 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/20">
                    <div class="relative w-full max-w-sm">
                        <div class="pointer-events-none absolute inset-y-0 start-0 flex items-center ps-3">
                            <Search class="h-4 w-4 text-gray-400" />
                        </div>
                        <input 
                            type="text" 
                            v-model="search"
                            class="block w-full rounded-xl border border-gray-300 bg-white p-2.5 ps-10 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400 dark:focus:border-indigo-500 dark:focus:ring-indigo-500 shadow-sm" 
                            placeholder="Search code, name, gateway or group..." 
                        />
                    </div>
                </div>

                <!-- Table Content -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-500 dark:text-gray-400">
                        <thead class="bg-gray-50/70 text-xs uppercase text-gray-700 dark:bg-gray-800/40 dark:text-gray-400 border-b border-gray-200 dark:border-gray-800">
                            <tr>
                                <th scope="col" class="px-6 py-4">Channel Details</th>
                                <th scope="col" class="px-6 py-4">Group / Gateway</th>
                                <th scope="col" class="px-6 py-4">Limits (Min / Max)</th>
                                <th scope="col" class="px-6 py-4">Fees</th>
                                <th scope="col" class="px-6 py-4 text-center">QRIS</th>
                                <th scope="col" class="px-6 py-4 text-center">Status</th>
                                <th scope="col" class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                            <tr v-for="channel in filteredChannels" :key="channel.id" class="bg-white hover:bg-gray-50/50 dark:bg-transparent dark:hover:bg-gray-800/20 transition-colors">
                                <!-- Logo and Name -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-12 flex-shrink-0 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center p-1.5 overflow-hidden border border-gray-200 dark:border-gray-700">
                                            <img 
                                                v-if="channel.image" 
                                                :src="getImageUrl(channel.image)" 
                                                :alt="channel.method_name" 
                                                class="h-full w-full object-contain"
                                            />
                                            <ImageIcon v-else class="h-6 w-6 text-gray-400" />
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-900 dark:text-white flex items-center gap-1.5">
                                                {{ channel.method_name }}
                                            </div>
                                            <div class="font-mono text-xs text-gray-500 dark:text-gray-400">
                                                {{ channel.method_code }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <!-- Group / Gateway -->
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-800 dark:bg-gray-800 dark:text-gray-300">
                                        {{ channel.group || 'N/A' }}
                                    </span>
                                    <div class="text-xs text-gray-500 mt-1">Gateway: <span class="font-medium text-gray-700 dark:text-gray-300">{{ channel.gateway }}</span></div>
                                </td>
                                <!-- Limits -->
                                <td class="px-6 py-4 text-xs font-medium text-gray-900 dark:text-gray-100">
                                    <div>Min: {{ formatCurrency(channel.min_amount) }}</div>
                                    <div class="text-gray-500 dark:text-gray-400 mt-0.5">Max: {{ formatCurrency(channel.max_amount) }}</div>
                                </td>
                                <!-- Fees -->
                                <td class="px-6 py-4 text-xs font-medium text-gray-900 dark:text-gray-100">
                                    <div v-if="Number(channel.fee_percent) > 0 || channel.fee_flat > 0" class="space-y-0.5">
                                        <div v-if="Number(channel.fee_percent) > 0" class="flex items-center gap-1">
                                            <Percent class="h-3 w-3 text-indigo-500" />
                                            <span>{{ channel.fee_percent }}%</span>
                                        </div>
                                        <div v-if="channel.fee_flat > 0" class="flex items-center gap-1">
                                            <DollarSign class="h-3 w-3 text-emerald-500" />
                                            <span>{{ formatCurrency(channel.fee_flat) }}</span>
                                        </div>
                                    </div>
                                    <span v-else class="text-xs text-green-600 dark:text-green-400 font-semibold uppercase tracking-wide">Free</span>
                                </td>
                                <!-- QRIS Indicator -->
                                <td class="px-6 py-4 text-center">
                                    <span 
                                        :class="[
                                            'inline-flex items-center justify-center rounded-full px-2.5 py-0.5 text-xs font-bold uppercase',
                                            channel.is_qris ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-950/40 dark:text-indigo-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400'
                                        ]"
                                    >
                                        {{ channel.is_qris ? 'QRIS' : 'Non-QRIS' }}
                                    </span>
                                </td>
                                <!-- Status Toggle Switch -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center">
                                        <label :for="'active-toggle-' + channel.id" class="relative inline-flex items-center cursor-pointer">
                                            <input 
                                                type="checkbox" 
                                                :id="'active-toggle-' + channel.id" 
                                                :checked="!!channel.active" 
                                                @change="toggleStatus(channel)"
                                                class="sr-only peer"
                                            />
                                            <div class="w-10 h-6 bg-gray-200 dark:bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-indigo-600"></div>
                                        </label>
                                    </div>
                                </td>
                                <!-- Actions -->
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button 
                                            @click="openEditModal(channel)" 
                                            class="inline-flex items-center gap-1 rounded-lg border border-gray-300 bg-white p-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition"
                                            title="Edit"
                                        >
                                            <Edit class="h-4 w-4" />
                                        </button>
                                        <button 
                                            @click="openDeleteModal(channel)" 
                                            class="inline-flex items-center gap-1 rounded-lg border border-red-200 bg-red-50 p-1.5 text-xs font-medium text-red-600 hover:bg-red-100 dark:border-red-900/50 dark:bg-red-950/20 dark:text-red-400 dark:hover:bg-red-950/50 transition"
                                            title="Delete"
                                        >
                                            <Trash2 class="h-4 w-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="filteredChannels.length === 0">
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    No payment channels found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add/Edit Channel Modal -->
        <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black/60 backdrop-blur-sm p-4">
            <div class="relative w-full max-w-2xl rounded-2xl bg-white shadow-2xl dark:bg-gray-900 border border-gray-200 dark:border-gray-800 transition-all flex flex-col max-h-[90vh]">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-gray-200 p-5 dark:border-gray-800">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <Settings class="h-5 w-5 text-indigo-500" />
                        {{ modalMode === 'create' ? 'Create New Payment Channel' : 'Edit Payment Channel' }}
                    </h3>
                    <button @click="closeModal" type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-800 dark:hover:text-white transition">
                        <svg class="h-3 w-3" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="overflow-y-auto p-6 space-y-6">
                    <form @submit.prevent="submitForm" id="channelForm" class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        
                        <!-- Group -->
                        <div>
                            <label for="group" class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                                <Layers class="h-4 w-4 text-gray-400" /> Payment Group
                            </label>
                            <select 
                                id="group" 
                                v-model="form.group" 
                                class="block w-full rounded-xl border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-indigo-500"
                            >
                                <option value="E-Wallet">E-Wallet</option>
                                <option value="Virtual Account">Virtual Account</option>
                                <option value="Convenience Store">Convenience Store</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Other">Other</option>
                            </select>
                            <div v-if="form.errors.group" class="mt-1 text-xs text-red-500">{{ form.errors.group }}</div>
                        </div>

                        <!-- Gateway -->
                        <div>
                            <label for="gateway" class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                                <Plus class="h-4 w-4 text-gray-400" /> Gateway Handler
                            </label>
                            <input 
                                type="text" 
                                id="gateway" 
                                v-model="form.gateway" 
                                class="block w-full rounded-xl border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-indigo-500" 
                                required
                            />
                            <div v-if="form.errors.gateway" class="mt-1 text-xs text-red-500">{{ form.errors.gateway }}</div>
                        </div>

                        <!-- Method Code -->
                        <div>
                            <label for="method_code" class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                                <FileText class="h-4 w-4 text-gray-400" /> Method Code (Unique ID)
                            </label>
                            <input 
                                type="text" 
                                id="method_code" 
                                v-model="form.method_code" 
                                class="block w-full rounded-xl border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-indigo-500 font-mono" 
                                placeholder="e.g. qris, bca_va, alfamart"
                                required
                            />
                            <div v-if="form.errors.method_code" class="mt-1 text-xs text-red-500">{{ form.errors.method_code }}</div>
                        </div>

                        <!-- Method Name -->
                        <div>
                            <label for="method_name" class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                                <FileText class="h-4 w-4 text-gray-400" /> Public Method Name
                            </label>
                            <input 
                                type="text" 
                                id="method_name" 
                                v-model="form.method_name" 
                                class="block w-full rounded-xl border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-indigo-500" 
                                placeholder="e.g. QRIS, BCA Virtual Account"
                                required
                            />
                            <div v-if="form.errors.method_name" class="mt-1 text-xs text-red-500">{{ form.errors.method_name }}</div>
                        </div>

                        <!-- Image File (Optional Upload) -->
                        <div class="col-span-1 md:col-span-2">
                            <label for="image_file" class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                                <ImageIcon class="h-4 w-4 text-gray-400" /> Upload Icon Image (Replaces current path)
                            </label>
                            <input 
                                type="file" 
                                id="image_file" 
                                @change="handleImageUpload" 
                                accept="image/*"
                                class="block w-full text-sm text-gray-900 border border-gray-300 rounded-xl cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-850 dark:border-gray-700 p-2" 
                            />
                            <div v-if="form.errors.image_file" class="mt-1 text-xs text-red-500">{{ form.errors.image_file }}</div>
                        </div>

                        <!-- Image path / URL fallback -->
                        <div class="col-span-1 md:col-span-2">
                            <label for="image" class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                                <ImageIcon class="h-4 w-4 text-gray-400" /> Or Image path / URL
                            </label>
                            <input 
                                type="text" 
                                id="image" 
                                v-model="form.image" 
                                class="block w-full rounded-xl border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-indigo-500" 
                                placeholder="e.g. images/channels/qris.png"
                            />
                            <div v-if="form.errors.image" class="mt-1 text-xs text-red-500">{{ form.errors.image }}</div>
                        </div>

                        <!-- Description -->
                        <div class="col-span-1 md:col-span-2">
                            <label for="description" class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                                <HelpCircle class="h-4 w-4 text-gray-400" /> Channel Description
                            </label>
                            <textarea 
                                id="description" 
                                v-model="form.description" 
                                rows="3"
                                class="block w-full rounded-xl border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-indigo-500" 
                                placeholder="Details about this payment method..."
                            ></textarea>
                            <div v-if="form.errors.description" class="mt-1 text-xs text-red-500">{{ form.errors.description }}</div>
                        </div>

                        <!-- Min Amount -->
                        <div>
                            <label for="min_amount" class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                                <DollarSign class="h-4 w-4 text-gray-400" /> Minimum Amount (IDR)
                            </label>
                            <input 
                                type="number" 
                                id="min_amount" 
                                v-model="form.min_amount" 
                                class="block w-full rounded-xl border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-indigo-500" 
                                placeholder="e.g. 1000"
                                min="0"
                            />
                            <div v-if="form.errors.min_amount" class="mt-1 text-xs text-red-500">{{ form.errors.min_amount }}</div>
                        </div>

                        <!-- Max Amount -->
                        <div>
                            <label for="max_amount" class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                                <DollarSign class="h-4 w-4 text-gray-400" /> Maximum Amount (IDR)
                            </label>
                            <input 
                                type="number" 
                                id="max_amount" 
                                v-model="form.max_amount" 
                                class="block w-full rounded-xl border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-indigo-500" 
                                placeholder="e.g. 50000000"
                                min="0"
                            />
                            <div v-if="form.errors.max_amount" class="mt-1 text-xs text-red-500">{{ form.errors.max_amount }}</div>
                        </div>

                        <!-- Fee Percent -->
                        <div>
                            <label for="fee_percent" class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                                <Percent class="h-4 w-4 text-gray-400" /> Fee Percent (%)
                            </label>
                            <input 
                                type="number" 
                                id="fee_percent" 
                                v-model="form.fee_percent" 
                                step="0.01"
                                class="block w-full rounded-xl border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-indigo-500" 
                                placeholder="e.g. 0.70"
                                min="0"
                                max="100"
                                required
                            />
                            <div v-if="form.errors.fee_percent" class="mt-1 text-xs text-red-500">{{ form.errors.fee_percent }}</div>
                        </div>

                        <!-- Fee Flat -->
                        <div>
                            <label for="fee_flat" class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                                <DollarSign class="h-4 w-4 text-gray-400" /> Fee Flat (IDR)
                            </label>
                            <input 
                                type="number" 
                                id="fee_flat" 
                                v-model="form.fee_flat" 
                                class="block w-full rounded-xl border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-indigo-500" 
                                placeholder="e.g. 1500"
                                min="0"
                                required
                            />
                            <div v-if="form.errors.fee_flat" class="mt-1 text-xs text-red-500">{{ form.errors.fee_flat }}</div>
                        </div>

                        <!-- QRIS Switch -->
                        <div class="flex items-center justify-between p-3 rounded-xl border border-gray-200 dark:border-gray-800">
                            <div class="flex flex-col">
                                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-1">
                                    <ToggleLeft class="h-4 w-4 text-gray-400" /> Is QRIS?
                                </span>
                                <span class="text-xs text-gray-400">Specifies if this uses QRIS imaging</span>
                            </div>
                            <input 
                                type="checkbox" 
                                v-model="form.is_qris"
                                class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600"
                            />
                        </div>

                        <!-- Active Switch -->
                        <div class="flex items-center justify-between p-3 rounded-xl border border-gray-200 dark:border-gray-800">
                            <div class="flex flex-col">
                                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-1">
                                    <ToggleLeft class="h-4 w-4 text-gray-400" /> Active status
                                </span>
                                <span class="text-xs text-gray-400">Controls checkout availability</span>
                            </div>
                            <input 
                                type="checkbox" 
                                v-model="form.active"
                                class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600"
                            />
                        </div>

                    </form>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end gap-3 border-t border-gray-200 p-5 dark:border-gray-800">
                    <button 
                        @click="closeModal" 
                        type="button" 
                        class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit" 
                        form="channelForm"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 disabled:opacity-50 dark:focus:ring-indigo-800 shadow-md hover:shadow-lg transition-all"
                    >
                        Save Channel
                    </button>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="isDeleteOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <div class="relative w-full max-w-md rounded-2xl bg-white shadow-2xl dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-6 flex flex-col gap-5">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-full bg-red-100 dark:bg-red-950/40 flex items-center justify-center flex-shrink-0">
                        <ShieldAlert class="h-5 w-5 text-red-600 dark:text-red-400" />
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Delete Payment Channel</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">This action cannot be undone.</p>
                    </div>
                </div>

                <div class="text-sm text-gray-600 dark:text-gray-300">
                    Are you sure you want to delete <span class="font-bold text-gray-900 dark:text-white">{{ selectedChannel?.method_name }}</span>? All settings for this gateway channel will be permanently removed.
                </div>

                <div class="flex items-center justify-end gap-3 mt-2">
                    <button 
                        @click="closeDeleteModal" 
                        type="button" 
                        class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition"
                    >
                        Cancel
                    </button>
                    <button 
                        @click="confirmDelete" 
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-700 disabled:opacity-50 shadow-md hover:shadow-lg transition-all"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>