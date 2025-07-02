<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import { NavMain } from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem, type NavGroup } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid,
  UserPlus,
  UserCog,
  CalendarClock,
  Stethoscope,
  MessageCircle,
  FileInvoice,
  ShieldCheck,
  PackageCheck,
  ClipboardList,
  Hospital,
  ArrowBigRight,
  Megaphone,
  Globe2,
  CalendarDays,
  Users, } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavGroup[] = [
  {
    group: 'Patient Management',
    items: [
      { title: 'Dashboard', href: '/dashboard', icon: LayoutGrid },
      { title: 'Patients', href: '/dashboard/patients', icon: UserPlus },
      { title: 'Caregiver Assignments', href: '/dashboard/assignments', icon: CalendarClock },
      { title: 'Visit Services', href: '/dashboard/visits', icon: Stethoscope },
    ],
  },
  {
    group: 'Administrative Tools',
    items: [
      { title: 'Staff', href: '/dashboard/staff', icon: UserCog },
      { title: 'Messages', href: '/dashboard/messages', icon: MessageCircle },
      { title: 'Invoices', href: '/dashboard/invoices', icon: FileInvoice },
      { title: 'Insurance Claims', href: '/dashboard/insurance', icon: ShieldCheck },
      { title: 'Inventory Items', href: '/dashboard/inventory', icon: PackageCheck },
      { title: 'Admin Task Tracking', href: '/dashboard/tasks', icon: ClipboardList },
    ],
  },
  {
    group: 'Integrations',
    items: [
      { title: 'Partner Hospitals', href: '/dashboard/partners', icon: Hospital },
      { title: 'Referrals', href: '/dashboard/referrals', icon: ArrowBigRight },
      { title: 'Marketing Campaigns', href: '/dashboard/marketing', icon: Megaphone },
      { title: 'International Referrals', href: '/dashboard/international', icon: Globe2 },
      { title: 'Events', href: '/dashboard/events', icon: CalendarDays },
      { title: 'NGO Networks', href: '/dashboard/networks', icon: Users },
    ],
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
