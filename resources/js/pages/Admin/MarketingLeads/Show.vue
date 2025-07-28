<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { format } from 'date-fns'

interface MarketingLead {
  id: number;
  lead_code: string;
  first_name: string;
  last_name: string;
  email: string;
  phone: string;
  country: string;
  utm_source: string;
  utm_campaign: string;
  utm_medium: string;
  lead_score: number;
  status: string;
  conversion_date: string;
  notes: string;
  source_campaign: { campaign_name: string };
  landing_page: { page_title: string };
  assigned_staff: { full_name: string };
  converted_patient: { full_name: string };
  created_at: string;
  updated_at: string;
}

const props = defineProps<{
  marketingLead: MarketingLead;
}>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Marketing Leads', href: route('admin.marketing-leads.index') },
  { title: `${props.marketingLead.first_name} ${props.marketingLead.last_name}`, href: route('admin.marketing-leads.show', props.marketingLead.id) },
]
</script>

<template>
  <Head :title="`${marketingLead.first_name} ${marketingLead.last_name}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">{{ marketingLead.first_name }} {{ marketingLead.last_name }}</h1>
        <p class="text-sm text-muted-foreground">Details of the marketing lead.</p>
      </div>

      <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Lead Code:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingLead.lead_code }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Email:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingLead.email ?? '-' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingLead.phone ?? '-' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Country:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingLead.country ?? '-' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Status:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingLead.status }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Lead Score:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingLead.lead_score }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Source Campaign:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingLead.source_campaign?.campaign_name ?? '-' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Landing Page:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingLead.landing_page?.page_title ?? '-' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Assigned Staff:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingLead.assigned_staff?.full_name ?? '-' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Converted Patient:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingLead.converted_patient?.full_name ?? '-' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Conversion Date:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingLead.conversion_date ? format(new Date(marketingLead.conversion_date), 'PPP p') : '-' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">UTM Source:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingLead.utm_source ?? '-' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">UTM Campaign:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingLead.utm_campaign ?? '-' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">UTM Medium:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingLead.utm_medium ?? '-' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Created At:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ format(new Date(marketingLead.created_at), 'PPP p') }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Updated At:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ format(new Date(marketingLead.updated_at), 'PPP p') }}</p>
          </div>
        </div>

        <div class="mt-6">
          <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Notes:</p>
          <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingLead.notes ?? '-' }}</p>
        </div>

        <div class="mt-6 flex justify-end">
          <Link :href="route('admin.marketing-leads.edit', marketingLead.id)" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
            Edit Lead
          </Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
