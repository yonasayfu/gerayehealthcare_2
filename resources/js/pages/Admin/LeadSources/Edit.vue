<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Form from './Form.vue' // Import the new Form component

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
  <Head :title="`Edit ${leadSource.name}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="bg-white border border-4 rounded-lg shadow relative m-10">

        <div class="flex items-start justify-between p-5 border-b rounded-t">
            <h3 class="text-xl font-semibold">
                Edit Lead Source
            </h3>
            <Link :href="route('admin.lead-sources.index')" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
               <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </Link>
        </div>

        <form @submit.prevent="submit">
            <div class="p-6 space-y-6">
                <Form :form="form" :categories="props.categories" />
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