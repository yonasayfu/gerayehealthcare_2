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
  <Head :title="`Edit ${marketingPlatform.name}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="bg-white border border-4 rounded-lg shadow relative m-10">

        <div class="flex items-start justify-between p-5 border-b rounded-t">
            <h3 class="text-xl font-semibold">
                Edit Marketing Platform
            </h3>
            <Link :href="route('admin.marketing-platforms.index')" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
               <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </Link>
        </div>

        <form @submit.prevent="submit">
            <div class="p-6 space-y-6">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Platform Details</h2>
                <Form :form="form" />
            </div>

            <div class="p-6 border-t border-gray-200 rounded-b">
                <button :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="btn btn-primary" type="submit">
                  {{ form.processing ? 'Saving...' : 'Save Changes' }}
                </button>
            </div>
        </form>

    </div>
  </AppLayout>
</template>