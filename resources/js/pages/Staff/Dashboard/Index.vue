<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, usePage } from '@inertiajs/vue3'
import type { BreadcrumbItemType } from '@/types'
import { computed } from 'vue'
import { FileText, ClipboardList, CalendarDays } from 'lucide-vue-next'

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
]

const page = usePage()
const userRoles = computed(() => ((page.props as any)?.auth?.user?.roles || []).map((r: string) => r.toLowerCase()))
const isStaff = computed(() => userRoles.value.includes('staff'))
</script>

<template>
  <Head title="Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex-1 space-y-4 p-4 md:p-6">
      <div class="grid gap-4 md:grid-cols-3">
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

      <!-- Staff specific section (placeholder) -->
      <div v-if="isStaff" class="rounded-xl border border-border bg-white p-4 shadow-sm dark:border-sidebar-border dark:bg-background">
        <h2 class="text-sm font-semibold text-gray-800 dark:text-white">My Upcoming Visits</h2>
        <p class="mt-2 text-xs text-muted-foreground">This section shows your next visits.</p>
      </div>
    </div>
  </AppLayout>
</template>

