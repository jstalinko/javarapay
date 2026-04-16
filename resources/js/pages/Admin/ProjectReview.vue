<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import { CheckCircle, XCircle, FileText, Globe, BoxSelect, AlertCircle } from 'lucide-vue-next';

interface Project {
    id: number;
    name: string;
    apikey: string;
    merchant_code: string;
    is_production: boolean;
    fee_type: string;
    webhook_url: string;
    active: boolean;
    status: string;
    created_at: string;
    user: {
        name: string;
        email: string;
        phone: string;
    };
}

const props = defineProps<{
    projects: {
        data: Project[];
        links: any[];
    };
}>();

const page = usePage();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Project Review',
        href: '/dashboard/admin/project-review',
    },
];

const selectedProject = ref<Project | null>(null);

const form = useForm({
    status: '',
});

const openDetails = (project: Project) => {
    selectedProject.value = project;
};

const closeDetails = () => {
    selectedProject.value = null;
};

const updateStatus = (status: 'approve' | 'decline') => {
    if (!selectedProject.value) return;
    
    form.status = status;
    form.post(route('dashboard.admin.project.update-status', selectedProject.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeDetails();
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
</script>

<template>
    <Head title="Project Review" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 px-10">
            <!-- Header -->
            <div class="flex flex-col gap-2">
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100">Project Review Inbox</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Review and approve newly submitted projects or modifications.</p>
            </div>
            
            <div v-if="page.props.flash?.success" class="rounded-lg bg-green-50 p-4 text-sm text-green-800 dark:bg-green-900/30 dark:text-green-400">
                {{ page.props.flash.success }}
            </div>

            <!-- List Vector -->
            <div class="flex flex-col rounded-2xl border border-sidebar-border/70 bg-white shadow-sm dark:border-sidebar-border dark:bg-sidebar">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-500 dark:text-gray-400">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-700 dark:bg-gray-800/50 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-4">Project Name</th>
                                <th scope="col" class="px-6 py-4">Merchant</th>
                                <th scope="col" class="px-6 py-4">Submitted Date</th>
                                <th scope="col" class="px-6 py-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="project in projects.data" :key="project.id" class="border-b bg-white hover:bg-gray-50 dark:border-gray-800 dark:bg-sidebar dark:hover:bg-gray-800/50">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ project.name }}</div>
                                    <div class="text-xs text-indigo-600 dark:text-indigo-400">{{ project.is_production ? 'Production' : 'Sandbox' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ project.user.name }}</div>
                                    <div class="text-xs">{{ project.user.email }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    {{ formatDate(project.created_at) }}
                                </td>
                                <td class="px-6 py-4">
                                    <button 
                                        @click="openDetails(project)" 
                                        class="inline-flex items-center gap-2 rounded-lg bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-600 hover:bg-indigo-100 dark:bg-indigo-900/30 dark:text-indigo-400 dark:hover:bg-indigo-900/50 transition"
                                    >
                                        <BoxSelect class="h-4 w-4" />
                                        Review Details
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="projects.data.length === 0">
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center space-y-3">
                                        <div class="rounded-full bg-gray-100 p-3 dark:bg-gray-800">
                                            <CheckCircle class="h-6 w-6 text-gray-400" />
                                        </div>
                                        <p class="text-base font-medium text-gray-900 dark:text-gray-100">All caught up!</p>
                                        <p class="text-sm">There are no pending projects in the review queue.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Details Modal -->
        <div v-if="selectedProject" class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-black/50 p-4 md:p-0">
            <div class="relative w-full max-w-2xl rounded-2xl bg-white shadow-xl dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-2xl transition-all">
                
                <div class="flex items-center justify-between border-b border-gray-200 p-5 dark:border-gray-800">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Project Details</h3>
                    <button @click="closeDetails" type="button" class="ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-800 dark:hover:text-white">
                        <svg class="h-3 w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>

                <div class="space-y-6 p-6">
                    <!-- Basic Info -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-800/50">
                            <span class="text-xs uppercase tracking-wider text-gray-500">Project Name</span>
                            <div class="mt-1 font-semibold text-gray-900 dark:text-gray-100">{{ selectedProject.name }}</div>
                        </div>
                        <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-800/50">
                            <span class="text-xs uppercase tracking-wider text-gray-500">Environment</span>
                            <div class="mt-1 font-semibold">
                                <span v-if="selectedProject.is_production" class="text-indigo-600 dark:text-indigo-400">Production</span>
                                <span v-else class="text-amber-600 dark:text-amber-400">Sandbox</span>
                            </div>
                        </div>
                    </div>

                    <!-- Owner Details -->
                    <div>
                        <h4 class="mb-3 text-sm font-semibold uppercase tracking-wider text-gray-500 flex items-center gap-2">
                            <User class="h-4 w-4" /> Owner Information
                        </h4>
                        <div class="rounded-xl border border-gray-200 dark:border-gray-800 overflow-hidden">
                            <table class="w-full text-sm">
                                <tbody>
                                    <tr class="border-b border-gray-200 dark:border-gray-800">
                                        <td class="bg-gray-50 px-4 py-3 font-medium text-gray-600 dark:bg-gray-800/50 dark:text-gray-400 w-1/3">Name</td>
                                        <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ selectedProject.user.name }}</td>
                                    </tr>
                                    <tr class="border-b border-gray-200 dark:border-gray-800">
                                        <td class="bg-gray-50 px-4 py-3 font-medium text-gray-600 dark:bg-gray-800/50 dark:text-gray-400">Email</td>
                                        <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ selectedProject.user.email }}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-gray-50 px-4 py-3 font-medium text-gray-600 dark:bg-gray-800/50 dark:text-gray-400">Contact</td>
                                        <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ selectedProject.user.phone || 'Not provided' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Technical Details -->
                    <div>
                        <h4 class="mb-3 text-sm font-semibold uppercase tracking-wider text-gray-500 flex items-center gap-2">
                            <Globe class="h-4 w-4" /> Configuration Details
                        </h4>
                        <div class="rounded-xl border border-gray-200 dark:border-gray-800 overflow-hidden">
                            <table class="w-full text-sm">
                                <tbody>
                                    <tr class="border-b border-gray-200 dark:border-gray-800">
                                        <td class="bg-gray-50 px-4 py-3 font-medium text-gray-600 dark:bg-gray-800/50 dark:text-gray-400 w-1/3">Fee Protocol</td>
                                        <td class="px-4 py-3 text-gray-900 dark:text-gray-100 uppercase tracking-widest text-xs font-bold">{{ selectedProject.fee_type }}</td>
                                    </tr>
                                    <tr class="border-b border-gray-200 dark:border-gray-800">
                                        <td class="bg-gray-50 px-4 py-3 font-medium text-gray-600 dark:bg-gray-800/50 dark:text-gray-400">Callback URL</td>
                                        <td class="px-4 py-3 text-emerald-600 dark:text-emerald-400 break-all">{{ selectedProject.webhook_url || 'Not configured' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-gray-50 px-4 py-3 font-medium text-gray-600 dark:bg-gray-800/50 dark:text-gray-400">Merchant Code</td>
                                        <td class="px-4 py-3 text-gray-500 font-mono text-xs">{{ selectedProject.merchant_code }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                </div>
                
                <div class="flex items-center justify-end gap-3 border-t border-gray-200 bg-gray-50 p-5 dark:border-gray-800 dark:bg-gray-900/50 rounded-b-2xl">
                    <button 
                        @click="closeDetails" 
                        type="button" 
                        class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:hover:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700"
                    >
                        Cancel
                    </button>
                    <button 
                        @click="updateStatus('decline')" 
                        :disabled="form.processing"
                        type="button" 
                        class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-300 disabled:opacity-50 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900 transition"
                    >
                        <XCircle class="h-4 w-4" /> Decline
                    </button>
                    <button 
                        @click="updateStatus('approve')" 
                        :disabled="form.processing"
                        type="button" 
                        class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-green-300 disabled:opacity-50 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800 transition shadow-lg shadow-green-500/30"
                    >
                        <CheckCircle class="h-4 w-4" /> Approve Project
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
