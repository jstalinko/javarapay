<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, usePage, router, Link } from '@inertiajs/vue3';
import { ref, watch, onBeforeUnmount } from 'vue';
import { Megaphone, Search, Plus, Edit, Trash2, CheckCircle, XCircle, Bold, Italic, List, ListOrdered } from 'lucide-vue-next';
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import { toast } from 'vue-sonner';

interface Announcement {
    id: number;
    title: string;
    category: string;
    content: string;
    is_published: boolean;
    created_at: string;
}

const props = defineProps<{
    announcements: {
        data: Announcement[];
        links: any[];
    };
    filters: {
        search?: string;
    };
}>();

const page = usePage();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Announcements Management',
        href: '/dashboard/admin/announcements',
    },
];

const search = ref(props.filters.search || '');

let debounceTimer: ReturnType<typeof setTimeout>;
watch(search, (value) => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get(route('dashboard.admin.announcements'), { search: value }, { preserveState: true, replace: true, preserveScroll: true });
    }, 300);
});

const isModalOpen = ref(false);
const editingId = ref<number | null>(null);

const form = useForm({
    title: '',
    category: 'common',
    content: '',
    is_published: false,
});

const editor = useEditor({
    content: form.content,
    extensions: [StarterKit],
    onUpdate: ({ editor }) => {
        form.content = editor.getHTML();
    },
});

watch(() => form.content, (value) => {
    if (editor.value && editor.value.getHTML() !== value) {
        editor.value.commands.setContent(value, false);
    }
});

onBeforeUnmount(() => {
    if (editor.value) editor.value.destroy();
});

const openCreateModal = () => {
    editingId.value = null;
    form.reset();
    if (editor.value) editor.value.commands.setContent('');
    isModalOpen.value = true;
};

const openEditModal = (announcement: Announcement) => {
    editingId.value = announcement.id;
    form.title = announcement.title;
    form.category = announcement.category;
    form.content = announcement.content;
    form.is_published = announcement.is_published;
    if (editor.value) editor.value.commands.setContent(announcement.content);
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    form.reset();
    editingId.value = null;
};

const submitForm = () => {
    if (editingId.value) {
        form.post(route('dashboard.admin.announcement.update', editingId.value), {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Announcement updated successfully!');
                closeModal();
            },
            onError: () => toast.error('Failed to update announcement.'),
        });
    } else {
        form.post(route('dashboard.admin.announcement.store'), {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Announcement created successfully!');
                closeModal();
            },
            onError: () => toast.error('Failed to create announcement.'),
        });
    }
};

