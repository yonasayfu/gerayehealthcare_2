<script setup lang="ts">
import { ref, computed } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'
import NavFooter from '@/components/NavFooter.vue'
import NavUser from '@/components/NavUser.vue'
import AppLogo from './AppLogo.vue'
import {
  Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem,
} from '@/components/ui/sidebar'
import type { FunctionalComponent } from 'vue';
import type { LucideProps } from 'lucide-vue-next';
import {
  LayoutGrid, UserPlus, UserCog, CalendarClock, Stethoscope, MessageCircle,
  Receipt, ShieldCheck, PackageCheck, ClipboardList, Hospital, ArrowBigRight,
  Megaphone, Globe2, CalendarDays, Users, BookOpen, Folder, ChevronDown,
  ChevronRight, CalendarCheck, UserCheck, Settings, DollarSign, CalendarOff
} from 'lucide-vue-next'

interface SidebarNavItem {
  title: string;
  routeName?: string;
  href?: string;
  icon: FunctionalComponent<LucideProps>;
  permission?: string;
}

interface SidebarNavGroup {
  group: string;
  icon: FunctionalComponent<LucideProps>;
  items: SidebarNavItem[];
  superAdminOnly?: boolean;
}

import { type AppPageProps } from '@/types'; // Import AppPageProps

const userRoles = computed(() => user.value?.roles || [])

const can = (permission: string): boolean => {
    if (!user.value) return false;
    if (user.value.roles.includes('Super Admin')) return true;
    if (!user.value.permissions) return false;
    return user.value.permissions.includes(permission);
}

const isSuperAdmin = computed(() => userRoles.value.includes('Super Admin'))
const isAdmin = computed(() => userRoles.value.includes('Admin'))
const isStaff = computed(() => userRoles.value.includes('Staff'))

const communicationNavGroup: SidebarNavGroup = {
    group: 'Communication',
    icon: MessageCircle,
    items: [
        { title: 'Messages', routeName: 'messages.index', icon: MessageCircle },
    ],
};

const allAdminNavItems: SidebarNavGroup[] = [
  {
    group: 'Patient Management',
    icon: UserPlus,
    items: [
      { title: 'Dashboard', routeName: 'dashboard', icon: LayoutGrid },
      { title: 'Patients', routeName: 'admin.patients.index', icon: UserPlus, permission: 'view patients' },
      { title: 'Caregiver Assignments', routeName: 'admin.assignments.index', icon: CalendarClock, permission: 'view assignments' },
      { title: 'Visit Services', routeName: 'admin.visit-services.index', icon: Stethoscope, permission: 'view visits' },
    ],
  },
  communicationNavGroup,
  {
    group: 'Administrative Tools',
    icon: UserCog,
    items: [
      { title: 'Staff', routeName: 'admin.staff.index', icon: UserCog, permission: 'view staff' },
      { title: 'Staff Availability', routeName: 'admin.staff-availabilities.index', icon: CalendarCheck, permission: 'view staff' },
      { title: 'Staff Payouts', routeName: 'admin.staff-payouts.index', icon: DollarSign },
      { title: 'Invoices', routeName: 'admin.invoices.index', icon: Receipt },
      { title: 'Services', routeName: 'admin.services.index', icon: ClipboardList },
      { title: 'Leave Requests', routeName: 'admin.admin-leave-requests.index', icon: CalendarOff },
    ],
  },
  {
    group: 'Integrations',
    icon: Globe2,
    items: [],
  },
   {
      group: 'System Management',
      icon: Settings,
      superAdminOnly: true,
      items: [
          { title: 'Role Management', routeName: 'admin.roles.index', icon: Users, permission: 'manage roles' },
          { title: 'User Management', routeName: 'admin.users.index', icon: UserCog, permission: 'manage users' },
      ]
  }
];

const mainNavItems = computed<SidebarNavGroup[]>(() => {
    if (isSuperAdmin.value) {
        return allAdminNavItems;
    }
    if (isAdmin.value) {
        return allAdminNavItems.filter(group => !group.superAdminOnly);
    }
    if (isStaff.value) {
        return [
            {
                group: 'My Tools',
                icon: UserCheck,
                items: [
                    { title: 'Dashboard', routeName: 'dashboard', icon: LayoutGrid },
                    { title: 'My Visits', routeName: 'staff.my-visits.index', icon: Stethoscope },
                    { title: 'My Earnings', routeName: 'staff.my-earnings.index', icon: DollarSign },
                    { title: 'My Availability', routeName: 'staff.my-availability.index', icon: UserCheck },
                    { title: 'My Leave Requests', routeName: 'staff.leave-requests.index', icon: CalendarOff }
                ]
            },
            communicationNavGroup,
        ];
    }
    return [];
});

const openGroups = ref<Record<string, boolean>>({})
const toggleGroup = (groupName: string) => { openGroups.value[groupName] = !openGroups.value[groupName] }
const footerNavItems = [
  { title: 'Github Repo', href: 'https://github.com/laravel/vue-starter-kit', icon: Folder },
  { title: 'Documentation', href: 'https://laravel.com/docs/starter-kits#vue', icon: BookOpen },
]
const isSidebarCollapsed = ref(false);
</script>

<template>
  <Sidebar collapsible="icon" variant="inset" @update:collapsed="(value: boolean) => isSidebarCollapsed = value">
    <SidebarHeader>
      <SidebarMenu>
        <SidebarMenuItem>
          <SidebarMenuButton size="lg" as-child>
            <Link :href="route('dashboard')"><AppLogo /></Link>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarHeader>
    <SidebarContent class="flex-grow overflow-y-auto custom-scrollbar">
      <div v-if="user">
        <div v-for="group in mainNavItems" :key="group.group">
          <SidebarMenuButton @click="toggleGroup(group.group)" class="flex w-full items-center justify-between px-2 py-1 text-sm font-semibold text-muted-foreground hover:text-primary">
            <span class="flex items-center gap-2 uppercase tracking-wide">
              <component :is="group.icon" class="h-4 w-4 text-primary" />
              <span v-if="!isSidebarCollapsed" class="truncate">{{ group.group }}</span>
            </span>
            <component :is="openGroups[group.group] !== false ? ChevronDown : ChevronRight" class="h-4 w-4" />
          </SidebarMenuButton>
          <SidebarMenu v-if="openGroups[group.group] !== false">
            <template v-for="item in group.items" :key="item.title">
              <SidebarMenuItem v-if="!item.permission || can(item.permission)">
                <SidebarMenuButton as-child>
                  <Link :href="item.routeName ? route(item.routeName) : '#'" class="flex items-center gap-2 px-2 py-1 text-sm">
                    <component :is="item.icon" class="h-4 w-4" />
                    <span v-if="!isSidebarCollapsed">{{ item.title }}</span>
                  </Link>
                </SidebarMenuButton>
              </SidebarMenuItem>
            </template>
          </SidebarMenu>
        </div>
      </div>
    </SidebarContent>
    <SidebarFooter>
      <NavFooter :items="footerNavItems" :is-collapsed="isSidebarCollapsed" />
      <NavUser v-if="user" :is-collapsed="false" />
    </SidebarFooter>
  </Sidebar>
  <slot />
</template>

<style scoped>
.truncate { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.custom-scrollbar::-webkit-scrollbar { width: 18px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 4px; border: 2px solid transparent; background-clip: content-box; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background-color: #94a3b8; }
.custom-scrollbar { scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent; }
</style>
