<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Form from './Form.vue' // Import the new Form component
import FormActions from '@/components/FormActions.vue'

interface MarketingTask {
  id: number;
  task_code: string;
  title: string;
  description: string;
  expected_results?: string;
  task_type: string;
  status: string;
  scheduled_at: string;
  completed_at: string;
  notes: string;
  campaign_id: number;
  assigned_to_staff_id: number | null;
  related_content_id: number | null;
  doctor_id: number | null;
}

const props = defineProps<{
  marketingTask: MarketingTask;
  campaigns: Array<any>;
  staffs: Array<any>;
  campaignContents: Array<any>;
  taskTypes: Array<string>;
  statuses: Array<string>;
}>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Marketing Tasks', href: route('admin.marketing-tasks.index') },
  { title: props.marketingTask.title, href: route('admin.marketing-tasks.show', props.marketingTask.id) },
  { title: 'Edit', href: route('admin.marketing-tasks.edit', props.marketingTask.id) },
]

const form = useForm({
  task_code: props.marketingTask.task_code,
  campaign_id: props.marketingTask.campaign_id,
  assigned_to_staff_id: props.marketingTask.assigned_to_staff_id ?? null,
  related_content_id: props.marketingTask.related_content_id ?? null,
  doctor_id: props.marketingTask.doctor_id ?? null,
  task_type: props.marketingTask.task_type,
  title: props.marketingTask.title,
  description: props.marketingTask.description,
  expected_results: props.marketingTask.expected_results ?? '',
  scheduled_at: props.marketingTask.scheduled_at,
  completed_at: props.marketingTask.completed_at,
  status: props.marketingTask.status,
  notes: props.marketingTask.notes,
});

const submit = () => {
  form.put(route('admin.marketing-tasks.update', props.marketingTask.id));
};
</script>

<template>
  <Head title="Edit Marketing Task" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header: Back button removed -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div>
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Marketing Task</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Update marketing task information below.</p>
          </div>
          <!-- intentionally no Back button here -->
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <Form :form="form" v-bind="$props" />

          <FormActions :cancel-href="route('admin.marketing-tasks.index')" submit-text="Save Changes" :processing="form.processing" />
        </form>
      </div>
    </div>
  </AppLayout>
</template>
