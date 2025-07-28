<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { format } from 'date-fns'

interface MarketingPlatform {
  id: number;
  name: string;
  api_endpoint: string;
  api_credentials: string;
  is_active: boolean;
  created_at: string;
  updated_at: string;
}

const props = defineProps<{
  marketingPlatform: MarketingPlatform;
}>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Marketing Platforms', href: route('admin.marketing-platforms.index') },
  { title: props.marketingPlatform.name, href: route('admin.marketing-platforms.show', props.marketingPlatform.id) },
]
</script>

<template>
  <Head :title="marketingPlatform.name" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">{{ marketingPlatform.name }}</h1>
        <p class="text-sm text-muted-foreground">Details of the marketing platform.</p>
      </div>

      <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Platform Name:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingPlatform.name }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">API Endpoint:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingPlatform.api_endpoint ?? '-' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">API Credentials:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingPlatform.api_credentials ?? '-' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Is Active:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingPlatform.is_active ? 'Yes' : 'No' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Created At:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ format(new Date(marketingPlatform.created_at), 'PPP p') }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Updated At:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ format(new Date(marketingPlatform.updated_at), 'PPP p') }}</p>
          </div>
        </div>

        <div class="mt-6 flex justify-end">
          <Link :href="route('admin.marketing-platforms.edit', marketingPlatform.id)" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
            Edit Platform
          </Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
