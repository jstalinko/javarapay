<script setup lang="ts">
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import type { Component } from 'vue';
import { ArrowRightLeft, BoxSelect, LayoutGrid, User,Banknote, Info } from 'lucide-vue-next';
interface NavItem {
    title: string;
    url?: string|null;
    href?: string|null;
    icon?: Component;
}

defineProps<{
    items: NavItem[];
}>();

const adminMenus: NavItem[] = [
   {
    title:'Dashboard Admin',
    href:'/dashboard/admin',
    icon:LayoutGrid
   },
    {
        title: 'Project Review',
        href: '/dashboard/admin/project-review', 
        icon: BoxSelect
    },
    {
        title: 'User Management',
        href: '/dashboard/admin/user-management',
        icon: User
    },
    {
        title:'Transactions',
        href:'/dashboard/admin/transactions',
        icon:ArrowRightLeft
    },
    {
        title:'Withdrawal',
        href:'/dashboard/admin/withdrawal',
        icon:Banknote
    },
    {
        title:'Announcements',
        href:'/dashboard/admin/announcements',
        icon: Info
    }
];

const page = usePage<SharedData>();
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>Platform</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in items" :key="item.title">
                <SidebarMenuButton as-child :is-active="item.href === page.url">
                    <Link :href="item.href">
                        <component :is="item.icon" />
                        <span>{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
     <SidebarGroup class="px-2 py-0" v-if="page.props.auth.user.role === 'admin'">
        <SidebarGroupLabel>Administrator</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in adminMenus" :key="item.title">
                <SidebarMenuButton as-child :is-active="item.href === page.url">
                    <Link :href="item.href">
                        <component :is="item.icon" />
                        <span>{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
