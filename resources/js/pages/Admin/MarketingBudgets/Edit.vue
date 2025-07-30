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
  <Head :title="`Edit ${marketingBudget.budget_name}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="bg-white border border-4 rounded-lg shadow relative m-10">

        <div class="flex items-start justify-between p-5 border-b rounded-t">
            <h3 class="text-xl font-semibold">
                Edit Marketing Budget
            </h3>
            <Link :href="route('admin.marketing-budgets.index')" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
               <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </Link>
        </div>

        <form @submit.prevent="submit">
            <div class="p-6 space-y-6">
                <Form :form="form" :campaigns="props.campaigns" :platforms="props.platforms" :statuses="props.statuses" />
            </div>

            <div class="p-6 border-t border-gray-200 rounded-b">
                <button :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="text-white bg-cyan-600 hover:bg-cyan-700 focus:ring-4 focus:ring-cyan-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center" type="submit">
                  {{ form.processing ? 'Saving...' : 'Save Changes' }}
                </button>
            </div>
        </form>

    </div>
  </AppLayout>
</template>