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
  // Assuming Sidebar also exposes its collapsed state, or we can derive it
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
} from 'lucide-vue-next'

const mainNavItems = [
  {
    group: 'Patient Management',
    icon: UserPlus,
    items: [
      { title: 'Dashboard', href: '/dashboard', icon: LayoutGrid },
      { title: 'Patients', href: '/dashboard/patients', icon: UserPlus },
      { title: 'Caregiver Assignments', href: '/dashboard/assignments', icon: CalendarClock },
      { title: 'Visit Services', href: '/dashboard/visits', icon: Stethoscope },
    ],
  },
  {
    group: 'Administrative Tools',
    icon: UserCog,
    items: [
      { title: 'Staff', href: '/dashboard/staff', icon: UserCog },
      { title: 'Messages', href: '/dashboard/messages', icon: MessageCircle },
      { title: 'Invoices', href: '/dashboard/invoices', icon: Receipt },
      { title: 'Insurance Claims', href: '/dashboard/insurance', icon: ShieldCheck },
      { title: 'Inventory Items', href: '/dashboard/inventory', icon: PackageCheck },
      { title: 'Admin Task Tracking', href: '/dashboard/tasks', icon: ClipboardList },
    ],
  },
  {
    group: 'Integrations',
    icon: Globe2,
    items: [
      { title: 'Partner Hospitals', href: '/dashboard/partners', icon: Hospital },
      { title: 'Referrals', href: '/dashboard/referrals', icon: ArrowBigRight },
      { title: 'Marketing Campaigns', href: '/dashboard/marketing', icon: Megaphone },
      { title: 'International Referrals', href: '/dashboard/international', icon: Globe2 },
      { title: 'Events', href: '/dashboard/events', icon: CalendarDays },
      { title: 'NGO Networks', href: '/dashboard/networks', icon: Users },
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
// We'll pass this down or update it based on the Sidebar component's behavior.
// For now, let's assume Sidebar provides an update:collapsed event or a prop like 'is-collapsed'.
// If not, you might need to manage this with a CSS media query for fixed breakpoint collapses.
const isSidebarCollapsed = ref(false);

// If the Sidebar component itself emits a "collapsed" event or provides a prop,
// you can update isSidebarCollapsed here.
// Example (pseudo-code, depends on your actual Sidebar component):
// <Sidebar @update:collapsed="value => isSidebarCollapsed = value" collapsible="icon" ...>
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

    <SidebarContent>
      <div
        v-for="group in mainNavItems"
        :key="group.group"
        class="mb-4 rounded-md bg-muted/40 px-2 pt-2 pb-1 overflow-hidden"
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
              <Link :href="item.href" class="flex items-center gap-2 px-2 py-1 text-sm">
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
  The 'truncate' class might still be useful for general text overflow
  within the expanded sidebar, but the primary hiding/showing should be
  done via Vue's conditional rendering or the UI component's internal logic.
*/
.truncate {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
</style>