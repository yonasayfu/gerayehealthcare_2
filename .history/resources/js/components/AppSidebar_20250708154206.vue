<script setup lang="ts">
import { ref, computed } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'
import NavFooter from '@/components/NavFooter.vue'
import NavUser from '@/components/NavUser.vue'
import AppLogo from './AppLogo.vue'
import {
  Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem,
} from '@/components/ui/sidebar'
import {
  LayoutGrid, UserPlus, UserCog, CalendarClock, Stethoscope, MessageCircle,
  Receipt, ShieldCheck, PackageCheck, ClipboardList, Hospital, ArrowBigRight,
  Megaphone, Globe2, CalendarDays, Users, BookOpen, Folder, ChevronDown,
  ChevronRight, CalendarCheck, UserCheck, Settings,
} from 'lucide-vue-next'

const page = usePage()
const user = computed(() => page.props.auth.user)
const userRoles = computed(() => user.value?.roles || [])

// Helper function to check permissions
const can = (permission) => {
    if (!user.value) return false;
    // Super Admin bypasses all permission checks
    if (user.value.roles.includes('Super Admin')) return true;
    if (!user.value.permissions) return false;
    return user.value.permissions.includes(permission);
}

const isSuperAdmin = computed(() => userRoles.value.includes('Super Admin'))
const isAdmin = computed(() => userRoles.value.includes('Admin'))
const isStaff = computed(() => userRoles.value.includes('Staff'))

// This is the master list of ALL possible navigation links for Admins and Super Admins.
const allAdminNavItems = [
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
    group: 'Administrative Tools',
    icon: UserCog,
    items: [
      { title: 'Staff', routeName: 'admin.staff.index', icon: UserCog, permission: 'view staff' },
      { title: 'Staff Availability', routeName: 'admin.staff-availabilities.index', icon: CalendarCheck, permission: 'view staff' },
      // { title: 'Messages', routeName: 'admin.messages.index', icon: MessageCircle, permission: 'view messages' },
      // { title: 'Invoices', routeName: 'admin.invoices.index', icon: Receipt, permission: 'view invoices' },
      // { title: 'Insurance Claims', routeName: 'admin.insurance.index', icon: ShieldCheck, permission: 'view insurance' },
      // { title: 'Inventory Items', routeName: 'admin.inventory.index', icon: PackageCheck, permission: 'view inventory' },
      // { title: 'Admin Task Tracking', routeName: 'admin.tasks.index', icon: ClipboardList, permission: 'view tasks' },
    ],
  },
  {
    group: 'Integrations',
    icon: Globe2,
    items: [
      // { title: 'Partner Hospitals', routeName: 'admin.partners.index', icon: Hospital, permission: 'view partners' },
      // { title: 'Referrals', routeName: 'admin.referrals.index', icon: ArrowBigRight, permission: 'view referrals' },
      // { title: 'Marketing Campaigns', routeName: 'admin.marketing.index', icon: Megaphone, permission: 'view marketing' },
      // { title: 'International Referrals', routeName: 'admin.international.index', icon: Globe2, permission: 'view international' },
      // { title: 'Events', routeName: 'admin.events.index', icon: CalendarDays, permission: 'view events' },
      // { title: 'NGO Networks', routeName: 'admin.networks.index', icon: Users, permission: 'view networks' },
    ],
  },
   {
      group: 'System Management',
      icon: Settings,
      superAdminOnly: true, // This custom flag hides the group from regular Admins
      items: [
          { title: 'Role Management', routeName: 'admin.roles.index', icon: Users, permission: 'manage roles' },
          { title: 'User Management', routeName: 'admin.users.index', icon: UserCog, permission: 'manage users' },
      ]
  }
];

// This computed property intelligently builds the final navigation list based on the user's role.
const mainNavItems = computed(() => {
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
                    { title: 'My Availability', routeName: 'staff.my-availability.index', icon: UserCheck }
                ]
            }
        ];
    }
    return [];
});


const openGroups = ref({})
const toggleGroup = (groupName: string) => { openGroups.value[groupName] = !openGroups.value[groupName] }
const footerNavItems = [
  { title: 'Github Repo', href: 'https://github.com/laravel/vue-starter-kit', icon: Folder },
  { title: 'Documentation', href: 'https://laravel.com/docs/starter-kits#vue', icon: BookOpen },
]
const isSidebarCollapsed = ref(false);
</script>

<template>
  <Sidebar collapsible="icon" variant="inset" @update:collapsed="value => isSidebarCollapsed = value">
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
      <NavUser v-if="user" :is-collapsed="isSidebarCollapsed" />
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