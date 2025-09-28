<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Form from './Form.vue' // Import the new Form component

interface MarketingPlatform {
  id: number;
  name: string;
  api_endpoint: string;
  api_credentials: string;
  is_active: boolean;
}

const props = defineProps<{
  marketingPlatform: MarketingPlatform;
}>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Marketing Platforms', href: route('admin.marketing-platforms.index') },
  { title: props.marketingPlatform.name, href: route('admin.marketing-platforms.show', props.marketingPlatform.id) },
  { title: 'Edit', href: route('admin.marketing-platforms.edit', props.marketingPlatform.id) },
]

const form = useForm({
  name: props.marketingPlatform.name,
  api_endpoint: props.marketingPlatform.api_endpoint,
  api_credentials: props.marketingPlatform.api_credentials,
  is_active: props.marketingPlatform.is_active,
});

const submit = () => {
  form.put(route('admin.marketing-platforms.update', props.marketingPlatform.id));
};
</script>

<template>
  <Head title="Edit Marketing Platform" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header: Back button removed -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div>
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Marketing Platform</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Update marketing platform information below.</p>
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
              :href="route('admin.marketing-platforms.index')"
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