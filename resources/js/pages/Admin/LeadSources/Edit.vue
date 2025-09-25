<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Form from './Form.vue' // Import the new Form component
import FormActions from '@/components/FormActions.vue'

interface LeadSource {
  id: number;
  name: string;
  category: string;
  description: string;
  is_active: boolean;
}

const props = defineProps<{
  leadSource: LeadSource;
  categories: Array<string>;
}>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Lead Sources', href: route('admin.lead-sources.index') },
  { title: props.leadSource.name, href: route('admin.lead-sources.show', props.leadSource.id) },
  { title: 'Edit', href: route('admin.lead-sources.edit', props.leadSource.id) },
]

const form = useForm({
  name: props.leadSource.name,
  category: props.leadSource.category,
  description: props.leadSource.description,
  is_active: props.leadSource.is_active,
});

console.log('Edit.vue - leadSource.category:', props.leadSource.category);
console.log('Edit.vue - form.category initial:', form.category);

const submit = () => {
  form.put(route('admin.lead-sources.update', props.leadSource.id));
};
</script>

<template>
  <Head title="Edit Lead Source" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header: Back button removed -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div>
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Lead Source</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Update lead source information below.</p>
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
              :href="route('admin.lead-sources.index')"
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
