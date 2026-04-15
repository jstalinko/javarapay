<script setup>
import { ref } from 'vue';
import { Head, useForm, router, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { MoreHorizontal, Eye, Edit, ShieldCheck, Copy, EyeOff, Check, Plus, Banknote } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

// UI components
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';

const props = defineProps({
    projects: Array,
});

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Project', href: '/dashboard/project' },
];

const selectedProject = ref(null);
const isModalOpen = ref(false);
const modalMode = ref('detail');
const isApikeyVisible = ref(false);
const copiedField = ref(null);

const form = useForm({
    name: '',
    fee_type: 'customer',
    webhook_url: '',
    is_production: false,
    active: true,
});

const openModal = (project, mode) => {
    modalMode.value = mode;
    if (mode === 'create') {
        form.reset();
        selectedProject.value = null;
    } else {
        selectedProject.value = { ...project };
        form.name = project.name;
        form.fee_type = project.fee_type;
        form.webhook_url = project.webhook_url || '';
        form.is_production = !!project.is_production;
        form.active = !!project.active;
    }
    isModalOpen.value = true;
    isApikeyVisible.value = false;
    copiedField.value = null;
};

const closeModal = () => {
    isModalOpen.value = false;
    if (modalMode.value === 'create') {
        form.reset();
    }
    setTimeout(() => {
        selectedProject.value = null;
    }, 300);
};

const copyToClipboard = (text, field) => {
    if (!text) return;
    navigator.clipboard.writeText(text);
    copiedField.value = field;
    setTimeout(() => {
        if (copiedField.value === field) {
            copiedField.value = null;
        }
    }, 2000);
};

const submit = () => {
    if (modalMode.value === 'create') {
        form.post('/dashboard/project', {
            onSuccess: () => {
                toast.success('Project created successfully');
                closeModal();
            },
            onError: () => {
                toast.error('Failed to create project');
            }
        });
    } else if (modalMode.value === 'edit') {
        form.post(`/dashboard/project/${selectedProject.value.id}`, {
            onSuccess: () => {
                toast.success('Project updated successfully');
                closeModal();
            },
            onError: () => {
                toast.error('Failed to update project');
            }
        });
    }
};

const toggleToProduction = (project) => {
    router.post(`/dashboard/project/${project.id}/toggle-production`, {}, {
        onSuccess: () => {
            toast.success('Project environment updated');
        },
        onError: () => {
            toast.error('Failed to update environment');
        }
    });
};
</script>

