<script setup lang="ts">
import { ref, computed, nextTick } from 'vue'
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
  ChevronUp, Minimize2, Maximize2, GitFork, BarChart
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

const props = defineProps<{
  unreadCount?: number;
  inventoryAlertCount?: number; // Add this line
}>()

const page = usePage<AppPageProps>();
const user = computed(() => page.props.auth.user);
const userRoles = computed(() => user.value?.roles || []);

const can = (permission: string): boolean => {
    if (!user.value) return false;
    if (user.value.roles.includes('Super Admin')) return true;
    if (user.value.permissions === null) return false;
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
    group: 'Marketing Management',
    icon: Megaphone,
    items: [
      { title: 'Campaigns', routeName: 'admin.marketing-campaigns.index', icon: Megaphone, permission: 'manage marketing' },
      { title: 'Leads', routeName: 'admin.marketing-leads.index', icon: Users, permission: 'manage marketing' },
      { title: 'Landing Pages', routeName: 'admin.landing-pages.index', icon: Globe2, permission: 'manage marketing' },
      { title: 'Platforms', routeName: 'admin.marketing-platforms.index', icon: Package, permission: 'manage marketing' },
      { title: 'Lead Sources', routeName: 'admin.lead-sources.index', icon: GitFork, permission: 'manage marketing' },
      { title: 'Budgets', routeName: 'admin.marketing-budgets.index', icon: DollarSign, permission: 'manage marketing' },
      { title: 'Content', routeName: 'admin.campaign-contents.index', icon: BookOpen, permission: 'manage marketing' },
      { title: 'Tasks', routeName: 'admin.marketing-tasks.index', icon: ClipboardList, permission: 'manage marketing' },
      { title: 'Analytics', routeName: 'admin.marketing-analytics.dashboard-data', icon: BarChart, permission: 'view marketing analytics' },
    ],
  },
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
      { title: 'Maintenance', routeName: 'admin.inventory-maintenance-records.index', icon: Wrench, permission: 'view maintenance records' },
      { title: 'Transactions', routeName: 'admin.inventory-transactions.index', icon: ClipboardList, permission: 'view inventory transactions' },
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
                    { title: 'My Leave Requests', routeName: 'staff.leave-requests.index', icon: CalendarOff },
                    { title: 'My Campaigns', routeName: 'staff.marketing-campaigns.index', icon: Megaphone },
                    { title: 'My Leads', routeName: 'staff.marketing-leads.index', icon: Users },
                    { title: 'My Marketing Tasks', routeName: 'staff.marketing-tasks.index', icon: ClipboardList },
                ]
            },
            communicationNavGroup,
        ];
    }
    return [];
});

