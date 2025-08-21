<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Form from './Form.vue' // Import the new Form component

interface MarketingBudget {
  id: number;
  budget_name: string;
  description: string;
  allocated_amount: number;
  spent_amount: number;
  period_start: string;
  period_end: string;
  status: string;
  campaign_id: number;
  platform_id: number;
}

const props = defineProps<{
  marketingBudget: MarketingBudget;
  campaigns: Array<any>;
  platforms: Array<any>;
  statuses: Array<string>;
}>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Marketing Budgets', href: route('admin.marketing-budgets.index') },
  { title: props.marketingBudget.budget_name, href: route('admin.marketing-budgets.show', props.marketingBudget.id) },
  { title: 'Edit', href: route('admin.marketing-budgets.edit', props.marketingBudget.id) },
]

const form = useForm({
  budget_name: props.marketingBudget.budget_name,
  campaign_id: props.marketingBudget.campaign_id,
  platform_id: props.marketingBudget.platform_id,
  description: props.marketingBudget.description,
  allocated_amount: props.marketingBudget.allocated_amount,
  spent_amount: props.marketingBudget.spent_amount,
  period_start: props.marketingBudget.period_start ? new Date(props.marketingBudget.period_start).toISOString().slice(0, 10) : '',
  period_end: props.marketingBudget.period_end ? new Date(props.marketingBudget.period_end).toISOString().slice(0, 10) : '',
  status: props.marketingBudget.status,
});

const submit = () => {
  form.put(route('admin.marketing-budgets.update', props.marketingBudget.id));
};
</script>

<template>
  <Head title="Edit Marketing Budget" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header: Back button removed -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div>
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Marketing Budget</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Update marketing budget information below.</p>
          </div>
          <!-- intentionally no Back button here -->
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <Form :form="form" v-bind="$props" />

          <!-- Footer actions: Cancel + Save (right aligned), no logic changes -->
          <div class="flex justify-end gap-2 pt-2">
            <Link
              :href="route('admin.marketing-budgets.index')"
              class="btn-glass btn-glass-sm"
            >
              Cancel
            </Link>

            <button
              type="submit"
              :disabled="form.processing"
              class="btn-glass btn-glass-sm"
            >
              {{ form.processing ? 'Saving...' : 'Save Changes' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>