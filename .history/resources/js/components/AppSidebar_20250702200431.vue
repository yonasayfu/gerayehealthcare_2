<script setup lang="ts">
import { ref, computed } from 'vue'
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

const isCollapsed = ref(false)
</script>

<template>
  <Sidebar collapsible="icon" variant="inset" @update:collapsed="isCollapsed = $event">
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
        <!-- Group Header -->
        <button
          @click="toggleGroup(group.group)"
          class="flex w-full items-center justify-between px-2 py-1 text-sm font-semibold text-muted-foreground hover:text-primary"
        >
          <span class="flex items-center gap-2 uppercase tracking-wide truncate">
            <component :is="group.icon" class="h-4 w-4 text-primary shrink-0" />
            <span v-show="!isCollapsed" class="truncate">{{ group.group }}</span>
          </span>
          <component v-if="!isCollapsed" :is="openGroups[group.group] ? ChevronDown : ChevronRight" class="h-4 w-4" />
        </button>

        <!-- Group Items -->
        <SidebarMenu v-if="openGroups[group.group] && !isCollapsed">
          <SidebarMenuItem v-for="item in group.items" :key="item.title">
            <SidebarMenuButton as-child>
              <Link :href="item.href" class="flex items-center gap-2 px-2 py-1 text-sm">
                <component :is="item.icon" class="h-4 w-4" />
                {{ item.title }}
              </Link>
            </SidebarMenuButton>
          </SidebarMenuItem>
        </SidebarMenu>
      </div>
    </SidebarContent>

    <SidebarFooter>
      <NavFooter :items="footerNavItems" />
      <NavUser />
    </SidebarFooter>
  </Sidebar>
  <slot />
</template>

<style scoped>
.truncate {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  display: inline-block;
  max-width: 100%;
}
</style>
