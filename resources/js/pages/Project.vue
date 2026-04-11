<script setup lang="ts">
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { MoreHorizontal, Eye, Edit, ShieldCheck, Copy, EyeOff, Check } from 'lucide-vue-next';

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

const props = defineProps<{
    projects: any[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Project', href: '/dashboard/project' },
];

const selectedProject = ref<any>(null);
const isModalOpen = ref(false);
const modalMode = ref<'detail' | 'edit'>('detail');
const isApikeyVisible = ref(false);
const copiedField = ref<string | null>(null);

const openModal = (project: any, mode: 'detail' | 'edit') => {
    selectedProject.value = { ...project };
    modalMode.value = mode;
    isModalOpen.value = true;
    isApikeyVisible.value = false;
    copiedField.value = null;
};

const closeModal = () => {
    isModalOpen.value = false;
    setTimeout(() => {
        selectedProject.value = null;
    }, 300);
};

const copyToClipboard = (text: string, field: string) => {
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
    // Only handles edit update, this could be adjusted according to backend route.
    // For now we just close or call an inertia route if it existed.
    // router.put(route('dashboard.project.update', selectedProject.value.id), selectedProject.value, {
    //    onSuccess: () => closeModal()
    // })
    closeModal();
};

const toggleToProduction = (project: any) => {
    // router.put(route('dashboard.project.toggle_production', project.id))
};
</script>

<template>
    <Head title="Projects" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="rounded-xl border bg-card text-card-foreground shadow-sm flex-1">
                <div class="flex flex-col space-y-1.5 p-6">
                    <h3 class="font-semibold leading-none tracking-tight">Projects</h3>
                    <p class="text-sm text-muted-foreground">Manage your projects and their configurations.</p>
                </div>
                <div class="p-6 pt-0">
                    <div class="relative w-full overflow-auto">
                        <table class="w-full caption-bottom text-sm">
                            <thead class="[&_tr]:border-b">
                                <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground w-[250px]">Name</th>
                                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Environment</th>
                                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Fee Type</th>
                                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Status</th>
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
                                    <td class="p-4 align-middle uppercase text-xs font-semibold tracking-wider">{{ project.fee_type }}</td>
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
                                            </DropdownMenuContent>
                                        </DropdownMenu>
                                    </td>
                                </tr>
                                <tr v-if="!projects || projects.length === 0">
                                    <td colspan="5" class="p-8 text-center text-muted-foreground">
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
                <DialogHeader>
                    <DialogTitle>{{ modalMode === 'detail' ? 'Project Details' : 'Edit Project' }}</DialogTitle>
                </DialogHeader>
                
                <div class="grid gap-6 py-4" v-if="selectedProject">
                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="name" class="text-right font-medium">Name</Label>
                        <Input id="name" v-model="selectedProject.name" class="col-span-3" :disabled="modalMode === 'detail'" />
                    </div>

                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="fee_type" class="text-right font-medium">Fee Type</Label>
                        <Input id="fee_type" v-model="selectedProject.fee_type" class="col-span-3" :disabled="modalMode === 'detail'" />
                    </div>

                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="webhook_url" class="text-right font-medium">Webhook URL</Label>
                        <Input id="webhook_url" v-model="selectedProject.webhook_url" class="col-span-3" :disabled="modalMode === 'detail'" />
                    </div>

                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="is_production" class="text-right font-medium">Environment</Label>
                        <div class="col-span-3 flex items-center space-x-2">
                            <Checkbox id="is_production" :checked="selectedProject.is_production ? true : false" @update:checked="val => selectedProject.is_production = val" :disabled="modalMode === 'detail'" />
                            <label for="is_production" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                                Set as Production
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="active" class="text-right font-medium">Status</Label>
                        <div class="col-span-3 flex items-center space-x-2">
                            <Checkbox id="active" :checked="selectedProject.active ? true : false" @update:checked="val => selectedProject.active = val" :disabled="modalMode === 'detail'" />
                            <label for="active" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                                Active Project
                            </label>
                        </div>
                    </div>

                    <!-- Only show in detail mode -->
                    <template v-if="modalMode === 'detail'">
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
                        {{ modalMode === 'edit' ? 'Cancel' : 'Close' }}
                    </Button>
                    <Button v-if="modalMode === 'edit'" @click="submit" type="submit">
                        Save changes
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
