<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { format } from 'date-fns'

interface CampaignContent {
  id: number;
  title: string;
  description: string;
  media_url: string;
  content_type: string;
  status: string;
  scheduled_post_date: string;
  actual_post_date: string;
  engagement_metrics: Record<string, any>;
  campaign: { campaign_name: string };
  platform: { name: string };
  created_at: string;
  updated_at: string;
}

const props = defineProps<{
  campaignContent: CampaignContent;
}>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Campaign Contents', href: route('admin.campaign-contents.index') },
  { title: props.campaignContent.title, href: route('admin.campaign-contents.show', props.campaignContent.id) },
]

const formatJson = (json: Record<string, any>) => {
  return JSON.stringify(json, null, 2);
};
</script>

<template>
  <Head :title="campaignContent.title" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">{{ campaignContent.title }}</h1>
        <p class="text-sm text-muted-foreground">Details of the campaign content.</p>
      </div>

      <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Title:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ campaignContent.title }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Campaign:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ campaignContent.campaign?.campaign_name ?? '-' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Platform:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ campaignContent.platform?.name ?? '-' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Content Type:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ campaignContent.content_type ?? '-' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Status:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ campaignContent.status ?? '-' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Scheduled Post Date:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ campaignContent.scheduled_post_date ? format(new Date(campaignContent.scheduled_post_date), 'PPP p') : '-' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Actual Post Date:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ campaignContent.actual_post_date ? format(new Date(campaignContent.actual_post_date), 'PPP p') : '-' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Media URL:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white"><a :href="campaignContent.media_url" target="_blank" class="text-cyan-600 hover:underline">{{ campaignContent.media_url ?? '-' }}</a></p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Created At:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ format(new Date(campaignContent.created_at), 'PPP p') }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Updated At:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ format(new Date(campaignContent.updated_at), 'PPP p') }}</p>
          </div>
        </div>

        <div class="mt-6">
          <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Description:</p>
          <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ campaignContent.description ?? '-' }}</p>
        </div>

        <div class="mt-6">
          <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Engagement Metrics:</p>
          <pre class="mt-1 text-lg text-gray-900 dark:text-white bg-gray-100 dark:bg-gray-800 p-4 rounded-md overflow-auto">{{ formatJson(campaignContent.engagement_metrics) }}</pre>
        </div>

        <div class="mt-6 flex justify-end">
          <Link :href="route('admin.campaign-contents.edit', campaignContent.id)" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
            Edit Content
          </Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
