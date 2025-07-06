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
  LayoutGrid,
  UserPlus,
  UserCog,
  CalendarClock,
  Stethoscope,
  ShieldCheck,
  ClipboardList,
  Hospital,
  Megaphone,
  Globe2,
  CalendarDays,
  Users,
  BookOpen,
  Folder,
  ChevronDown,
  ChevronRight,
  CalendarCheck,
  UserCheck,
  BarChart3,
  Settings,
} from 'lucide-vue-next'

const page = usePage()
const user = computed(() => page.props.auth.user)
const userRoles = computed(() => user.value?.roles || [])

const isSuperAdmin = computed(() => userRoles.value.includes('Super Admin'))
const isAdmin = computed(() => userRoles.value.includes('Admin'))
const isStaff = computed(() => userRoles.value.includes('Staff'))

const baseAdminNavItems = [
  {
    group: 'Patient Management',
    icon: UserPlus,
    items: [
      { title: 'Dashboard', routeName: 'dashboard', icon: LayoutGrid },
      { title: 'Patients', routeName: 'patients.index', icon: UserPlus },
      { title: 'Caregiver Assignments', routeName: 'assignments.index', icon: CalendarClock },
    ],
  },
  {
    group: 'Administrative Tools',
    icon: UserCog,
    items: [
      { title: 'Staff', routeName: 'staff.index', icon: UserCog },
      { title: 'Staff Availability', routeName: 'staff-availabilities.index', icon: CalendarCheck },
    ],
  },
];

const superAdminOnlyNavItems = [
  {
      group: 'System Management',
      icon: Settings,
      items: [
          // { title: 'Role Management', routeName: 'roles.index', icon: Users },
          // { title: 'Reporting', routeName: 'reporting.index', icon: BarChart3 },
      ]
  }
];

const mainNavItems = computed(() => {
    if (isSuperAdmin.value) {
        return [...baseAdminNavItems, ...superAdminOnlyNavItems];
    }
    if (isAdmin.value) {
        return baseAdminNavItems;
    }
    if (isStaff.value) {
        return [
            {
                group: 'My Tools',
                icon: UserCheck,
                items: [
                    { title: 'Dashboard', routeName: 'dashboard', icon: LayoutGrid },
                    { title: 'My Availability', routeName: 'my-availability.index', icon: UserCheck }
                ]
            }
        ];
    }
    return [];
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
      <!-- This single block now intelligently renders the correct links based on the user's role -->
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
                <Link :href="route(item.routeName)" class="flex items-center gap-2 px-2 py-1 text-sm">
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
      <!-- This v-if is the fix. It prevents NavUser from rendering if the user is not logged in. -->
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