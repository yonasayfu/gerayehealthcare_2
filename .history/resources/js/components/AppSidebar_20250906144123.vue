<script setup lang="ts">
import { ref, computed, nextTick, onMounted, watch } from 'vue'
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
  Receipt, ShieldCheck, ClipboardList, ArrowBigRight,
  Megaphone, Globe2, CalendarDays, Users, BookOpen, Folder,
  ChevronRight, CalendarCheck, UserCheck, Settings, DollarSign, CalendarOff, Warehouse, Package, FileText, Wrench, Bell,
  Minimize2, Maximize2, GitFork, BarChart, Pill, FlaskConical
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

// Removed AppPageProps missing type import to satisfy TS

const props = defineProps<{
  unreadCount?: number;
  inventoryAlertCount?: number; // Add this line
}>()

const page = usePage();
const user = computed(() => (page.props as any)?.auth?.user ?? null);
// Normalize roles to lowercase for consistent checks
const userRoles = computed(() => (user.value?.roles || []).map((r: string) => r.toLowerCase()));

const can = (permission: string): boolean => {
    if (!user.value) return false;

    // Check if user is super admin
    if (userRoles.value?.some((role: string) => role === 'super-admin')) {
        return true;
    }

    // Check if user has the specific permission
    return user.value.permissions?.includes(permission) || false;
}

// Dynamic role checking - no longer hardcoded
const hasRole = (role: string): boolean => {
    if (!user.value) return false;
    return userRoles.value?.includes(role.toLowerCase()) || false;
}

// Permission-based access instead of role-based
const hasAnyPermission = (permissions: string[]): boolean => {
    if (!user.value) return false;
    if (userRoles.value?.some((role: string) => role === 'super-admin')) return true;
    return permissions.some(permission => user.value.permissions?.includes(permission));
}

const hasAllPermissions = (permissions: string[]): boolean => {
    if (!user.value) return false;
    if (userRoles.value?.some((role: string) => role === 'super-admin')) return true;
    return permissions.every(permission => user.value.permissions?.includes(permission));
}

// Track open groups with localStorage persistence
const SIDEBAR_STORAGE_KEY = 'sidebar-open-groups'
const SIDEBAR_EXPAND_ALL_KEY = 'sidebar-expand-all'

// Initialize from localStorage or empty array
const openGroups = ref<string[]>([])
const areAllGroupsExpanded = ref(false)

// Load sidebar state from localStorage on component mount
const loadSidebarState = () => {
  try {
    const stored = localStorage.getItem(SIDEBAR_STORAGE_KEY)
    const expandedState = localStorage.getItem(SIDEBAR_EXPAND_ALL_KEY)
    
    if (stored) {
      openGroups.value = JSON.parse(stored)
    }
    
    if (expandedState) {
      areAllGroupsExpanded.value = JSON.parse(expandedState)
    }
  } catch (error) {
    console.warn('Failed to load sidebar state from localStorage:', error)
    openGroups.value = []
    areAllGroupsExpanded.value = false
  }
}

// Save sidebar state to localStorage
const saveSidebarState = () => {
  try {
    localStorage.setItem(SIDEBAR_STORAGE_KEY, JSON.stringify(openGroups.value))
    localStorage.setItem(SIDEBAR_EXPAND_ALL_KEY, JSON.stringify(areAllGroupsExpanded.value))
  } catch (error) {
    console.warn('Failed to save sidebar state to localStorage:', error)
  }
}

// Watch for changes and save to localStorage
watch([openGroups, areAllGroupsExpanded], () => {
  saveSidebarState()
}, { deep: true })

const communicationNavGroup: SidebarNavGroup = {
    group: 'Communication',
    icon: MessageCircle,
    items: [
        { title: 'Messages', routeName: 'admin.messages', icon: MessageCircle, permission: 'view messages' },
    ]
};

