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
    <div class="max-w-3xl mx-auto p-6 bg-white dark:bg-background rounded-md shadow print:bg-white print:shadow-none print:text-black">
      
      <!-- Avatar and Name -->
      <div class="text-center mb-6">
        <img
          v-if="staff.photo"
          :src="'/' + staff.photo"
          alt="Staff Photo"
          class="mx-auto h-28 w-28 rounded-full object-cover shadow mb-4 border border-gray-300"
        />
        <div v-else class="mx-auto h-28 w-28 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 text-xl font-semibold mb-4">
          {{ staff.first_name[0] }}{{ staff.last_name[0] }}
        </div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
          {{ staff.first_name }} {{ staff.last_name }}
        </h1>
        <p class="text-gray-500 text-sm">Staff Profile Report</p>
      </div>

      <!-- Staff Details -->
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
          <span class="font-medium">Position:</span>
          <p>{{ staff.position ?? '-' }}</p>
        </div>
        <div>
          <span class="font-medium">Department:</span>
          <p>{{ staff.department ?? '-' }}</p>
        </div>
        <div>
          <span class="font-medium">Status:</span>
          <p>{{ staff.status ?? '-' }}</p>
        </div>
        <div>
          <span class="font-medium">Hire Date:</span>
          <p>{{ staff.hire_date ?? '-' }}</p>
        </div>
      </div>

      <!-- Footer -->
      <div class="mt-8 flex justify-between print:hidden">
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

  .print\:bg-white {
    background-color: white !important;
  }

  .print\:text-black {
    color: black !important;
  }

  .print\:shadow-none {
    box-shadow: none !important;
  }

  .print\:hidden {
    display: none !important;
  }

  .print\:block {
    display: block !important;
  }

  .max-w-3xl {
    max-width: 700px;
  }

  .mx-auto {
    margin: 0 auto;
  }
}
</style>
