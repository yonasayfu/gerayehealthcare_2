<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { format } from 'date-fns'

interface MarketingBudget {
  id: number;
  budget_name: string;
  description: string;
  allocated_amount: number;
  spent_amount: number;
  period_start: string;
  period_end: string;
  status: string;
  campaign: { campaign_name: string };
  platform: { name: string };
  created_at: string;
  updated_at: string;
}

const props = defineProps<{
  marketingBudget: MarketingBudget;
}>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Marketing Budgets', href: route('admin.marketing-budgets.index') },
  { title: props.marketingBudget.budget_name, href: route('admin.marketing-budgets.show', props.marketingBudget.id) },
]

function printCurrent() {
  window.print();
}
</script>

<template>
  <Head :title="marketingBudget.budget_name" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6 print:p-0 print:space-y-0">
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">{{ marketingBudget.budget_name }}</h1>
        <p class="text-sm text-muted-foreground">Details of the marketing budget.</p>
      </div>

      <div class="print-document bg-card text-card-foreground p-6 rounded-lg shadow print:shadow-none print:rounded-none print:bg-transparent">
        <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
          <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
          <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
          <p class="text-gray-600 dark:text-gray-400 print-document-title">Marketing Budget Record</p>
          <hr class="my-3 border-gray-300 print:my-2">
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Budget Name:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingBudget.budget_name }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Campaign:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingBudget.campaign?.campaign_name ?? '-' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Platform:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingBudget.platform?.name ?? '-' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Allocated Amount:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingBudget.allocated_amount }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Spent Amount:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingBudget.spent_amount }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Period Start:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingBudget.period_start ? format(new Date(marketingBudget.period_start), 'PPP') : '-' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Period End:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingBudget.period_end ? format(new Date(marketingBudget.period_end), 'PPP') : '-' }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Status:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingBudget.status }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Created At:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ format(new Date(marketingBudget.created_at), 'PPP p') }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Updated At:</p>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ format(new Date(marketingBudget.updated_at), 'PPP p') }}</p>
          </div>
        </div>

        <div class="mt-6">
          <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Description:</p>
          <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ marketingBudget.description ?? '-' }}</p>
        </div>

        <div class="mt-6 flex flex-wrap gap-2 justify-end print:hidden">
          <Link :href="route('admin.marketing-budgets.index')" class="btn-glass btn-glass-sm">
            Back to List
          </Link>
          <button @click="printCurrent" class="btn-glass btn-glass-sm" title="Print this budget">
            Print Current
          </button>
          <Link :href="route('admin.marketing-budgets.edit', marketingBudget.id)" class="btn-glass btn-glass-sm">
            Edit
          </Link>
        </div>

        <div class="hidden print:block text-center mt-4 text-sm text-gray-500">
          <hr class="my-2 border-gray-300">
          <p>Printed on: {{ format(new Date(), 'PPP p') }}</p>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
