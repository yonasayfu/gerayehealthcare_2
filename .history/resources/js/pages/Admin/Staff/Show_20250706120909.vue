<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItemType } from '@/types'

const props = defineProps<{
  staff: {
    id: number
    first_name: string
    last_name: string
    email: string
    phone: string | null
    position: string | null
    department: string | null
    status: 'Active' | 'Inactive'
    hire_date: string | null
    photo: string | null
    created_at: string
    updated_at: string
  }
}>()

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Staff', href: '/dashboard/staff' },
  { title: 'View', href: `/dashboard/staff/${props.staff.id}` },
]
</script>

<template>
  <Head :title="`Staff: ${props.staff.first_name} ${props.staff.last_name}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm flex items-center justify-between">
        <div>
          <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Staff Details</h1>
          <p class="text-sm text-muted-foreground">View comprehensive information about this staff member.</p>
        </div>
        <Link :href="route('admin.staff.edit', props.staff.id)"
          class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-md transition">
          Edit Staff
        </Link>
      </div>

      <div class="rounded-lg border border-border bg-white dark:bg-background p-6 shadow-sm space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="col-span-1 md:col-span-2 flex justify-center items-center py-4">
            <div v-if="staff.photo" class="relative w-40 h-40 rounded-full overflow-hidden border-2 border-gray-200">
              <img :src="'/storage/' + staff.photo" alt="Staff Photo" class="w-full h-full object-cover" />
            </div>
            <div v-else class="w-40 h-40 flex items-center justify-center rounded-full bg-gray-200 text-gray-500 text-sm">
              No Photo
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">First Name:</label>
            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ staff.first_name }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Last Name:</label>
            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ staff.last_name }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email:</label>
            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ staff.email ?? '-' }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone:</label>
            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ staff.phone ?? '-' }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Position:</label>
            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ staff.position ?? '-' }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Department:</label>
            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ staff.department ?? '-' }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status:</label>
            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ staff.status ?? '-' }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Hire Date:</label>
            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ staff.hire_date ?? '-' }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Created At:</label>
            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ new Date(staff.created_at).toLocaleString() }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Last Updated:</label>
            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ new Date(staff.updated_at).toLocaleString() }}</p>
          </div>
        </div>

        <div class="flex justify-end mt-6">
          <Link :href="route('adminstaff.index')"
            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-muted/30 transition">
            Back to Staff List
          </Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>