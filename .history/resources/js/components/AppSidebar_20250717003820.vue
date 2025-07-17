<script setup lang="ts">
import { ref, computed } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'
import NavUser from '@/components/NavUser.vue'
import AppLogo from './AppLogo.vue'
import {
  Collapsible,
  CollapsibleContent,
  CollapsibleTrigger,
} from '@/components/ui/collapsible'
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarHeader,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
} from '@/components/ui/sidebar'
import type { FunctionalComponent } from 'vue';
import type { LucideProps } from 'lucide-vue-next';
import {
  LayoutGrid, UserPlus, UserCog, CalendarClock, Stethoscope, MessageCircle,
  Receipt, ShieldCheck, PackageCheck, ClipboardList, Hospital, ArrowBigRight,
  Megaphone, Globe2, CalendarDays, Users, BookOpen, Folder, ChevronDown,
  ChevronRight, CalendarCheck, UserCheck, Settings, DollarSign, CalendarOff, Search
} from 'lucide-vue-next'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'

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

const page = usePage<AppPageProps>(); // Explicitly type usePage
const user = computed(() => page.props.auth.user);
const userRoles = computed(() => user.value?.roles || []);

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
    items: [],
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
      { title: 'Task Delegations',    routeName: 'admin.task-delegations.index',    icon: ClipboardList,   permission: 'view task delegations' },
      
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
                    { title: 'My Tasks',           routeName: 'staff.task-delegations.index',icon: ClipboardList },
                    { title: 'My Leave Requests', routeName: 'staff.leave-requests.index', icon: CalendarOff }
                ]
            },
            communicationNavGroup,
        ];
    }
    return [];
});

const isSidebarCollapsed = ref(false);
</script>

<template>
  <Sidebar collapsible="icon" @update:collapsed="(value: boolean) => isSidebarCollapsed = value">
    <SidebarHeader>
        <SidebarMenu>
            <SidebarMenuItem>
                <SidebarMenuButton size="lg" as-child>
                    <Link :href="route('dashboard')"><AppLogo /></Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarHeader>
    <SidebarContent>
        <SidebarMenu>
          <Collapsible
            v-for="group in mainNavItems"
            :key="group.group"
            as-child
            :default-open="true"
            class="group/collapsible"
          >
            <SidebarMenuItem>
              <CollapsibleTrigger as-child>
                <SidebarMenuButton :tooltip="group.group">
                  <component :is="group.icon" />
                  <span>{{ group.group }}</span>
                  <ChevronRightIcon class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90" />
                </SidebarMenuButton>
              </CollapsibleTrigger>
              <CollapsibleContent>
                 <SidebarMenu>
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
              </CollapsibleContent>
            </SidebarMenuItem>
          </Collapsible>
        </SidebarMenu>
    </SidebarContent>
    <SidebarFooter>
      <NavUser v-if="user" />
    </SidebarFooter>
  </Sidebar>
</template>
