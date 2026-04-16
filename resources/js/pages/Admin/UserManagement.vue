<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, usePage, router, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { UserCog, Search, Settings, BoxSelect, AlertCircle, ShieldAlert, KeyRound, CheckCircle } from 'lucide-vue-next';

interface User {
    id: number;
    name: string;
    email: string;
    phone: string | null;
    address: string | null;
    province: string | null;
    city: string | null;
    postal_code: string | null;
    limit_project: number;
    limit_bank: number;
    withdraw_fee_type: string;
    withdraw_fee: number;
    fee_tx_type: string;
    fee_tx: number;
    role: string;
    status: string;
    created_at: string;
}

const props = defineProps<{
    users: {
        data: User[];
        links: any[];
    };
    filters: {
        search?: string;
    };
}>();

const page = usePage();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'User Management',
        href: '/dashboard/admin/user-management',
    },
];

const selectedUser = ref<User | null>(null);
const search = ref(props.filters.search || '');

let debounceTimer: ReturnType<typeof setTimeout>;
watch(search, (value) => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get(route('dashboard.admin.user-management'), { search: value }, { preserveState: true, replace: true, preserveScroll: true });
    }, 300);
});

const form = useForm({
    status: '',
    change_password: '',
});

const openDetails = (user: User) => {
    selectedUser.value = user;
    form.status = user.status;
    form.change_password = '';
};

const closeDetails = () => {
    selectedUser.value = null;
};

const submitUpdate = () => {
    if (!selectedUser.value) return;
    
    // Send only truthy password if they want to change it
    const data = form.change_password ? form : form.transform((data) => ({ ...data, change_password: '' }));
    
    data.post(route('dashboard.admin.user.update', selectedUser.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeDetails();
            form.reset('change_password');
        },
    });
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

const badgeClass = (status: string) => {
    switch (status) {
        case 'active':
            return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
        case 'inactive':
            return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300';
        case 'banned':
            return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400';
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300';
    }
};
</script>

