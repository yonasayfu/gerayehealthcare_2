<script setup lang="ts">
import { ref, computed } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'
import NavUser from '@/components/NavUser.vue'
import AppLogo from './AppLogo.vue'
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
  ChevronRight, CalendarCheck, UserCheck, Settings, DollarSign, CalendarOff, Search, Warehouse, Package, FileText, Wrench, Bell,
  ChevronUp, Minimize2, Maximize2
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

import { type AppPageProps } from '@/types';

const page = usePage<AppPageProps>();
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

// Track open groups
const openGroups = ref<string[]>([])
const areAllGroupsExpanded = ref(false)

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
      { title: 'Task Delegations', routeName: 'admin.task-delegations.index', icon: ClipboardList, permission: 'view task delegations' },
      { title: 'Leave Requests', routeName: 'admin.admin-leave-requests.index', icon: CalendarOff },
    ],
  },
  {
    group: 'Inventory Management',
    icon: Warehouse,
    items: [
      { title: 'Inventory Items', routeName: 'admin.inventory-items.index', icon: Package, permission: 'view inventory items' },
      { title: 'Suppliers', routeName: 'admin.suppliers.index', icon: UserPlus, permission: 'view suppliers' },
      { title: 'Requests', routeName: 'admin.inventory-requests.index', icon: FileText, permission: 'view inventory requests' },
      { title: 'Transactions', routeName: 'admin.inventory-transactions.index', icon: ClipboardList, permission: 'view inventory transactions' },
      { title: 'Maintenance', routeName: 'admin.inventory-maintenance-records.index', icon: Wrench, permission: 'view maintenance records' },
      { title: 'Alerts', routeName: 'admin.inventory-alerts.index', icon: Bell, permission: 'view inventory alerts' },
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
                    { title: 'My Tasks', routeName: 'staff.task-delegations.index', icon: ClipboardList },
                    { title: 'My Leave Requests', routeName: 'staff.leave-requests.index', icon: CalendarOff }
                ]
            },
            communicationNavGroup,
        ];
    }
    return [];
});

// Toggle group - only this should affect group opening/closing
const toggleGroup = (groupName: string) => {
  const currentIndex = openGroups.value.indexOf(groupName)
  
  if (currentIndex > -1) {
    // Group is already open, close it
    openGroups.value.splice(currentIndex, 1)
  } else {
    // Group is closed, open it
    // Only enforce 2-group limit in normal mode (not when all are expanded)
    if (!areAllGroupsExpanded.value && openGroups.value.length >= 2) {
      // Remove the first (oldest) open group
      openGroups.value.shift()
    }
    // Add the new group
    openGroups.value.push(groupName)
  }
}

// Toggle expand/collapse all with single button
const toggleAllGroups = () => {
  if (areAllGroupsExpanded.value) {
    // Currently expanded, collapse all
    openGroups.value = []
    areAllGroupsExpanded.value = false
  } else {
    // Currently collapsed, expand all
    const allGroupNames = mainNavItems.value.map(group => group.group)
    openGroups.value = allGroupNames
    areAllGroupsExpanded.value = true
  }
}

const isSidebarCollapsed = ref(false);
</script>

<template>
  <Sidebar 
    collapsible="icon" 
    v-model:collapsed="isSidebarCollapsed"
    class="sidebar"
  >
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
        <SidebarMenu>
            <div v-for="group in mainNavItems" :key="group.group" class="mb-1">
                <!-- Manual collapsible implementation -->
                <div class="group/collapsible">
                    <div 
                        @click="toggleGroup(group.group)"
                        class="font-medium text-sidebar-foreground hover:bg-accent hover:text-accent-foreground rounded-md transition-colors duration-150 justify-between px-3 py-2 w-full flex items-center gap-2 cursor-pointer"
                        :title="group.group"
                    >
                        <div class="flex items-center gap-2 min-w-0">
                            <component :is="group.icon" class="h-4 w-4 flex-shrink-0" />
                            <span class="truncate">{{ group.group }}</span>
                        </div>
                        <ChevronRight
                            class="ml-auto h-4 w-4 transition-transform duration-200 flex-shrink-0"
                            :class="{ 'rotate-90': openGroups.includes(group.group) }"
                        />
                    </div>
                    
                   
                    <!-- Group items (menu items) -->
                    <div v-show="openGroups.includes(group.group)" class="pl-8">
                        <SidebarMenu>
                            <SidebarMenuItem
                                v-for="item in group.items"
                                :key="item.title"
                            >
                                <Link 
                                    v-if="item.routeName"
                                    :href="route(item.routeName)"
                                    @click.stop
                                    class="flex items-center gap-2 text-sidebar-foreground hover:bg-accent hover:text-accent-foreground rounded-md transition-colors duration-150 px-3 py-2"
                                    :title="item.title"
                                >
                                    <component :is="item.icon" class="h-4 w-4 flex-shrink-0" />
                                    <span class="truncate">{{ item.title }}</span>
                                </Link>

                                <a
                                    v-else
                                    :href="item.href"
                                    @click.stop
                                    class="flex items-center gap-2 text-sidebar-foreground hover:bg-accent hover:text-accent-foreground rounded-md transition-colors duration-150 px-3 py-2"
                                    :title="item.title"
                                >
                                    <component :is="item.icon" class="h-4 w-4 flex-shrink-0" />
                                    <span class="truncate">{{ item.title }}</span>
                                </a>
                            </SidebarMenuItem>
                        </SidebarMenu>
                    </div>
                </div>
            </div>
        </SidebarMenu>
    </SidebarContent>

    <SidebarFooter>
        <!-- Optional footer content -->
    </SidebarFooter>
  </Sidebar>
</template>

<style scoped>
/* Styling for collapsible behavior */
.group/collapsible {
  cursor: pointer;
}
.group/collapsible .rotate-90 {
  transform: rotate(90deg);
}
</style>