<template>
    <Head title="Projects" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="rounded-xl border bg-card text-card-foreground shadow-sm flex-1">
                <div class="flex flex-row items-center justify-between space-y-0 p-6">
                    <div class="flex flex-col space-y-1.5">
                        <h3 class="font-semibold leading-none tracking-tight">Projects</h3>
                        <p class="text-sm text-muted-foreground">Manage your projects and their configurations.</p>
                    </div>
                    <Button @click="openModal(null, 'create')">
                        <Plus class="mr-2 h-4 w-4" />
                        Create New Project
                    </Button>
                </div>
                <div class="p-6 pt-0">
                    <div class="relative w-full overflow-auto">
                        <table class="w-full caption-bottom text-sm">
                            <thead class="[&_tr]:border-b">
                                <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground w-[250px]">Name</th>
                                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Environment</th>
                                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Approval Status</th>
                                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Fee Type</th>
                                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">State</th>
                                    <th class="h-12 px-4 align-middle font-medium text-muted-foreground w-[100px]">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="[&_tr:last-child]:border-0">
                                <tr v-for="project in projects" :key="project.id" class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                                    <td class="p-4 align-middle font-medium">{{ project.name }}</td>
                                    <td class="p-4 align-middle">
                                        <span v-if="project.is_production" class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-primary/10 text-primary border-primary/20 dark:bg-primary/20">Production</span>
                                        <span v-else class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-muted text-muted-foreground">Sandbox</span>
                                    </td>
                                    <td class="p-4 align-middle">
                                        <span v-if="project.status === 'in_review'" class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-yellow-100 text-yellow-800 border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-400 dark:border-yellow-800">In Review</span>
                                        <span v-else-if="project.status === 'approve'" class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800">Approved</span>
                                        <span v-else-if="project.status === 'decline'" class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-red-100 text-red-800 border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800">Declined</span>
                                        <span v-else-if="project.status === 'banned'" class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-gray-100 text-gray-800 border-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700">Banned</span>
                                        <span v-else class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-muted text-muted-foreground capitalize">{{ project.status }}</span>
                                    </td>
                                    <td class="p-4 align-middle capitalize text-xs font-semibold tracking-wider">{{ project.fee_type }}</td>
                                    <td class="p-4 align-middle">
                                        <span v-if="project.active" class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-green-100 text-green-800 border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800">Active</span>
                                        <span v-else class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-red-100 text-red-800 border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800">Inactive</span>
                                    </td>
                                    <td class="p-4 align-middle">
                                        <DropdownMenu>
                                            <DropdownMenuTrigger as-child>
                                                <Button variant="ghost" class="h-8 w-8 p-0">
                                                    <span class="sr-only">Open menu</span>
                                                    <MoreHorizontal class="h-4 w-4" />
                                                </Button>
                                            </DropdownMenuTrigger>
                                            <DropdownMenuContent align="end">
                                                <DropdownMenuItem @click="openModal(project, 'detail')">
                                                    <Eye class="mr-2 h-4 w-4" />
                                                    <span>Detail</span>
                                                </DropdownMenuItem>
                                                <DropdownMenuItem @click="openModal(project, 'edit')">
                                                    <Edit class="mr-2 h-4 w-4" />
                                                    <span>Edit</span>
                                                </DropdownMenuItem>
                                                <DropdownMenuItem @click="toggleToProduction(project)" v-if="!project.is_production">
                                                    <ShieldCheck class="mr-2 h-4 w-4" />
                                                    <span>Toggle to Production</span>
                                                </DropdownMenuItem>
                                                <DropdownMenuItem @click="router.visit(`/dashboard/project/${project.id}/channels`)">
                                                    <Banknote class="mr-2 h-4 w-4"/>
                                                    <span>Payment Channels</span>
                                                </DropdownMenuItem>
                                            </DropdownMenuContent>
                                        </DropdownMenu>
                                    </td>
                                </tr>
                                <tr v-if="!projects || projects.length === 0">
                                    <td colspan="6" class="p-8 text-center text-muted-foreground">
                                        No projects found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <Dialog v-model:open="isModalOpen">
            <DialogContent class="sm:max-w-[550px]">
                <form @submit.prevent="submit">
                    <DialogHeader>
                        <DialogTitle>
                            <span v-if="modalMode === 'detail'">Project Details</span>
                            <span v-else-if="modalMode === 'edit'">Edit Project</span>
                            <span v-else>Create New Project</span>
                        </DialogTitle>
                    </DialogHeader>
                    
                    <div class="grid gap-6 py-4">
                        <div class="grid grid-cols-4 items-center gap-4">
                            <Label for="name" class="text-right font-medium">Name</Label>
                            <Input id="name" v-model="form.name" class="col-span-3" :disabled="modalMode === 'detail'" required />
                            <span v-if="form.errors.name" class="col-span-3 col-start-2 text-sm text-red-500">{{ form.errors.name }}</span>
                        </div>

                        <div class="grid grid-cols-4 items-center gap-4">
                            <Label for="fee_type" class="text-right font-medium">Fee Type</Label>
                            <select id="fee_type" v-model="form.fee_type" class="col-span-3 flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" :disabled="modalMode === 'detail'" required>
                                <option value="customer">Customer</option>
                                <option value="merchant">Merchant</option>
                            </select>
                            <span v-if="form.errors.fee_type" class="col-span-3 col-start-2 text-sm text-red-500">{{ form.errors.fee_type }}</span>
                        </div>

                        <div class="grid grid-cols-4 items-center gap-4">
                            <Label for="webhook_url" class="text-right font-medium">Webhook URL</Label>
                            <Input id="webhook_url" type="url" v-model="form.webhook_url" class="col-span-3" :disabled="modalMode === 'detail'" />
                            <span v-if="form.errors.webhook_url" class="col-span-3 col-start-2 text-sm text-red-500">{{ form.errors.webhook_url }}</span>
                        </div>

                        <template v-if="modalMode !== 'create'">
                            <div class="grid grid-cols-4 items-center gap-4">
                                <Label for="is_production" class="text-right font-medium">Environment</Label>
                                <div class="col-span-3 flex items-center space-x-2">
                                    <Checkbox id="is_production" :checked="form.is_production" @update:checked="val => form.is_production = val" :disabled="modalMode === 'detail'" />
                                    <label for="is_production" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                                        Set as Production
                                    </label>
                                </div>
                            </div>

                            <div class="grid grid-cols-4 items-center gap-4">
                                <Label for="active" class="text-right font-medium">State</Label>
                                <div class="col-span-3 flex items-center space-x-2">
                                    <Checkbox id="active" :checked="form.active" @update:checked="val => form.active = val" :disabled="modalMode === 'detail'" />
                                    <label for="active" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                                        Active Project
                                    </label>
                                </div>
                            </div>
                        </template>

                        <!-- Only show in detail mode -->
                        <template v-if="modalMode === 'detail' && selectedProject">
                            <div class="grid grid-cols-4 items-center gap-4">
                                <Label for="apikey" class="text-right font-medium">API Key</Label>
                                <div class="col-span-3 flex items-center space-x-2">
                                    <Input id="apikey" :type="isApikeyVisible ? 'text' : 'password'" :value="selectedProject.apikey || '—'" class="flex-1 font-mono text-xs" readonly />
                                    <Button variant="outline" size="icon" @click="isApikeyVisible = !isApikeyVisible" type="button" :disabled="!selectedProject.apikey">
                                        <EyeOff v-if="isApikeyVisible" class="h-4 w-4" />
                                        <Eye v-else class="h-4 w-4" />
                                    </Button>
                                    <Button variant="outline" size="icon" @click="copyToClipboard(selectedProject.apikey, 'apikey')" type="button" :disabled="!selectedProject.apikey">
                                        <Check v-if="copiedField === 'apikey'" class="h-4 w-4 text-green-500" />
                                        <Copy v-else class="h-4 w-4" />
                                    </Button>
                                </div>
                            </div>

                            <div class="grid grid-cols-4 items-center gap-4">
                                <Label for="merchant_code" class="text-right font-medium">Merchant Code</Label>
                                <div class="col-span-3 flex items-center space-x-2">
                                    <Input id="merchant_code" :value="selectedProject.merchant_code || '—'" class="flex-1 font-mono text-xs" readonly />
                                    <Button variant="outline" size="icon" @click="copyToClipboard(selectedProject.merchant_code, 'merchant_code')" type="button" :disabled="!selectedProject.merchant_code">
                                        <Check v-if="copiedField === 'merchant_code'" class="h-4 w-4 text-green-500" />
                                        <Copy v-else class="h-4 w-4" />
                                    </Button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <DialogFooter>
                        <Button variant="outline" @click="closeModal" type="button">
                            {{ modalMode === 'detail' ? 'Close' : 'Cancel' }}
                        </Button>
                        <Button v-if="modalMode === 'create' || modalMode === 'edit'" type="submit" :disabled="modalMode === 'create' && form.processing">
                            <span v-if="modalMode === 'create' && form.processing">Saving...</span>
                            <span v-else>Save changes</span>
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
