<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { format } from 'date-fns'

interface MarketingCampaign {
  id: number;
  campaign_code: string;
  campaign_name: string;
  campaign_type: string;
  target_audience: Record<string, any>;
  budget_allocated: number;
  budget_spent: number;
  start_date: string;
  end_date: string;
  status: string;
  utm_campaign: string;
  utm_source: string;
  utm_medium: string;
  goals: Record<string, any>;
  platform: { name: string };
  assigned_staff: { full_name: string };
  created_by_staff: { full_name: string };
  created_at: string;
  updated_at: string;
}

const props = defineProps<{
  marketingCampaign: MarketingCampaign;
}>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Marketing Campaigns', href: route('admin.marketing-campaigns.index') },
  { title: props.marketingCampaign.campaign_name, href: route('admin.marketing-campaigns.show', props.marketingCampaign.id) },
]

const formatJson = (json: Record<string, any>) => {
  return JSON.stringify(json, null, 2);
};
</script>

<template>
  <Head :title="marketingCampaign.campaign_name" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">{{ marketingCampaign.campaign_name }}</h1>
        <p class="text-sm text-muted-foreground">Details of the marketing campaign.</p>
      </div>

      <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Campaign Code:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingCampaign.campaign_code }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Platform:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingCampaign.platform?.name ?? '-' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Campaign Type:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingCampaign.campaign_type ?? '-' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Status:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingCampaign.status }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Start Date:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingCampaign.start_date ? format(new Date(marketingCampaign.start_date), 'PPP') : '-' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">End Date:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingCampaign.end_date ? format(new Date(marketingCampaign.end_date), 'PPP') : '-' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Budget Allocated:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingCampaign.budget_allocated }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Budget Spent:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingCampaign.budget_spent }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">UTM Campaign:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingCampaign.utm_campaign ?? '-' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">UTM Source:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingCampaign.utm_source ?? '-' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">UTM Medium:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingCampaign.utm_medium ?? '-' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Assigned Staff:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingCampaign.assigned_staff?.full_name ?? '-' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Created By:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingCampaign.created_by_staff?.full_name ?? '-' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Created At:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ format(new Date(marketingCampaign.created_at), 'PPP p') }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Updated At:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ format(new Date(marketingCampaign.updated_at), 'PPP p') }}</p>
          </div>
        </div>

        <div class="mt-6">
          <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Target Audience:</p>
          <pre class="mt-1 text-lg text-gray-900 dark:text-white bg-gray-100 dark:bg-gray-800 p-4 rounded-md overflow-auto">{{ formatJson(marketingCampaign.target_audience) }}</pre>
        </div>

        <div class="mt-6">
          <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Goals:</p>
          <pre class="mt-1 text-lg text-gray-900 dark:text-white bg-gray-100 dark:bg-gray-800 p-4 rounded-md overflow-auto">{{ formatJson(marketingCampaign.goals) }}</pre>
        </div>

        <div class="mt-6 flex justify-end">
          <Link :href="route('admin.marketing-campaigns.edit', marketingCampaign.id)" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
            Edit Campaign
          </Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
