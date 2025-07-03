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
  { title: 'View', href: `/dashboard/staff/${props.staff.id}` },
]
</script>

<template>
  <Head title="View Staff" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <!-- Header -->
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Staff Details</h1>
        <p class="text-sm text-muted-foreground">Here you can see all the information about this staff member.</p>
      </div>

      <!-- Staff Info -->
      <div class="rounded-lg border border-border bg-white dark:bg-background p-6 shadow-sm grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <strong>First Name:</strong>
          <p>{{ staff.first_name }}</p>
        </div>
        <div>
          <strong>Last Name:</strong>
          <p>{{ staff.last_name }}</p>
        </div>
        <div>
          <strong>Email:</strong>
          <p>{{ staff.email ?? '-' }}</p>
        </div>
        <div>
          <strong>Phone:</strong>
          <p>{{ staff.phone ?? '-' }}</p>
        </div>
        <div>
          <strong>Position:</strong>
          <p>{{ staff.position ?? '-' }}</p>
        </div>
        <div>
          <strong>Department:</strong>
          <p>{{ staff.department ?? '-' }}</p>
        </div>
        <div>
          <strong>Status:</strong>
          <p>{{ staff.status ?? '-' }}</p>
        </div>
        <div>
          <strong>Hire Date:</strong>
          <p>{{ staff.hire_date ?? '-' }}</p>
        </div>
        <div class="md:col-span-2">
          <strong>Photo:</strong>
          <div v-if="staff.photo" class="mt-2">
            <img :src="'/' + staff.photo" alt="Staff Photo" class="h-24 rounded-md shadow" />
          </div>
          <p v-else>-</p>
        </div>
      </div>

      <!-- Back Button -->
      <div class="flex justify-end">
        <Link
          href="/dashboard/staff"
          class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-muted/30 transition"
        >
          Back to Staff List
        </Link>
      </div>
    </div>
  </AppLayout>
</template>
