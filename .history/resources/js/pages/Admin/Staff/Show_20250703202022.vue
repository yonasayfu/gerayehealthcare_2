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
  { title: `${props.staff.first_name} ${props.staff.last_name}`, href: `/dashboard/staff/${props.staff.id}` },
]
</script>

<template>
  <Head :title="`${props.staff.first_name} ${props.staff.last_name}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <div class="bg-white dark:bg-gray-900 rounded-lg shadow-md p-6 flex flex-col md:flex-row items-center gap-6">
        <div class="flex-shrink-0">
          <img
            :src="props.staff.photo ? '/' + props.staff.photo : '/images/default-profile.png'"
            alt="Staff Photo"
            class="w-40 h-40 rounded-full object-cover border-4 border-gray-300"
          />
        </div>
        <div class="flex-1">
          <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
            {{ props.staff.first_name }} {{ props.staff.last_name }}
          </h1>
          <p class="text-sm text-gray-500 dark:text-gray-300 mb-4">
            {{ props.staff.position ?? 'Position Not Specified' }}
          </p>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-2">
            <p><strong>Email:</strong> {{ props.staff.email ?? 'N/A' }}</p>
            <p><strong>Phone:</strong> {{ props.staff.phone ?? 'N/A' }}</p>
            <p><strong>Department:</strong> {{ props.staff.department ?? 'N/A' }}</p>
            <p><strong>Status:</strong> {{ props.staff.status ?? 'N/A' }}</p>
            <p><strong>Hire Date:</strong> {{ props.staff.hire_date ?? 'N/A' }}</p>
          </div>
        </div>
      </div>

      <div class="flex justify-end">
        <Link
          :href="`/dashboard/staff/${props.staff.id}/edit`"
          class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm"
        >
          Edit Profile
        </Link>
      </div>
    </div>
  </AppLayout>
</template>
