<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import InputLabel from '@/components/ui/label/Label.vue'
import TextInput from '@/components/ui/input/Input.vue'
import InputError from '@/components/InputError.vue'
import PrimaryButton from '@/components/ui/button/Button.vue'
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
  assigned_staff_id: number;
  goals: Record<string, any>;
  platform_id: number;
}

const props = defineProps<{
  marketingCampaign: MarketingCampaign;
}>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Marketing Campaigns', href: route('admin.marketing-campaigns.index') },
  { title: props.marketingCampaign.campaign_name, href: route('admin.marketing-campaigns.show', props.marketingCampaign.id) },
  { title: 'Edit', href: route('admin.marketing-campaigns.edit', props.marketingCampaign.id) },
]

const form = useForm({
  campaign_name: props.marketingCampaign.campaign_name,
  platform_id: props.marketingCampaign.platform_id,
  campaign_type: props.marketingCampaign.campaign_type,
  target_audience: JSON.stringify(props.marketingCampaign.target_audience, null, 2),
  budget_allocated: props.marketingCampaign.budget_allocated,
  budget_spent: props.marketingCampaign.budget_spent,
  start_date: props.marketingCampaign.start_date,
  end_date: props.marketingCampaign.end_date,
  status: props.marketingCampaign.status,
  utm_campaign: props.marketingCampaign.utm_campaign,
  utm_source: props.marketingCampaign.utm_source,
  utm_medium: props.marketingCampaign.utm_medium,
  assigned_staff_id: props.marketingCampaign.assigned_staff_id,
  goals: JSON.stringify(props.marketingCampaign.goals, null, 2),
});

const submit = () => {
  form.put(route('admin.marketing-campaigns.update', props.marketingCampaign.id));
};

// Dummy data for select options (replace with actual data from props if available)
const platforms = [
  { id: 1, name: 'TikTok' },
  { id: 2, name: 'Meta' },
  { id: 3, name: 'Google' },
  { id: 4, name: 'LinkedIn' },
];

const campaignTypes = [
  'Awareness',
  'Lead Gen',
  'Conversion',
];

const statuses = [
  'Draft',
  'Active',
  'Paused',
  'Completed',
];

const staffMembers = [
  { id: 1, full_name: 'John Doe' },
  { id: 2, full_name: 'Jane Smith' },
];
</script>

<template>
  <Head :title="`Edit ${marketingCampaign.campaign_name}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="bg-white border border-4 rounded-lg shadow relative m-10">

        <div class="flex items-start justify-between p-5 border-b rounded-t">
            <h3 class="text-xl font-semibold">
                Edit Campaign: {{ marketingCampaign.campaign_name }}
            </h3>
            <Link :href="route('admin.marketing-campaigns.index')" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
               <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </Link>
        </div>

        <div class="p-6 space-y-6">
            <form @submit.prevent="submit" class="space-y-6 bg-white dark:bg-gray-900 p-6 rounded-lg shadow">
                <div>
                    <InputLabel for="campaign_name" value="Campaign Name" />
                    <TextInput
                        id="campaign_name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.campaign_name"
                        required
                        autofocus
                    />
                    <InputError class="mt-2" :message="form.errors.campaign_name" />
                </div>

                <div>
                    <InputLabel for="platform_id" value="Platform" />
                    <select
                        id="platform_id"
                        v-model="form.platform_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
                        required
                    >
                        <option value="">Select a Platform</option>
                        <option v-for="platform in platforms" :key="platform.id" :value="platform.id">{{ platform.name }}</option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.platform_id" />
                </div>

                <div>
                    <InputLabel for="campaign_type" value="Campaign Type" />
                    <select
                        id="campaign_type"
                        v-model="form.campaign_type"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
                    >
                        <option value="">Select a Type</option>
                        <option v-for="type in campaignTypes" :key="type" :value="type">{{ type }}</option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.campaign_type" />
                </div>

                <div>
                    <InputLabel for="target_audience" value="Target Audience (JSON)" />
                    <TextInput
                        id="target_audience"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.target_audience"
                    />
                    <InputError class="mt-2" :message="form.errors.target_audience" />
                </div>

                <div>
                    <InputLabel for="budget_allocated" value="Budget Allocated" />
                    <TextInput
                        id="budget_allocated"
                        type="number"
                        step="0.01"
                        class="mt-1 block w-full"
                        v-model="form.budget_allocated"
                        required
                    />
                    <InputError class="mt-2" :message="form.errors.budget_allocated" />
                </div>

                <div>
                    <InputLabel for="budget_spent" value="Budget Spent" />
                    <TextInput
                        id="budget_spent"
                        type="number"
                        step="0.01"
                        class="mt-1 block w-full"
                        v-model="form.budget_spent"
                    />
                    <InputError class="mt-2" :message="form.errors.budget_spent" />
                </div>

                <div>
                    <InputLabel for="start_date" value="Start Date" />
                    <TextInput
                        id="start_date"
                        type="date"
                        class="mt-1 block w-full"
                        v-model="form.start_date"
                        required
                    />
                    <InputError class="mt-2" :message="form.errors.start_date" />
                </div>

                <div>
                    <InputLabel for="end_date" value="End Date" />
                    <TextInput
                        id="end_date"
                        type="date"
                        class="mt-1 block w-full"
                        v-model="form.end_date"
                    />
                    <InputError class="mt-2" :message="form.errors.end_date" />
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

                <div>
                    <InputLabel for="utm_campaign" value="UTM Campaign" />
                    <TextInput
                        id="utm_campaign"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.utm_campaign"
                    />
                    <InputError class="mt-2" :message="form.errors.utm_campaign" />
                </div>

                <div>
                    <InputLabel for="utm_source" value="UTM Source" />
                    <TextInput
                        id="utm_source"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.utm_source"
                    />
                    <InputError class="mt-2" :message="form.errors.utm_source" />
                </div>

                <div>
                    <InputLabel for="utm_medium" value="UTM Medium" />
                    <TextInput
                        id="utm_medium"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.utm_medium"
                    />
                    <InputError class="mt-2" :message="form.errors.utm_medium" />
                </div>

                <div>
                    <InputLabel for="assigned_staff_id" value="Assigned Staff" />
                    <select
                        id="assigned_staff_id"
                        v-model="form.assigned_staff_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
                    >
                        <option value="">Select Staff</option>
                        <option v-for="staff in staffMembers" :key="staff.id" :value="staff.id">{{ staff.full_name }}</option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.assigned_staff_id" />
                </div>

                <div>
                    <InputLabel for="goals" value="Goals (JSON)" />
                    <TextInput
                        id="goals"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.goals"
                    />
                    <InputError class="mt-2" :message="form.errors.goals" />
                </div>

                <div class="flex items-center justify-end">
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Update Campaign
                    </PrimaryButton>
                </div>
            </form>
        </div>

    </div>
  </AppLayout>
</template>
