<script setup lang="ts">
import { ref } from 'vue'
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
  MessageCircle,
  Receipt,
  ShieldCheck,
  PackageCheck,
  ClipboardList,
  Hospital,
  ArrowBigRight,
  Megaphone,
  Globe2,
  CalendarDays,
  Users,
  BookOpen,
  Folder,
  ChevronDown,
  ChevronRight,
  CalendarCheck,
} from 'lucide-vue-next'

const mainNavItems = [
  {
    group: 'Patient Management',
    icon: UserPlus,
    items: [
      { title: 'Dashboard', routeName: 'dashboard', icon: LayoutGrid },
      { title: 'Patients', routeName: 'patients.index', icon: UserPlus },
      { title: 'Caregiver Assignments', routeName: 'assignments.index', icon: CalendarClock },
      // { title: 'Visit Services', routeName: 'visits.index', icon: Stethoscope }, // Assuming this route exists
    ],
  },
  {
    group: 'Administrative Tools',
    icon: UserCog,
    items: [
      { title: 'Staff', routeName: 'staff.index', icon: UserCog },
      { title: 'Staff Availability', routeName: 'staff-availabilities.index', icon: CalendarCheck },
      // { title: 'Messages', routeName: 'messages.index', icon: MessageCircle },
      // { title: 'Invoices', routeName: 'invoices.index', icon: Receipt },
      // { title: 'Insurance Claims', routeName: 'insurance.index', icon: ShieldCheck },
      // { title: 'Inventory Items', routeName: 'inventory.index', icon: PackageCheck },
      // { title: 'Admin Task Tracking', routeName: 'tasks.index', icon: ClipboardList },
    ],
  },
  {
    group: 'Integrations',
    icon: Globe2,
    items: [
      // { title: 'Partner Hospitals', routeName: 'partners.index', icon: Hospital },
      // { title: 'Referrals', routeName: 'referrals.index', icon: ArrowBigRight },
      // { title: 'Marketing Campaigns', routeName: 'marketing.index', icon: Megaphone },
      // { title: 'International Referrals', routeName: 'international.index', icon: Globe2 },
      // { title: 'Events', routeName: 'events.index', icon: CalendarDays },
      // { title: 'NGO Networks', routeName: 'networks.index', icon: Users },
    ],
  },
]

const openGroups = ref(Object.fromEntries(mainNavItems.map(group => [group.group, true])))

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

// Reactive variable to track the sidebar's collapsed state
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
          <component :is="openGroups[group.group] ? ChevronDown : ChevronRight" class="h-4 w-4" />
        </SidebarMenuButton>

        <SidebarMenu v-if="openGroups[group.group]">
          <SidebarMenuItem v-for="item in group.items" :key="item.title">
            <SidebarMenuButton as-child>
              <!-- Use route() helper for all links -->
              <Link :href="route(item.routeName)" class="flex items-center gap-2 px-2 py-1 text-sm">
                <component :is="item.icon" class="h-4 w-4" />
                <span v-if="!isSidebarCollapsed">{{ item.title }}</span>
              </Link>
            </SidebarMenuButton>
          </SidebarMenuItem>
        </SidebarMenu>
      </div>
    </SidebarContent>

    <SidebarFooter>
      <NavFooter :items="footerNavItems" :is-collapsed="isSidebarCollapsed" />
      <NavUser :is-collapsed="isSidebarCollapsed" />
    </SidebarFooter>
  </Sidebar>
  <slot />
</template>

<style scoped>
/*
  Tailwind's `truncate` utility is good for single-line text overflow with ellipsis.
  It won't make a container scrollable on its own.
*/
.truncate {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

/* Custom scrollbar styles (optional, for better aesthetics) */
.custom-scrollbar::-webkit-scrollbar {
  width: 18px; /* Width of the scrollbar */
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent; /* Track color */
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: #cbd5e1; /* Thumb color (light gray for example) */
  border-radius: 4px; /* Rounded corners for the thumb */
  border: 2px solid transparent; /* Padding around the thumb */
  background-clip: content-box; /* Ensures padding is transparent */
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background-color: #94a3b8; /* Darker thumb on hover */
}

/* For Firefox */
.custom-scrollbar {
  scrollbar-width: thin; /* "auto" or "thin" */
  scrollbar-color: #cbd5e1 transparent; /* thumb color track color */
}
</style>