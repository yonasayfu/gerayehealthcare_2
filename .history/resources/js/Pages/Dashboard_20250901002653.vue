<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, usePage } from '@inertiajs/vue3'
import { type BreadcrumbItem } from '@/types'
import { computed } from 'vue'

// Icons (you can change to better match your UI later)
import { FileText, ClipboardList, CalendarDays } from 'lucide-vue-next'

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/dashboard' },
]

const page = usePage();
const userRoles = computed(() => (page.props as any)?.auth?.user?.roles || []);
const isStaff = computed(() => userRoles.value.includes('Staff'));
</script>

<template>
  <Head title="Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex-1 space-y-4 p-4 md:p-6">
      <!-- Dashboard Widgets -->
      <div class="grid gap-4 md:grid-cols-3">
        <!-- Widget 1: Recent Invoices -->
        <div class="rounded-xl border border-border bg-white p-4 shadow-sm dark:border-sidebar-border dark:bg-background">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <FileText class="h-5 w-5 text-primary" />
              <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Recent Invoices</h2>
            </div>
            <span class="text-sm text-muted-foreground">12</span>
          </div>
          <p class="mt-2 text-xs text-muted-foreground">Last 7 days</p>
        </div>

        <!-- Widget 2: Pending Admin Tasks -->
        <div class="rounded-xl border border-border bg-white p-4 shadow-sm dark:border-sidebar-border dark:bg-background">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <ClipboardList class="h-5 w-5 text-primary" />
              <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Pending Admin Tasks</h2>
            </div>
            <span class="text-sm text-muted-foreground">6</span>
          </div>
          <p class="mt-2 text-xs text-muted-foreground">Awaiting staff action</p>
        </div>

        <!-- Widget 3: Upcoming Events -->
        <div class="rounded-xl border border-border bg-white p-4 shadow-sm dark:border-sidebar-border dark:bg-background">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <CalendarDays class="h-5 w-5 text-primary" />
              <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Upcoming Events</h2>
            </div>
            <span class="text-sm text-muted-foreground">3</span>
          </div>
          <p class="mt-2 text-xs text-muted-foreground">Next 14 days</p>
        </div>
      </div>

      <!-- Main Content Placeholder -->
      <div v-if="!isStaff" class="rounded-xl border border-border bg-white p-8 text-center text-muted-foreground shadow-sm dark:border-sidebar-border dark:bg-background">
        Welcome to your admin dashboard. Select a section from the sidebar to begin.
      </div>
      <div v-else class="rounded-xl border border-border bg-white p-8 text-center text-muted-foreground shadow-sm dark:border-sidebar-border dark:bg-background">
        Welcome to your staff dashboard. Select a section from the sidebar to begin.
      </div>
    </div>
  </AppLayout>
</template>
