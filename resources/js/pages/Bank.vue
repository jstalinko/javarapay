<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Plus, CreditCard, CheckCircle2, User } from 'lucide-vue-next';
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
import { Checkbox } from '@/components/ui/checkbox';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

const props = defineProps<{
    banks: any[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Banks', href: '/dashboard/bank' },
];

const isModalOpen = ref(false);

const form = useForm({
    bank_name: '',
    account_name: '',
    account_number: '',
    is_primary: true,
});

const openModal = () => {
    form.reset();
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    form.reset();
};

const submit = () => {
    form.post('/dashboard/bank', {
        onSuccess: () => {
            toast.success('Bank added successfully');
            closeModal();
        },
        onError: () => {
            toast.error('Failed to add bank');
        }
    });
};

const formatCensored = (number: string) => {
    if (!number) return '';
    return '**** **** ' + number.slice(-4);
};

</script>

<template>
    <Head title="Banks" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Bank Accounts</h1>
                    <p class="text-muted-foreground text-sm">Manage your bank accounts for withdrawals.</p>
                </div>
                <Button @click="openModal">
                    <Plus class="mr-2 h-4 w-4" />
                    Add Bank
                </Button>
            </div>

            <div v-if="banks && banks.length > 0" class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <Card v-for="bank in banks" :key="bank.id" :class="{'border-primary/50 bg-primary/5 dark:bg-primary/10 shadow-sm': bank.is_primary}">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text font-semibold">
                            {{ bank.bank_name }}
                        </CardTitle>
                        <CreditCard class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="mt-4 space-y-4">
                            <div class="flex flex-col gap-1">
                                <span class="text-[10px] uppercase text-muted-foreground font-bold tracking-wider">Account holder</span>
                                <div class="flex items-center gap-2">
                                    <User class="h-3.5 w-3.5 text-muted-foreground" />
                                    <span class="text-sm font-medium">{{ bank.account_name }}</span>
                                </div>
                            </div>
                            <div class="flex flex-col gap-1">
                                <span class="text-[10px] uppercase text-muted-foreground font-bold tracking-wider">Account number</span>
                                <div class="text-lg font-mono tracking-widest text-foreground/90">
                                    {{ formatCensored(bank.account_number) }}
                                </div>
                            </div>
                            <div v-if="bank.is_primary" class="flex items-center gap-1.5 text-xs text-primary font-bold">
                                <CheckCircle2 class="h-4 w-4" />
                                Primary Account
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div v-else class="flex min-h-[400px] flex-col items-center justify-center rounded-xl border border-dashed p-12 text-center">
                <div class="rounded-full bg-muted p-4">
                    <CreditCard class="h-10 w-10 text-muted-foreground/60" />
                </div>
                <h3 class="mt-4 text-lg font-semibold">No bank accounts</h3>
                <p class="mt-2 text-sm text-muted-foreground max-w-sm">
                    You haven't added any bank accounts yet. Add one to start receiving payments.
                </p>
                <Button @click="openModal" class="mt-6">
                    <Plus class="mr-2 h-4 w-4" />
                    Add Your First Bank
                </Button>
            </div>
        </div>

        <Dialog v-model:open="isModalOpen">
            <DialogContent class="sm:max-w-[425px]">
                <form @submit.prevent="submit">
                    <DialogHeader>
                        <DialogTitle>Add Bank Account</DialogTitle>
                    </DialogHeader>
                    
                    <div class="grid gap-4 py-4">
                        <div class="grid gap-2">
                            <Label for="bank_name">Bank Name</Label>
                            <Input id="bank_name" v-model="form.bank_name" placeholder="e.g. Bank Central Asia" required />
                            <span v-if="form.errors.bank_name" class="text-xs text-red-500">{{ form.errors.bank_name }}</span>
                        </div>

                        <div class="grid gap-2">
                            <Label for="account_name">Account Holder Name</Label>
                            <Input id="account_name" v-model="form.account_name" placeholder="As written in your bank account" required />
                            <span v-if="form.errors.account_name" class="text-xs text-red-500">{{ form.errors.account_name }}</span>
                        </div>

                        <div class="grid gap-2">
                            <Label for="account_number">Account Number</Label>
                            <Input id="account_number" v-model="form.account_number" placeholder="Enter account number" required />
                            <span v-if="form.errors.account_number" class="text-xs text-red-500">{{ form.errors.account_number }}</span>
                        </div>

                        <div class="flex items-center space-x-2 pt-2">
                            <Checkbox id="is_primary" :checked="form.is_primary" @update:checked="val => form.is_primary = val" />
                            <label for="is_primary" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                                Set as primary account
                            </label>
                        </div>
                    </div>

                    <DialogFooter>
                        <Button variant="outline" @click="closeModal" type="button">Cancel</Button>
                        <Button type="submit" :disabled="form.processing">
                            <span v-if="form.processing">Saving...</span>
                            <span v-else>Save Bank</span>
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
