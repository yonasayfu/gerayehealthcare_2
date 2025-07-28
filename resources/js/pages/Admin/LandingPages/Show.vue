<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { format } from 'date-fns'

interface LandingPage {
  id: number;
  page_code: string;
  page_title: string;
  page_url: string;
  template_used: string;
  language: string;
  form_fields: Record<string, any>;
  conversion_goal: string;
  views: number;
  submissions: number;
  conversion_rate: number;
  is_active: boolean;
  campaign: { campaign_name: string };
  created_at: string;
  updated_at: string;
}

const props = defineProps<{
  landingPage: LandingPage;
}>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Landing Pages', href: route('admin.landing-pages.index') },
  { title: props.landingPage.page_title, href: route('admin.landing-pages.show', props.landingPage.id) },
]

const formatJson = (json: Record<string, any>) => {
  return JSON.stringify(json, null, 2);
};
</script>

<template>
  <Head :title="landingPage.page_title" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">{{ landingPage.page_title }}</h1>
        <p class="text-sm text-muted-foreground">Details of the landing page.</p>
      </div>

      <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Page Code:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ landingPage.page_code }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Page URL:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white"><a :href="landingPage.page_url" target="_blank" class="text-cyan-600 hover:underline">{{ landingPage.page_url }}</a></p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Campaign:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ landingPage.campaign?.campaign_name ?? '-' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Template Used:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ landingPage.template_used ?? '-' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Language:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ landingPage.language }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Conversion Goal:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ landingPage.conversion_goal ?? '-' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Views:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ landingPage.views }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Submissions:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ landingPage.submissions }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Conversion Rate:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ landingPage.conversion_rate }}%</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Is Active:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ landingPage.is_active ? 'Yes' : 'No' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Created At:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ format(new Date(landingPage.created_at), 'PPP p') }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Updated At:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ format(new Date(landingPage.updated_at), 'PPP p') }}</p>
          </div>
        </div>

        <div class="mt-6">
          <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Form Fields:</p>
          <pre class="mt-1 text-lg text-gray-900 dark:text-white bg-gray-100 dark:bg-gray-800 p-4 rounded-md overflow-auto">{{ formatJson(landingPage.form_fields) }}</pre>
        </div>

        <div class="mt-6 flex justify-end">
          <Link :href="route('admin.landing-pages.edit', landingPage.id)" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
            Edit Landing Page
          </Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