// Toggle group - only this should affect group opening/closing
const toggleGroup = (groupName: string, event?: Event) => {
  // Stop propagation if event is provided
  if (event) {
    event.stopPropagation();
    event.preventDefault();
  }
  
  const currentIndex = openGroups.value.indexOf(groupName)
  
  if (currentIndex > -1) {
    // Group is already open, close it
    openGroups.value.splice(currentIndex, 1)
    areAllGroupsExpanded.value = false // Reset expand all state
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
  
  // Ensure the state is preserved
  nextTick(() => {
    if (areAllGroupsExpanded.value) {
      const allGroupNames = mainNavItems.value.map((group: SidebarNavGroup) => group.group);
      openGroups.value = allGroupNames;
    }
  });
}

// Function to close a specific group
const closeGroup = (groupName: string) => {
  const currentIndex = openGroups.value.indexOf(groupName);
  if (currentIndex > -1) {
    openGroups.value.splice(currentIndex, 1);
  }
};

// Toggle expand/collapse all with single button
const toggleAllGroups = (event?: Event) => {
  // Stop propagation if event is provided
  if (event) {
    event.stopPropagation();
    event.preventDefault();
  }
  
  if (areAllGroupsExpanded.value) {
    // Currently expanded, collapse all
    openGroups.value = []
    areAllGroupsExpanded.value = false
  } else {
    // Currently collapsed, expand all
    const allGroupNames = mainNavItems.value.map((group: SidebarNavGroup) => group.group)
    openGroups.value = allGroupNames
    areAllGroupsExpanded.value = true
  }
  
  // Ensure the state is preserved
  nextTick(() => {
    if (areAllGroupsExpanded.value) {
      const allGroupNames = mainNavItems.value.map((group: SidebarNavGroup) => group.group);
      openGroups.value = allGroupNames;
    }
  });
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
                <div class="group/collapsible">
                    <div 
                        @click="toggleGroup(group.group, $event)"
                        class="font-medium text-sidebar-foreground hover:bg-accent hover:text-accent-foreground rounded-md transition-colors duration-150 justify-between px-3 py-2 w-full flex items-center gap-2 cursor-pointer"
                        :title="group.group"
                    >
                        <div class="flex items-center gap-2 min-w-0">
                            <component :is="group.icon" class="h-4 w-4 flex-shrink-0" />
                            <span class="truncate">{{ group.group }}</span>
                        </div>
                        
                        <ChevronRight
                            class="ml-auto h-4 w-4 transition-transform duration-200 flex-shrink-0"
                            :class="{ 'rotate-90': openGroups.includes(group.group) }"/>
                    </div>
                    
                    <div 
                        v-show="openGroups.includes(group.group)"
                        class="overflow-hidden transition-all duration-200 ease-in-out"
                        :class="{
                            'max-h-96 opacity-100': openGroups.includes(group.group),
                            'max-h-0 opacity-0': !openGroups.includes(group.group)
                        }">
                        <SidebarMenu class="pl-4 mt-1 space-y-1">
                            <template v-for="item in group.items" :key="item.title">
                                <SidebarMenuItem v-if="!item.permission || can(item.permission)">
                                    <SidebarMenuButton as-child>
                                        <Link 
                                            :href="item.routeName ? route(item.routeName) : '#'" 
                                            class="gap-2 px-2 py-1.5 text-sm hover:bg-muted/30 rounded-md w-full flex items-center"
                                            preserve-scroll 
                                            preserve-state
                                            @click.stop>
                                            <component :is="item.icon" class="h-4 w-4 flex-shrink-0" />
                                            <span class="truncate">{{ item.title }}</span>
                                            <span v-if="item.title === 'Alerts' && inventoryAlertCount > 0" class="ml-auto text-xs bg-red-500 text-white rounded-full px-2">{{ inventoryAlertCount }}</span>
                                        </Link>
                                    </SidebarMenuButton>
                                </SidebarMenuItem>
                            </template>
                        </SidebarMenu>
                    </div>
                </div>
            </div>
        </SidebarMenu>
    </SidebarContent>
    
    <SidebarFooter class="border-t pt-2">
        <SidebarMenu>
            <SidebarMenuItem>
                <SidebarMenuButton 
                    @click.stop="(event) => toggleAllGroups(event)"
                    class="w-full justify-between px-2 py-2 text-sm hover:bg-muted/30 rounded-md"
                    :aria-expanded="areAllGroupsExpanded"
                >
                    <div class="flex items-center gap-2">
                        <component 
                            :is="areAllGroupsExpanded ? Minimize2 : Maximize2" 
                            class="h-4 w-4 flex-shrink-0" 
                        />
                        <span class="truncate">{{ areAllGroupsExpanded ? 'Collapse All' : 'Expand All' }}</span>
                    </div>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
        <NavUser />
    </SidebarFooter>
  </Sidebar>
</template>

<style scoped>
/* Smooth transitions */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 200ms;
}

/* Fix for collapsed sidebar - show icons clearly */
.sidebar[data-state="collapsed"] .truncate {
  display: none;
}

.sidebar[data-state="collapsed"] .flex-shrink-0 {
  margin: 0 auto;
}

/* Ensure proper spacing in collapsed mode */
.sidebar[data-state="collapsed"] .flex.items-center.gap-2 {
  justify-content: center;
}

.sidebar[data-state="collapsed"] .min-w-0 {
  min-width: auto;
}
</style>