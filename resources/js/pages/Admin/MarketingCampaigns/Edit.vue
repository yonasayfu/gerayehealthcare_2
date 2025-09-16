<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Form from './Form.vue'
import FormActions from '@/components/FormActions.vue'

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
  assigned_staff_id: number;
  goals: Record<string, any>;
  platform_id: number;
}

const props = defineProps<{
  marketingCampaign: MarketingCampaign;
  platforms: { id: number; name: string }[];
  staffMembers: { id: number; full_name: string }[];
  campaignTypes: string[];
  statuses: string[];
}>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Marketing Campaigns', href: route('admin.marketing-campaigns.index') },
  { title: props.marketingCampaign.campaign_name, href: route('admin.marketing-campaigns.show', props.marketingCampaign.id) },
  { title: 'Edit', href: route('admin.marketing-campaigns.edit', props.marketingCampaign.id) },
]

const toDateInput = (val?: string) => (val ? val.substring(0, 10) : '');

const form = useForm({
  campaign_name: props.marketingCampaign.campaign_name,
  platform_id: props.marketingCampaign.platform_id,
  campaign_type: props.marketingCampaign.campaign_type,
  target_audience: JSON.stringify(props.marketingCampaign.target_audience ?? {}, null, 2),
  budget_allocated: props.marketingCampaign.budget_allocated,
  budget_spent: props.marketingCampaign.budget_spent,
  start_date: toDateInput(props.marketingCampaign.start_date),
  end_date: toDateInput(props.marketingCampaign.end_date),
  status: props.marketingCampaign.status,
  utm_campaign: props.marketingCampaign.utm_campaign,
  utm_source: props.marketingCampaign.utm_source,
  utm_medium: props.marketingCampaign.utm_medium,
  assigned_staff_id: props.marketingCampaign.assigned_staff_id,
  goals: JSON.stringify(props.marketingCampaign.goals ?? {}, null, 2),
});

const submit = () => {
  form.put(route('admin.marketing-campaigns.update', props.marketingCampaign.id));
};

// Options now come from props: props.platforms, props.staffMembers, props.campaignTypes, props.statuses
</script>

<template>
  <Head title="Edit Marketing Campaign" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header: Back button removed -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div>
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Marketing Campaign</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Update marketing campaign information below.</p>
          </div>
          <!-- intentionally no Back button here -->
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <Form :form="form" :platforms="props.platforms" :staff-members="props.staffMembers" :campaign-types="props.campaignTypes" :statuses="props.statuses" />

          <FormActions :cancel-href="route('admin.marketing-campaigns.index')" submit-text="Save Changes" :processing="form.processing" />
        </form>
      </div>
    </div>
  </AppLayout>
</template>
