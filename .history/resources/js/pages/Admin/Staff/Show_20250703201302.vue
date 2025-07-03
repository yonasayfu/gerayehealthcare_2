<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItemType } from '@/types'

const props = defineProps<{
  staff: {
    id: number
    first_name: string
    last_name: string
    email: string | null
    phone: string | null
    position: string | null
    department: string | null
    status: string | null
    hire_date: string | null
    photo: string | null
  }
}>()

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Staff', href: '/dashboard/staff' },
  { title: 'Profile', href: `/dashboard/staff/${props.staff.id}` },
]
</script>

<template>
  <Head title="Staff Profile" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="mx-auto max-w-screen-lg p-8 bg-white dark:bg-background rounded-md shadow print:bg-white print:shadow-none print:text-black landscape-layout">

      <!-- Profile Header -->
      <div class="flex items-start gap-6 border-b pb-6 mb-6">
        <img
          v-if="staff.photo"
          :src="'/' + staff.photo"
          alt="Staff Photo"
          class="h-36 w-36 rounded-full object-cover border border-gray-300 shadow"
        />
        <div class="flex-1">
          <h1 class="text-3xl font-bold text-gray-800 dark:text-white">
            {{ staff.first_name }} {{ staff.last_name }}
          </h1>
          <p class="text-sm text-gray-500 mt-1">Staff Profile Report</p>
          <div class="mt-4 space-y-1 text-sm text-gray-700 dark:text-gray-200">
            <p><strong>Position:</strong> {{ staff.position ?? '-' }}</p>
            <p><strong>Department:</strong> {{ staff.department ?? '-' }}</p>
            <p><strong>Status:</strong> {{ staff.status ?? '-' }}</p>
          </div>
        </div>
      </div>

      <!-- Details Section -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-gray-700 dark:text-gray-200">
        <div>
          <span class="font-medium">Email:</span>
          <p>{{ staff.email ?? '-' }}</p>
        </div>
        <div>
          <span class="font-medium">Phone:</span>
          <p>{{ staff.phone ?? '-' }}</p>
        </div>
        <div>
          <span class="font-medium">Hire Date:</span>
          <p>{{ staff.hire_date ?? '-' }}</p>
        </div>
      </div>

      <!-- Footer Actions -->
      <div class="mt-10 flex justify-between print:hidden">
        <Link
          href="/dashboard/staff"
          class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-muted/30 transition"
        >
          Back to Staff List
        </Link>
        <button
          @click="() => window.print()"
          class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition"
        >
          Print
        </button>
      </div>
    </div>
  </AppLayout>
</template>

<style>
@media print {
  body * {
    visibility: hidden !important;
  }

  .landscape-layout,
  .landscape-layout * {
    visibility: visible !important;
  }

  .landscape-layout {
    position: absolute !important;
    top: 0;
    left: 0;
    width: 100%;
    padding: 30px;
    font-size: 12px;
  }

  .print\:hidden {
    display: none !important;
  }
}
</style>
