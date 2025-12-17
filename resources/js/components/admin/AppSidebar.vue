<script setup lang="ts">
import NavFooter from '@/components/admin/NavFooter.vue';
import NavMain from '@/components/admin/NavMain.vue';
import NavUser from '@/components/admin/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/admin/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid } from 'lucide-vue-next';
import AppLogo from '././AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Admin Dashboard',
        href: '/admin/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'User Management',
        href: '/admin/users',
        icon: LayoutGrid,
    },
     {
        title: 'Category Management',
        href: '/admin/category',
        icon: LayoutGrid,
    },
    {
        title: 'Brand Management',
        href: '/admin/brands',
        icon: LayoutGrid,
    },
    {
        title: 'Product Management',
        href: '/admin/product',
        icon: LayoutGrid,
    },
    {
        title: 'Order Management',
        href: '/admin/orders',
        icon: LayoutGrid,
    },
    {
        title: 'Settings',
        href: '/admin/settings',
        icon: LayoutGrid,
    },
  
];

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
