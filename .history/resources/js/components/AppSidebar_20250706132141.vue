<script setup lang="ts">
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import NavFooter from '@/components/NavFooter.vue'
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
import { Link } from '@inertiajs/vue3'

import {
  LayoutGrid, UserPlus, UserCog, CalendarClock, Stethoscope, MessageCircle,
  Receipt, ShieldCheck, PackageCheck, ClipboardList, Hospital, ArrowBigRight,
  Megaphone, Globe2, CalendarDays, Users, BookOpen, Folder, ChevronDown,
  ChevronRight, CalendarCheck, UserCheck, Settings,
} from 'lucide-vue-next'

const page = usePage()
const user = computed(() => page.props.auth.user)
const userRoles = computed(() => user.value?.roles || [])

const isSuperAdmin = computed(() => userRoles.value.includes('Super Admin'))
const isAdmin = computed(() => userRoles.value.includes('Admin'))
const isStaff = computed(() => userRoles.value.includes('Staff'))

// --- Navigation Items Definition ---

// This is the master list of ALL possible navigation links for Admins.
// Links for modules we have not built yet are commented out to prevent errors.
const allAdminNavItems = [
  {
    group: 'Patient Management',
    icon: UserPlus,
    items: [
      { title: 'Dashboard', routeName: 'dashboard', icon: LayoutGrid },
      { title: 'Patients', routeName: 'admin.patients.index', icon: UserPlus },
      { title: 'Caregiver Assignments', routeName: 'admin.assignments.index', icon: CalendarClock },
      // { title: 'Visit Services', routeName: 'admin.visits.index', icon: Stethoscope },
    ],
  },
  {
    group: 'Administrative Tools',
    icon: UserCog,
    items: [
      { title: 'Staff', routeName: 'admin.staff.index', icon: UserCog },
      { title: 'Staff Availability', routeName: 'admin.staff-availabilities.index', icon: CalendarCheck },
       { title: 'Messages', routeName: 'admin.messages.index', icon: MessageCircle },
       { title: 'Invoices', routeName: 'admin.invoices.index', icon: Receipt },
       { title: 'Insurance Claims', routeName: 'admin.insurance.index', icon: ShieldCheck },
       { title: 'Inventory Items', routeName: 'admin.inventory.index', icon: PackageCheck },
      // { title: 'Admin Task Tracking', routeName: 'admin.tasks.index', icon: ClipboardList },
    ],
  },
  {
    group: 'Integrations',
    icon: Globe2,
    items: [
       { title: 'Partner Hospitals', routeName: 'admin.partners.index', icon: Hospital },
       { title: 'Referrals', routeName: 'admin.referrals.index', icon: ArrowBigRight },
       { title: 'Marketing Campaigns', routeName: 'admin.marketing.index', icon: Megaphone },
       { title: 'International Referrals', routeName: 'admin.international.index', icon: Globe2 },
       { title: 'Events', routeName: 'admin.events.index', icon: CalendarDays },
       { title: 'NGO Networks', routeName: 'admin.networks.index', icon: Users },
    ],
  },
   {
      group: 'System Management',
      icon: Settings,
      superAdminOnly: true, // This is a custom flag to hide this from regular Admins
      items: [
           { title: 'Role Management', routeName: 'admin.roles.index', icon: Users },
      ]
  }
];

// This computed property intelligently builds the final navigation list based on the user's role.
const mainNavItems = computed(() => {
    if (isSuperAdmin.value) {
        // Super Admin sees everything.
        return allAdminNavItems;
    }
    if (isAdmin.value) {
        // Regular Admin sees everything EXCEPT groups marked as superAdminOnly.
        return allAdminNavItems.filter(group => !group.superAdminOnly);
    }
    if (isStaff.value) {
        // Staff members see a completely different, limited set of links.
        return [
            {
                group: 'My Tools',
                icon: UserCheck,
                items: [
                    { title: 'Dashboard', routeName: 'dashboard', icon: LayoutGrid },
                    { title: 'My Availability', routeName: 'staff.my-availability.index', icon: UserCheck }
                ]
            }
        ];
    }
    return []; // If no role, show nothing.
});


const openGroups = ref({})

const toggleGroup = (groupName: string) => {
  openGroups.value[groupName] = !openGroups.value[groupName]
}

const footerNavItems = [
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
]

const isSidebarCollapsed = ref(false);

</script>

<template>
  <Sidebar collapsible="icon" variant="inset" @update:collapsed="value => isSidebarCollapsed = value">
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

    <SidebarContent class="flex-grow overflow-y-auto custom-scrollbar">
      <div v-if="user">
        <div
          v-for="group in mainNavItems"
          :key="group.group"
          >
          <SidebarMenuButton
            @click="toggleGroup(group.group)"
            class="flex w-full items-center justify-between px-2 py-1 text-sm font-semibold text-muted-foreground hover:text-primary"
          >
            <span class="flex items-center gap-2 uppercase tracking-wide">
              <component :is="group.icon" class="h-4 w-4 text-primary" />
              <span v-if="!isSidebarCollapsed" class="truncate">{{ group.group }}</span>
            </span>
            <component :is="openGroups[group.group] !== false ? ChevronDown : ChevronRight" class="h-4 w-4" />
          </SidebarMenuButton>

          <SidebarMenu v-if="openGroups[group.group] !== false">
            <SidebarMenuItem v-for="item in group.items" :key="item.title">
              <SidebarMenuButton as-child>
                <!-- This now safely handles links that might be commented out -->
                <Link :href="item.routeName ? route(item.routeName) : '#'" class="flex items-center gap-2 px-2 py-1 text-sm">
                  <component :is="item.icon" class="h-4 w-4" />
                  <span v-if="!isSidebarCollapsed">{{ item.title }}</span>
                </Link>
              </SidebarMenuButton>
            </SidebarMenuItem>
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
.truncate {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.custom-scrollbar::-webkit-scrollbar { width: 18px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 4px; border: 2px solid transparent; background-clip: content-box; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background-color: #94a3b8; }
.custom-scrollbar { scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent; }
</style>