const deleteAnnouncement = (id: number) => {
    if (confirm('Are you sure you want to delete this announcement?')) {
        router.delete(route('dashboard.admin.announcement.delete', id), {
            preserveScroll: true,
            onSuccess: () => toast.success('Announcement deleted successfully!'),
            onError: () => toast.error('Failed to delete announcement.'),
        });
    }
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
    <Head title="Announcements Management" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 px-10">
            <!-- Header -->
            <div class="flex flex-col gap-2">
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100 border-b pb-2">Announcements Management</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Create global system announcements, set categories, and manage publication status.</p>
            </div>

            <!-- List -->
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
                            placeholder="Search by title or category..." 
                        />
                    </div>
                    <button 
                        @click="openCreateModal"
                        class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-300 dark:bg-indigo-600 dark:hover:bg-indigo-700 dark:focus:ring-indigo-800 transition"
                    >
                        <Plus class="h-4 w-4" /> Create Announcement
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-500 dark:text-gray-400">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-700 dark:bg-gray-800/50 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-4">Title</th>
                                <th scope="col" class="px-6 py-4">Category</th>
                                <th scope="col" class="px-6 py-4">Status</th>
                                <th scope="col" class="px-6 py-4">Date</th>
                                <th scope="col" class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="ann in announcements.data" :key="ann.id" class="border-b bg-white hover:bg-gray-50 dark:border-gray-800 dark:bg-sidebar dark:hover:bg-gray-800/50">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900 dark:text-white flex items-center gap-2 max-w-sm truncate" :title="ann.title">
                                        <Megaphone class="h-4 w-4 text-indigo-500" />
                                        {{ ann.title }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 uppercase text-xs font-semibold tracking-wide">
                                    {{ ann.category }}
                                </td>
                                <td class="px-6 py-4">
                                    <span v-if="ann.is_published" class="rounded bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900/30 dark:text-green-400">Published</span>
                                    <span v-else class="rounded bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800 dark:bg-gray-800 dark:text-gray-300">Draft</span>
                                </td>
                                <td class="px-6 py-4">
                                    {{ formatDate(ann.created_at) }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button 
                                            @click="openEditModal(ann)" 
                                            class="inline-flex items-center justify-center h-8 w-8 rounded bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition"
                                            title="Edit"
                                        >
                                            <Edit class="h-4 w-4" />
                                        </button>
                                        <button 
                                            @click="deleteAnnouncement(ann.id)" 
                                            class="inline-flex items-center justify-center h-8 w-8 rounded bg-red-50 text-red-600 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/40 transition"
                                            title="Delete"
                                        >
                                            <Trash2 class="h-4 w-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="announcements.data.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    No announcements found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="announcements.links && announcements.links.length > 3" class="flex items-center justify-center border-t border-gray-200 p-4 dark:border-gray-800 sm:justify-between">
                    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700 dark:text-gray-400">
                                Showing
                                <span class="font-medium text-gray-900 dark:text-white">{{ announcements.data.length }}</span>
                                results
                            </p>
                        </div>
                        <div>
                            <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                                <template v-for="(link, i) in announcements.links" :key="i">
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

        <!-- Form Modal -->
        <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-black/50 p-4 md:p-0">
            <div class="relative w-full max-w-2xl rounded-2xl bg-white shadow-xl dark:bg-gray-900 border border-gray-200 dark:border-gray-800 transition-all">
                
                <div class="flex items-center justify-between border-b border-gray-200 p-5 dark:border-gray-800">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ editingId ? 'Edit Announcement' : 'Create Request' }}</h3>
                    <button @click="closeModal" type="button" class="ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-800 dark:hover:text-white">
                        <XCircle class="h-5 w-5" />
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>

                <div class="p-6">
                    <form @submit.prevent="submitForm" class="space-y-4">
                        
                        <div>
                            <label for="title" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
                            <input 
                                type="text" 
                                id="title" 
                                v-model="form.title" 
                                class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-indigo-500 dark:focus:ring-indigo-500" 
                                required
                            />
                            <div v-if="form.errors.title" class="mt-1 text-xs text-red-500">{{ form.errors.title }}</div>
                        </div>

                        <div>
                            <label for="category" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Category Tag</label>
                            <input 
                                type="text" 
                                id="category" 
                                v-model="form.category" 
                                class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-indigo-500 dark:focus:ring-indigo-500" 
                                placeholder="common, urgent, promotion"
                            />
                            <div v-if="form.errors.category" class="mt-1 text-xs text-red-500">{{ form.errors.category }}</div>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Content</label>
                            <div class="rounded-lg border border-gray-300 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800 focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-indigo-500">
                                <div v-if="editor" class="flex flex-wrap items-center gap-1 border-b border-gray-200 bg-gray-50 px-3 py-2 dark:border-gray-700 dark:bg-gray-900/50 rounded-t-lg">
                                    <button type="button" @click="editor.chain().focus().toggleBold().run()" :class="{ 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-400': editor.isActive('bold') }" class="rounded p-1.5 text-gray-600 hover:bg-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 transition">
                                        <Bold class="h-4 w-4" />
                                    </button>
                                    <button type="button" @click="editor.chain().focus().toggleItalic().run()" :class="{ 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-400': editor.isActive('italic') }" class="rounded p-1.5 text-gray-600 hover:bg-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 transition">
                                        <Italic class="h-4 w-4" />
                                    </button>
                                    <div class="mx-1 h-5 w-px bg-gray-300 dark:bg-gray-600"></div>
                                    <button type="button" @click="editor.chain().focus().toggleBulletList().run()" :class="{ 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-400': editor.isActive('bulletList') }" class="rounded p-1.5 text-gray-600 hover:bg-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 transition">
                                        <List class="h-4 w-4" />
                                    </button>
                                    <button type="button" @click="editor.chain().focus().toggleOrderedList().run()" :class="{ 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-400': editor.isActive('orderedList') }" class="rounded p-1.5 text-gray-600 hover:bg-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 transition">
                                        <ListOrdered class="h-4 w-4" />
                                    </button>
                                </div>
                                <editor-content :editor="editor" class="prose max-w-none p-3 text-sm min-h-[150px] focus:outline-none dark:prose-invert" />
                            </div>
                            <div v-if="form.errors.content" class="mt-1 text-xs text-red-500">{{ form.errors.content }}</div>
                        </div>

                        <div class="flex items-center gap-2 mt-4 bg-gray-50 p-4 rounded-lg border border-gray-200 dark:bg-gray-800/50 dark:border-gray-700">
                            <input 
                                type="checkbox" 
                                id="is_published" 
                                v-model="form.is_published" 
                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800"
                            />
                            <label for="is_published" class="text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer select-none border-b border-transparent">
                                Publish to User Dashboards
                            </label>
                            <div v-if="form.errors.is_published" class="mt-1 text-xs text-red-500">{{ form.errors.is_published }}</div>
                        </div>

                        <!-- Form Actions -->
                        <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-800">
                            <button 
                                @click="closeModal" 
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
                                <CheckCircle class="h-4 w-4" /> Save
                            </button>
                        </div>

                    </form>
                </div>
                
            </div>
        </div>

    </AppLayout>
</template>