<template>
    <Head title="User Management" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 px-10">
            <!-- Header -->
            <div class="flex flex-col gap-2">
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100">User Management</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">View user details, update access statuses, and manage security credentials.</p>
            </div>
            
            <div v-if="page.props.flash?.success" class="rounded-lg bg-green-50 p-4 text-sm text-green-800 dark:bg-green-900/30 dark:text-green-400">
                {{ page.props.flash.success }}
            </div>

            <!-- User List -->
            <div class="flex flex-col rounded-2xl border border-sidebar-border/70 bg-white shadow-sm dark:border-sidebar-border dark:bg-sidebar">
                <!-- Toolbar -->
                <div class="flex items-center justify-between border-b border-gray-200 p-4 dark:border-gray-800">
                    <div class="relative w-full max-w-sm">
                        <div class="pointer-events-none absolute inset-y-0 start-0 flex items-center ps-3">
                            <Search class="h-4 w-4 text-gray-400" />
                        </div>
                        <input 
                            type="text" 
                            v-model="search"
                            class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 ps-10 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-indigo-500 dark:focus:ring-indigo-500" 
                            placeholder="Search by name or email..." 
                        />
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-500 dark:text-gray-400">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-700 dark:bg-gray-800/50 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-4">User</th>
                                <th scope="col" class="px-6 py-4">Role</th>
                                <th scope="col" class="px-6 py-4">Status</th>
                                <th scope="col" class="px-6 py-4">Joined Date</th>
                                <th scope="col" class="px-6 py-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="user in users.data" :key="user.id" class="border-b bg-white hover:bg-gray-50 dark:border-gray-800 dark:bg-sidebar dark:hover:bg-gray-800/50">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900 dark:text-white flex items-center gap-2">
                                        {{ user.name }}
                                        <ShieldAlert v-if="user.role === 'admin'" class="h-3.5 w-3.5 text-indigo-500" />
                                    </div>
                                    <div class="text-xs text-gray-500">{{ user.email }}</div>
                                </td>
                                <td class="px-6 py-4 uppercase text-xs font-semibold tracking-wide">
                                    {{ user.role }}
                                </td>
                                <td class="px-6 py-4">
                                    <span :class="['rounded px-2.5 py-0.5 text-xs font-medium', badgeClass(user.status)]">
                                        {{ user.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    {{ formatDate(user.created_at) }}
                                </td>
                                <td class="px-6 py-4">
                                    <button 
                                        @click="openDetails(user)" 
                                        class="inline-flex items-center gap-2 rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition"
                                    >
                                        <UserCog class="h-4 w-4" />
                                        Manage
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="users.data.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    No users found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="users.links && users.links.length > 3" class="flex items-center justify-center border-t border-gray-200 p-4 dark:border-gray-800 sm:justify-between">
                    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700 dark:text-gray-400">
                                Showing
                                <span class="font-medium text-gray-900 dark:text-white">{{ users.data.length }}</span>
                                results
                            </p>
                        </div>
                        <div>
                            <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                                <template v-for="(link, i) in users.links" :key="i">
                                    <div 
                                        v-if="link.url === null" 
                                        class="cursor-not-allowed relative inline-flex items-center border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400"
                                        v-html="link.label" 
                                    ></div>
                                    <Link 
                                        v-else 
                                        :href="link.url"
                                        :class="[
                                            link.active ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600 dark:bg-indigo-900/30 dark:border-indigo-500 dark:text-indigo-400' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-700',
                                            'relative inline-flex items-center border px-4 py-2 text-sm font-medium'
                                        ]"
                                        v-html="link.label"
                                    ></Link>
                                </template>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details Modal -->
        <div v-if="selectedUser" class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-black/50 p-4 md:p-0">
            <div class="relative w-full max-w-3xl rounded-2xl bg-white shadow-xl dark:bg-gray-900 border border-gray-200 dark:border-gray-800 transition-all">
                
                <div class="flex items-center justify-between border-b border-gray-200 p-5 dark:border-gray-800">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">User Data Management</h3>
                    <button @click="closeDetails" type="button" class="ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-800 dark:hover:text-white">
                        <svg class="h-3 w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>

                <div class="flex flex-col md:flex-row h-[60vh] md:h-auto overflow-y-auto">
                    <!-- Readonly Info Col -->
                    <div class="p-6 md:w-1/2 space-y-6 md:border-r border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/20">
                        <div>
                            <h4 class="mb-3 text-sm font-semibold uppercase tracking-wider text-gray-500">Profile Identification</h4>
                            <div class="space-y-3 text-sm">
                                <div><span class="text-gray-500">Name:</span> <span class="font-medium text-gray-900 dark:text-white">{{ selectedUser.name }}</span></div>
                                <div><span class="text-gray-500">Email:</span> <span class="font-medium text-gray-900 dark:text-white">{{ selectedUser.email }}</span></div>
                                <div><span class="text-gray-500">Phone:</span> <span class="font-medium text-gray-900 dark:text-white">{{ selectedUser.phone || 'Not configured' }}</span></div>
                            </div>
                        </div>

                        <div>
                            <h4 class="mb-3 text-sm font-semibold uppercase tracking-wider text-gray-500">Address Data</h4>
                            <div class="space-y-1 text-sm text-gray-900 dark:text-white">
                                <p>{{ selectedUser.address || 'Address not listed' }}</p>
                                <p v-if="selectedUser.city || selectedUser.province">
                                    {{ selectedUser.city || '' }} {{ selectedUser.province || '' }} {{ selectedUser.postal_code || '' }}
                                </p>
                            </div>
                        </div>

                        <div>
                            <h4 class="mb-3 text-sm font-semibold uppercase tracking-wider text-gray-500">System Resources</h4>
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div class="rounded-lg bg-indigo-50 p-3 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-900/50">
                                    <div class="text-indigo-600 dark:text-indigo-400 font-bold text-lg">{{ selectedUser.limit_project }}</div>
                                    <div class="text-xs text-indigo-800 dark:text-indigo-300 uppercase tracking-wide">Project Limit</div>
                                </div>
                                <div class="rounded-lg bg-emerald-50 p-3 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-900/50">
                                    <div class="text-emerald-600 dark:text-emerald-400 font-bold text-lg">{{ selectedUser.limit_bank }}</div>
                                    <div class="text-xs text-emerald-800 dark:text-emerald-300 uppercase tracking-wide">Bank Limit</div>
                                </div>
                                
                                <div class="col-span-2 rounded-lg bg-white border border-gray-200 dark:bg-gray-800 dark:border-gray-700 p-3">
                                    <div class="text-xs tracking-wide text-gray-500 dark:text-gray-400 mb-1">Financial Fees</div>
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-gray-900 dark:text-white">Tx Fee ({{ selectedUser.fee_tx_type }})</span>
                                        <span class="font-semibold">{{ selectedUser.fee_tx }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm mt-1">
                                        <span class="text-gray-900 dark:text-white">Withdrawal ({{ selectedUser.withdraw_fee_type }})</span>
                                        <span class="font-semibold">{{ selectedUser.withdraw_fee }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Manage Actions Form Col -->
                    <div class="p-6 md:w-1/2 space-y-6">
                        <form @submit.prevent="submitUpdate" class="flex h-full flex-col">
                            
                            <h4 class="mb-4 text-sm font-semibold uppercase tracking-wider text-gray-900 flex items-center gap-2 dark:text-white">
                                <Settings class="h-4 w-4" /> Administrative Controls
                            </h4>

                            <!-- Status Editor -->
                            <div class="mb-5">
                                <label for="status" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Account Access Status</label>
                                <select 
                                    id="status" 
                                    v-model="form.status" 
                                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-indigo-500 dark:focus:ring-indigo-500"
                                >
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="banned">Banned</option>
                                </select>
                                <div v-if="form.errors.status" class="mt-1 text-xs text-red-500">{{ form.errors.status }}</div>
                            </div>

                            <!-- Password Override -->
                            <div class="mb-auto">
                                <label for="change_password" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300 flex items-center gap-1.5 border-b border-gray-200 dark:border-gray-700 pb-2">
                                    <KeyRound class="h-3.5 w-3.5" /> Force Password Reset
                                </label>
                                <p class="text-xs text-gray-500 mb-3 dark:text-gray-400">Leave perfectly blank to keep the current password. If inputted, it immediately overwrites user access.</p>
                                <input 
                                    type="text" 
                                    id="change_password" 
                                    v-model="form.change_password" 
                                    class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white dark:placeholder-gray-500 dark:focus:border-indigo-500 dark:focus:ring-indigo-500" 
                                    placeholder="Enter new strong password"
                                    minlength="8"
                                />
                                <div v-if="form.errors.change_password" class="mt-1 text-xs text-red-500">{{ form.errors.change_password }}</div>
                            </div>

                            <!-- Form Actions -->
                            <div class="mt-8 pt-4 border-t border-gray-200 dark:border-gray-800 flex justify-end gap-3">
                                <button 
                                    @click="closeDetails" 
                                    type="button" 
                                    class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition"
                                >
                                    Cancel
                                </button>
                                <button 
                                    type="submit" 
                                    :disabled="form.processing"
                                    class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-5 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-300 disabled:opacity-50 dark:focus:ring-indigo-800 transition"
                                >
                                    <CheckCircle class="h-4 w-4" /> Save Changes
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
