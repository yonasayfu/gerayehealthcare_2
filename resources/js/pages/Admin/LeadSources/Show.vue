<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { format } from 'date-fns'

interface LeadSource {
  id: number;
  name: string;
  category: string;
  description: string;
  is_active: boolean;
  created_at: string;
  updated_at: string;
}

const props = defineProps<{
  leadSource: LeadSource;
}>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Lead Sources', href: route('admin.lead-sources.index') },
  { title: props.leadSource.name, href: route('admin.lead-sources.show', props.leadSource.id) },
]
</script>

<template>
  <Head :title="leadSource.name" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">{{ leadSource.name }}</h1>
        <p class="text-sm text-muted-foreground">Details of the lead source.</p>
      </div>

      <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Source Name:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ leadSource.name }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Category:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ leadSource.category ?? '-' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Is Active:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ leadSource.is_active ? 'Yes' : 'No' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Created At:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ format(new Date(leadSource.created_at), 'PPP p') }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Updated At:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ format(new Date(leadSource.updated_at), 'PPP p') }}</p>
          </div>
        </div>

        <div class="mt-6">
          <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Description:</p>
          <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ leadSource.description ?? '-' }}</p>
        </div>

        <div class="mt-6 flex justify-end">
          <Link :href="route('admin.lead-sources.edit', leadSource.id)" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
            Edit Lead Source
          </Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
