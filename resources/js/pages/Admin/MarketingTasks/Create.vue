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
  <Head title="Create Marketing Task" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="bg-white border border-4 rounded-lg shadow relative m-10">

        <div class="flex items-start justify-between p-5 border-b rounded-t">
            <h3 class="text-xl font-semibold">
                Create New Marketing Task
            </h3>
            <Link :href="route('admin.marketing-tasks.index')" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
               <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </Link>
        </div>

        <form @submit.prevent="submit">
            <div class="p-6 space-y-6">
                <Form :form="form" :campaigns="props.campaigns" :staffs="props.staffMembers" :campaignContents="props.contents" :taskTypes="props.taskTypes" :statuses="props.statuses" />
            </div>

            <div class="p-6 border-t border-gray-200 rounded-b">
                <button :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="text-white bg-cyan-600 hover:bg-cyan-700 focus:ring-4 focus:ring-cyan-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center" type="submit">
                  {{ form.processing ? 'Creating...' : 'Create Task' }}
                </button>
            </div>
        </form>

    </div>
  </AppLayout>
</template>