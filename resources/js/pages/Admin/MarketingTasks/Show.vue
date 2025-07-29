<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { format } from 'date-fns'

interface MarketingTask {
  id: number;
  task_code: string;
  title: string;
  description: string;
  task_type: string;
  status: string;
  scheduled_at: string;
  completed_at: string;
  notes: string;
  campaign: { campaign_name: string };
  assigned_to_staff: { full_name: string };
  related_content: { title: string };
  doctor: { full_name: string };
  created_at: string;
  updated_at: string;
}

const props = defineProps<{
  marketingTask: MarketingTask;
}>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Marketing Tasks', href: route('admin.marketing-tasks.index') },
  { title: props.marketingTask.title, href: route('admin.marketing-tasks.show', props.marketingTask.id) },
]
</script>

<template>
  <Head :title="marketingTask.title" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">{{ marketingTask.title }}</h1>
        <p class="text-sm text-muted-foreground">Details of the marketing task.</p>
      </div>

      <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Task Code:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingTask.task_code }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Campaign:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingTask.campaign?.campaign_name ?? '-' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Assigned To:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingTask.assigned_to_staff?.full_name ?? '-' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Related Content:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingTask.related_content?.title ?? '-' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Doctor (if applicable):</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingTask.doctor?.full_name ?? '-' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Task Type:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingTask.task_type ?? '-' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Status:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingTask.status ?? '-' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Scheduled At:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingTask.scheduled_at ? format(new Date(marketingTask.scheduled_at), 'PPP p') : '-' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Completed At:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingTask.completed_at ? format(new Date(marketingTask.completed_at), 'PPP p') : '-' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Created At:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ format(new Date(marketingTask.created_at), 'PPP p') }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Updated At:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ format(new Date(marketingTask.updated_at), 'PPP p') }}</p>
          </div>
        </div>

        <div class="mt-6">
          <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Description:</p>
          <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingTask.description ?? '-' }}</p>
        </div>

        <div class="mt-6">
          <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Notes:</p>
          <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingTask.notes ?? '-' }}</p>
        </div>

        <div class="mt-6 flex justify-end">
          <Link :href="route('admin.marketing-tasks.edit', marketingTask.id)" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
            Edit Task
          </Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
