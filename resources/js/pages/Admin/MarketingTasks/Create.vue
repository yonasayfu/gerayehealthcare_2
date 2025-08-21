<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Form from './Form.vue' // Import the new Form component

const props = defineProps<{
  campaigns: Array<any>;
  staffMembers: Array<any>;
  contents: Array<any>;
  taskTypes: Array<string>;
  statuses: Array<string>;
}>();

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Marketing Tasks', href: route('admin.marketing-tasks.index') },
  { title: 'Create', href: route('admin.marketing-tasks.create') },
]

const form = useForm({
  task_code: '',
  campaign_id: '',
  assigned_to_staff_id: '',
  related_content_id: '',
  doctor_id: '',
  task_type: '',
  title: '',
  description: '',
  expected_results: '',
  scheduled_at: '',
  completed_at: '',
  status: 'Pending',
  notes: '',
});

const submit = () => {
  form.post(route('admin.marketing-tasks.store'));
};
</script>

<template>
  <Head title="Create New Marketing Task" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header (no Back button here) -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div class="print:hidden">
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Create New Marketing Task</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Fill required information to create a marketing task.</p>
          </div>
        </div>
      </div>

      <!-- Form card -->
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <Form :form="form" v-bind="$props" />
          <!-- Footer actions: Cancel + Create (right aligned, no logic change) -->
          <div class="flex justify-end gap-2 pt-2">
            <Link :href="route('admin.marketing-tasks.index')" class="btn-glass btn-glass-sm">Cancel</Link>

            <button
              type="submit"
              :disabled="form.processing"
              class="btn-glass btn-glass-sm"
            >
              {{ form.processing ? 'Creating...' : 'Create Marketing Task' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>