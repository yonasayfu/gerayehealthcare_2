<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import InputLabel from '@/components/ui/label/Label.vue'
import TextInput from '@/components/ui/input/Input.vue'
import InputError from '@/components/InputError.vue'
import PrimaryButton from '@/components/ui/button/Button.vue'
import { format } from 'date-fns'

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Marketing Campaigns', href: route('admin.marketing-campaigns.index') },
  { title: 'Create', href: route('admin.marketing-campaigns.create') },
]

const form = useForm({
  campaign_name: '',
  platform_id: '',
  campaign_type: '',
  target_audience: '',
  budget_allocated: 0,
  budget_spent: 0,
  start_date: '',
  end_date: '',
  status: 'Draft',
  utm_campaign: '',
  utm_source: '',
  utm_medium: '',
  assigned_staff_id: '',
  goals: '',
});

const submit = () => {
  form.post(route('admin.marketing-campaigns.store'));
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
  <Head title="Create New Marketing Campaign" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header (no Back button here) -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div class="print:hidden">
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Create New Marketing Campaign</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Fill required information to create a marketing campaign.</p>
          </div>
        </div>
      </div>

      <!-- Form card -->
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <Form :form="form" v-bind="$props" />
          <!-- Footer actions: Cancel + Create (right aligned, no logic change) -->
          <div class="flex justify-end gap-2 pt-2">
            <Link :href="route('admin.marketing-campaigns.index')" class="btn-glass btn-glass-sm">Cancel</Link>

            <button
              type="submit"
              :disabled="form.processing"
              class="btn-glass btn-glass-sm"
            >
              {{ form.processing ? 'Creating...' : 'Create Marketing Campaign' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