// Notifications are handled by the NotificationBell component in the header

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
  {
    group: 'Medical Records',
    icon: FileText,
    items: [
      { title: 'Medical Documents', routeName: 'admin.medical-documents.index', icon: Folder, permission: 'view medical documents' },
      { title: 'Prescriptions', routeName: 'admin.prescriptions.index', icon: FileText, permission: 'view prescriptions' },
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
      { title: 'Leave Requests', routeName: 'admin.leave-requests.index', icon: CalendarOff },
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
    group: 'Financial Management',
    icon: DollarSign,
    items: [
      { title: 'Invoices', routeName: 'admin.invoices.index', icon: Receipt, permission: 'view invoices' },
      { title: 'Staff Payouts', routeName: 'admin.staff-payouts.index', icon: DollarSign, permission: 'view financial reports' },
      { title: 'Financial Reports', routeName: 'admin.reports.revenue-ar', icon: BarChart, permission: 'view financial reports' },
      
      { title: 'Budget Management', routeName: 'admin.marketing-budgets.index', icon: DollarSign, permission: 'manage budgets' },
    ],
  },
  {
    group: 'Reports & Analytics',
    icon: BarChart,
    items: [
      { title: 'Service Volume', routeName: 'admin.reports.service-volume', icon: ClipboardList, permission: 'view system reports' },
      { title: 'Revenue & AR', routeName: 'admin.reports.revenue-ar', icon: DollarSign, permission: 'view financial reports' },
      { title: 'Marketing ROI', routeName: 'admin.reports.marketing-roi', icon: BarChart, permission: 'view marketing analytics' },
      { title: 'Performance Metrics', routeName: 'admin.analytics.dashboard', icon: BarChart, permission: 'view analytics dashboard' },
    ],
  },
  {
    group: 'Events Management',
    icon: CalendarDays,
    items: [
      { title: 'Events', routeName: 'admin.events.index', icon: CalendarDays, permission: 'view events' },
      { title: 'Eligibility Criteria', routeName: 'admin.eligibility-criteria.index', icon: ClipboardList, permission: 'view eligibility criteria' },
      { title: 'Event Recommendations', routeName: 'admin.event-recommendations.index', icon: Users, permission: 'view event recommendations' },
      { title: 'Event Staff Assignments', routeName: 'admin.event-staff-assignments.index', icon: UserCog, permission: 'view event staff assignments' },
      /Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/Admin/EventParticipants
      { title: 'Event Broadcasts', routeName: 'admin.event-broadcasts.index', icon: Megaphone, permission: 'view event broadcasts' },
    ],
  },
  {
    group: 'Insurance',
    icon: ShieldCheck,
    items: [
      { title: 'Insurance Companies', routeName: 'admin.insurance-companies.index', icon: ShieldCheck, permission: 'view insurance companies' },
      { title: 'Corporate Clients', routeName: 'admin.corporate-clients.index', icon: Users, permission: 'view corporate clients' },
      { title: 'Insurance Policies', routeName: 'admin.insurance-policies.index', icon: FileText, permission: 'view insurance policies' },
      { title: 'Employee Insurance Records', routeName: 'admin.employee-insurance-records.index', icon: ClipboardList, permission: 'view employee insurance records' },
      { title: 'Insurance Claims', routeName: 'admin.insurance-claims.index', icon: Receipt, permission: 'view insurance claims' },
      { title: 'Ethiopian Calendar Days', routeName: 'admin.ethiopian-calendar-days.index', icon: CalendarDays, permission: 'view ethiopian calendar days' },
    ],
  },
  {
    group: 'Partnerships',
    icon: Users,
    items: [
      { title: 'Partners', routeName: 'admin.partners.index', icon: Users, permission: 'view partners' },
      { title: 'Agreements', routeName: 'admin.partner-agreements.index', icon: FileText, permission: 'view partner agreements' },
      { title: 'Referrals', routeName: 'admin.referrals.index', icon: ArrowBigRight, permission: 'view referrals' },
      { title: 'Commissions', routeName: 'admin.partner-commissions.index', icon: DollarSign, permission: 'view partner commissions' },
      { title: 'Engagements', routeName: 'admin.partner-engagements.index', icon: ClipboardList, permission: 'view partner engagements' },
      { title: 'Referral Documents', routeName: 'admin.referral-documents.index', icon: Folder, permission: 'view referral documents' },
      { title: 'Shared Invoices', routeName: 'admin.shared-invoices.index', icon: Receipt, permission: 'view shared invoices' },
    ],
  },
  communicationNavGroup,
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

// Dynamic navigation based on permissions, not hardcoded roles
const getFilteredNavItems = (): SidebarNavGroup[] => {
    const filteredGroups: SidebarNavGroup[] = [];

    for (const group of allAdminNavItems) {
        // Skip super admin only groups unless user is super admin
        if (group.superAdminOnly && !hasRole('super-admin')) {
            continue;
        }

        // Filter items based on permissions
        const filteredItems = group.items.filter(item => {
            // Always show dashboard
            if (item.routeName === 'dashboard') return true;

            // If item has permission requirement, check it
            if (item.permission) {
                return can(item.permission);
            }

            // If no permission specified, show to all authenticated users
            return true;
        });

        // Only include group if it has visible items
        if (filteredItems.length > 0) {
            filteredGroups.push({
                ...group,
                items: filteredItems
            });
        }
    }

    return filteredGroups;
};

const mainNavItems = computed<SidebarNavGroup[]>(() => {
    if (!user.value) return [];

    // For super admin, show everything
    if (hasRole('super-admin')) {
        return allAdminNavItems;
    }

    // For all other roles, dynamically filter based on permissions
    return getFilteredNavItems();
});

// Legacy staff navigation for backward compatibility
const getStaffNavItems = (): SidebarNavGroup[] => {
    if (!hasRole('staff')) return [];

    const staffGroups: SidebarNavGroup[] = [
        {
            group: 'Patient Care',
            icon: UserPlus,
            items: [
                { title: 'Dashboard', routeName: 'dashboard', icon: LayoutGrid },
                { title: 'Patients', routeName: 'admin.patients.index', icon: UserPlus, permission: 'view patients' },
                { title: 'My Visits', routeName: 'staff.my-visits.index', icon: Stethoscope },
                { title: 'Visit Services', routeName: 'admin.visit-services.index', icon: Stethoscope, permission: 'view visit services' },
                { title: 'Appointments', routeName: 'admin.appointments.index', icon: CalendarDays, permission: 'view appointments' },
            ]
        },
        {
            group: 'My Tools',
            icon: UserCheck,
            items: [
                { title: 'My Earnings', routeName: 'staff.my-earnings.index', icon: DollarSign },
                { title: 'My Availability', routeName: 'staff.my-availability.index', icon: UserCheck },
                { title: 'My Tasks', routeName: 'staff.task-delegations.index', icon: ClipboardList },
                { title: 'My Leave Requests', routeName: 'staff.leave-requests.index', icon: CalendarOff },
            ]
        },
        {
            group: 'Clinical Tools',
            icon: Stethoscope,
            items: [
                { title: 'Medical Records', routeName: 'admin.medical-records.index', icon: FileText, permission: 'view medical records' },
                { title: 'Prescriptions', routeName: 'admin.prescriptions.index', icon: Pill, permission: 'view prescriptions' },
                { title: 'Lab Results', routeName: 'admin.lab-results.index', icon: FlaskConical, permission: 'view lab results' },
            ]
        },
        communicationNavGroup,
    ];

    // Filter staff groups based on permissions
    return staffGroups.map(group => ({
        ...group,
        items: group.items.filter(item => {
            if (item.routeName === 'dashboard') return true;
            if (item.permission) return can(item.permission);
            return true;
        })
    })).filter(group => group.items.length > 0);
};

// Toggle group - only this should affect group opening/closing
const toggleGroup = (groupName: string, event?: Event) => {
  
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
    // State will be saved automatically by the watcher
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
  
  // State will be saved automatically by the watcher
}

const isSidebarCollapsed = ref(false);

// Load sidebar state when component mounts
onMounted(() => {
  loadSidebarState()
})
</script>

<template>
  <Sidebar 
    collapsible="icon" 
    v-model:collapsed="isSidebarCollapsed"
    class="sidebar bg-sidebar text-sidebar-foreground"
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
                                            <span v-if="item.title === 'Alerts' && (inventoryAlertCount || 0) > 0" class="ml-auto text-xs bg-red-500 text-white rounded-full px-2">{{ inventoryAlertCount || 0 }}</span>
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
                    @click.stop="(event: Event) => toggleAllGroups(event)"
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
