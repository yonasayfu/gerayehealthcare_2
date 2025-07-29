<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import InputLabel from '@/components/ui/label/Label.vue'
import TextInput from '@/components/ui/input/Input.vue'
import InputError from '@/components/InputError.vue'
import PrimaryButton from '@/components/ui/button/Button.vue'

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
  period_start: props.marketingBudget.period_start,
  period_end: props.marketingBudget.period_end,
  status: props.marketingBudget.status,
});

const submit = () => {
  form.put(route('admin.marketing-budgets.update', props.marketingBudget.id));
};

// Dummy data for select options (replace with actual data from props if available)
const campaigns = [
  { id: 1, campaign_name: 'Summer Sale 2024' },
  { id: 2, campaign_name: 'New Patient Drive' },
];

const platforms = [
  { id: 1, name: 'TikTok' },
  { id: 2, name: 'Meta' },
  { id: 3, name: 'Google' },
];

const statuses = [
  'Planned',
  'Active',
  'Completed',
  'Overspent',
];
</script>

<template>
  <Head :title="`Edit ${marketingBudget.budget_name}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Edit Marketing Budget</h1>
        <p class="text-sm text-muted-foreground">Update the details of the marketing budget.</p>
      </div>

      <form @submit.prevent="submit" class="space-y-6 bg-white dark:bg-gray-900 p-6 rounded-lg shadow">
        <div>
          <InputLabel for="budget_name" value="Budget Name" />
          <TextInput
            id="budget_name"
            type="text"
            class="mt-1 block w-full"
            v-model="form.budget_name"
            required
            autofocus
          />
          <InputError class="mt-2" :message="form.errors.budget_name" />
        </div>

        <div>
          <InputLabel for="campaign_id" value="Campaign" />
          <select
            id="campaign_id"
            v-model="form.campaign_id"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
          >
            <option value="">Select a Campaign</option>
            <option v-for="campaign in campaigns" :key="campaign.id" :value="campaign.id">{{ campaign.campaign_name }}</option>
          </select>
          <InputError class="mt-2" :message="form.errors.campaign_id" />
        </div>

        <div>
          <InputLabel for="platform_id" value="Platform" />
          <select
            id="platform_id"
            v-model="form.platform_id"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
          >
            <option value="">Select a Platform</option>
            <option v-for="platform in platforms" :key="platform.id" :value="platform.id">{{ platform.name }}</option>
          </select>
          <InputError class="mt-2" :message="form.errors.platform_id" />
        </div>

        <div>
          <InputLabel for="description" value="Description" />
          <textarea
            id="description"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
            v-model="form.description"
          ></textarea>
          <InputError class="mt-2" :message="form.errors.description" />
        </div>

        <div>
          <InputLabel for="allocated_amount" value="Allocated Amount" />
          <TextInput
            id="allocated_amount"
            type="number"
            step="0.01"
            class="mt-1 block w-full"
            v-model="form.allocated_amount"
            required
          />
          <InputError class="mt-2" :message="form.errors.allocated_amount" />
        </div>

        <div>
          <InputLabel for="spent_amount" value="Spent Amount" />
          <TextInput
            id="spent_amount"
            type="number"
            step="0.01"
            class="mt-1 block w-full"
            v-model="form.spent_amount"
          />
          <InputError class="mt-2" :message="form.errors.spent_amount" />
        </div>

        <div>
          <InputLabel for="period_start" value="Period Start Date" />
          <TextInput
            id="period_start"
            type="date"
            class="mt-1 block w-full"
            v-model="form.period_start"
            required
          />
          <InputError class="mt-2" :message="form.errors.period_start" />
        </div>

        <div>
          <InputLabel for="period_end" value="Period End Date" />
          <TextInput
            id="period_end"
            type="date"
            class="mt-1 block w-full"
            v-model="form.period_end"
          />
          <InputError class="mt-2" :message="form.errors.period_end" />
        </div>

        <div>
          <InputLabel for="status" value="Status" />
          <select
            id="status"
            v-model="form.status"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
            required
          >
            <option v-for="s in statuses" :key="s" :value="s">{{ s }}</option>
          </select>
          <InputError class="mt-2" :message="form.errors.status" />
        </div>

        <div class="flex items-center justify-end">
          <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
            Update Budget
          </PrimaryButton>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
