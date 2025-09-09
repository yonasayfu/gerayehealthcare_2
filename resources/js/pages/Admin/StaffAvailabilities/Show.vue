<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import ShowHeader from '@/components/ShowHeader.vue'
import { format } from 'date-fns'

const props = defineProps<{ staffAvailability: any }>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Staff Availabilities', href: route('admin.staff-availabilities.index') },
  { title: `Availability #${props.staffAvailability.id}`, href: route('admin.staff-availabilities.show', props.staffAvailability.id) },
]
</script>

<template>
  <Head :title="`Availability #${props.staffAvailability.id}`" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="m-10 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow">
      <ShowHeader title="Staff Availability Details" :subtitle="`Availability #${props.staffAvailability.id}`">
        <template #actions>
          <Link :href="route('admin.staff-availabilities.index')" class="btn-glass btn-glass-sm">Back</Link>
        </template>
      </ShowHeader>

      <div class="p-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div>
            <p class="text-sm text-muted-foreground">Staff</p>
            <p class="font-medium">{{ props.staffAvailability.staff?.first_name }} {{ props.staffAvailability.staff?.last_name }}</p>
          </div>
          <div>
            <p class="text-sm text-muted-foreground">Start Time</p>
            <p class="font-medium">{{ props.staffAvailability.start_time ? format(new Date(props.staffAvailability.start_time), 'PPP p') : '-' }}</p>
          </div>
          <div>
            <p class="text-sm text-muted-foreground">End Time</p>
            <p class="font-medium">{{ props.staffAvailability.end_time ? format(new Date(props.staffAvailability.end_time), 'PPP p') : '-' }}</p>
          </div>
          <div>
            <p class="text-sm text-muted-foreground">Status</p>
            <p class="font-medium">{{ props.staffAvailability.status }}</p>
          </div>
        </div>
      </div>

      <div class="p-6 border-t border-gray-200 dark:border-gray-700 rounded-b print:hidden">
        <div class="flex justify-end gap-2">
          <Link :href="route('admin.staff-availabilities.index')" class="btn-glass btn-glass-sm">Back to List</Link>
          <Link :href="route('admin.staff-availabilities.edit', props.staffAvailability.id)" class="btn-glass btn-glass-sm">Edit</Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

