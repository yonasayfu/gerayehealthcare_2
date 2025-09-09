<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { dashboard, quotesIndex } from '@/routes';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import {
    BookOpen,
    Folder,
    LayoutGrid,
    Users,
    UserCheck,
    MessageSquare,
    Bell,
    Shield,
    Settings,
    Search,
    UserCog,
    Building,
    Mail,
    Group
} from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const page = usePage();
const user = page.props.auth?.user;

const mainNavItems: NavItem[] = (() => {
    const items: NavItem[] = [
        { title: 'Dashboard', href: dashboard(), icon: LayoutGrid },
    ];

    // User Management - Available to all authenticated users
    items.push({ title: 'Users', href: '/admin/users', icon: Users });

    // Staff Management - Available to admins and above
    if (user?.roles?.some((role: any) => ['admin', 'ceo', 'coo', 'superadmin'].includes(role.name))) {
        items.push({ title: 'Staff Management', href: '/admin/staff', icon: UserCheck });
    }

    // Messaging - Available to all authenticated users
    items.push({ title: 'Messages', href: '/messages', icon: MessageSquare });

    // Groups - Available to all authenticated users
    items.push({ title: 'Groups', href: '/groups', icon: Group });

    // Notifications - Available to all authenticated users
    items.push({ title: 'Notifications', href: '/notifications', icon: Bell });

    // Role Management - Available to superadmin and admin only
    if (user?.roles?.some((role: any) => ['admin', 'superadmin'].includes(role.name))) {
        items.push({ title: 'Role Management', href: '/admin/roles', icon: Shield });
    }

    // Global Search - Available to all authenticated users
    items.push({ title: 'Global Search', href: '/admin/global-search', icon: Search });

    // RBAC Dashboard - Available to users with view-reports permission
    if (user?.permissions?.includes('view-reports') || user?.roles?.some((role: any) => ['admin', 'ceo', 'coo', 'superadmin'].includes(role.name))) {
        items.push({ title: 'RBAC Dashboard', href: '/rbac/dashboard', icon: UserCog });
    }

    // Settings - Available to admins and above
    if (user?.roles?.some((role: any) => ['admin', 'ceo', 'coo', 'superadmin'].includes(role.name))) {
        items.push({ title: 'Settings', href: '/admin/settings', icon: Settings });
    }

    // Quotes - Keep existing functionality
    try {
        items.push({ title: 'Quotes', href: quotesIndex(), icon: BookOpen });
    } catch (e) {
        // Skip Quotes when route is not registered in Ziggy
    }

    return items;
})();

const footerNavItems: NavItem[] = [
    // {
    //     title: 'Github Repo',
    //     href: 'https://github.com/laravel/vue-starter-kit',
    //     icon: Folder,
    // },
    // {
    //     title: 'Documentation',
    //     href: 'https://laravel.com/docs/starter-kits#vue',
    //     icon: BookOpen,
    // },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
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
