<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import InputLabel from '@/components/ui/label/Label.vue'
import TextInput from '@/components/ui/input/Input.vue'
import InputError from '@/components/InputError.vue'
import PrimaryButton from '@/components/ui/button/Button.vue'

interface MarketingTask {
  id: number;
  task_code: string;
  title: string;
  description: string;
  task_type: string;
  status: string;
  scheduled_at: string;
  completed_at: string;
  notes: string;
  campaign_id: number;
  assigned_to_staff_id: number;
  related_content_id: number;
  doctor_id: number;
}

const props = defineProps<{
  marketingTask: MarketingTask;
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
  assigned_to_staff_id: props.marketingTask.assigned_to_staff_id,
  related_content_id: props.marketingTask.related_content_id,
  doctor_id: props.marketingTask.doctor_id,
  task_type: props.marketingTask.task_type,
  title: props.marketingTask.title,
  description: props.marketingTask.description,
  scheduled_at: props.marketingTask.scheduled_at,
  completed_at: props.marketingTask.completed_at,
  status: props.marketingTask.status,
  notes: props.marketingTask.notes,
});

const submit = () => {
  form.put(route('admin.marketing-tasks.update', props.marketingTask.id));
};

// Dummy data for select options (replace with actual data from props if available)
const campaigns = [
  { id: 1, campaign_name: 'Summer Sale 2024' },
  { id: 2, campaign_name: 'New Patient Drive' },
];

const staffMembers = [
  { id: 1, full_name: 'John Doe' },
  { id: 2, full_name: 'Jane Smith' },
];

const contents = [
  { id: 1, title: 'Blog Post: New Services' },
  { id: 2, title: 'Video: Patient Testimonials' },
];

const taskTypes = [
  'Content Creation',
  'Posting',
  'Doctor Filming',
  'Lead Follow-up',
  'Campaign Setup',
];

const statuses = [
  'Pending',
  'In Progress',
  'Completed',
  'Cancelled',
];
</script>

<template>
  <Head :title="`Edit ${marketingTask.title}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Edit Marketing Task</h1>
        <p class="text-sm text-muted-foreground">Update the details of the marketing task.</p>
      </div>

      <form @submit.prevent="submit" class="space-y-6 bg-white dark:bg-gray-900 p-6 rounded-lg shadow">
        <div>
          <InputLabel for="title" value="Title" />
          <TextInput
            id="title"
            type="text"
            class="mt-1 block w-full"
            v-model="form.title"
            required
            autofocus
          />
          <InputError class="mt-2" :message="form.errors.title" />
        </div>

        <div>
          <InputLabel for="campaign_id" value="Campaign" />
          <select
            id="campaign_id"
            v-model="form.campaign_id"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
          >
            <option value="">Select a Campaign</option>
            <option v-for="campaign in campaigns" :key="campaign.id" :value="campaign.id">{{ campaign.campaign_name }}</option>
          </select>
          <InputError class="mt-2" :message="form.errors.campaign_id" />
        </div>

        <div>
          <InputLabel for="assigned_to_staff_id" value="Assigned To Staff" />
          <select
            id="assigned_to_staff_id"
            v-model="form.assigned_to_staff_id"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
          >
            <option value="">Select Staff</option>
            <option v-for="staff in staffMembers" :key="staff.id" :value="staff.id">{{ staff.full_name }}</option>
          </select>
          <InputError class="mt-2" :message="form.errors.assigned_to_staff_id" />
        </div>

        <div>
          <InputLabel for="related_content_id" value="Related Content" />
          <select
            id="related_content_id"
            v-model="form.related_content_id"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
          >
            <option value="">Select Content</option>
            <option v-for="content in contents" :key="content.id" :value="content.id">{{ content.title }}</option>
          </select>
          <InputError class="mt-2" :message="form.errors.related_content_id" />
        </div>

        <div>
          <InputLabel for="doctor_id" value="Doctor (if filming)" />
          <select
            id="doctor_id"
            v-model="form.doctor_id"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
          >
            <option value="">Select Doctor</option>
            <option v-for="staff in staffMembers" :key="staff.id" :value="staff.id">{{ staff.full_name }}</option>
          </select>
          <InputError class="mt-2" :message="form.errors.doctor_id" />
        </div>

        <div>
          <InputLabel for="task_type" value="Task Type" />
          <select
            id="task_type"
            v-model="form.task_type"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
            required
          >
            <option value="">Select a Type</option>
            <option v-for="type in taskTypes" :key="type" :value="type">{{ type }}</option>
          </select>
          <InputError class="mt-2" :message="form.errors.task_type" />
        </div>

        <div>
          <InputLabel for="description" value="Description" />
          <textarea
            id="description"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
            v-model="form.description"
          ></textarea>
          <InputError class="mt-2" :message="form.errors.description" />
        </div>

        <div>
          <InputLabel for="scheduled_at" value="Scheduled At" />
          <TextInput
            id="scheduled_at"
            type="datetime-local"
            class="mt-1 block w-full"
            v-model="form.scheduled_at"
            required
          />
          <InputError class="mt-2" :message="form.errors.scheduled_at" />
        </div>

        <div>
          <InputLabel for="completed_at" value="Completed At" />
          <TextInput
            id="completed_at"
            type="datetime-local"
            class="mt-1 block w-full"
            v-model="form.completed_at"
          />
          <InputError class="mt-2" :message="form.errors.completed_at" />
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
          <InputLabel for="notes" value="Notes" />
          <textarea
            id="notes"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
            v-model="form.notes"
          ></textarea>
          <InputError class="mt-2" :message="form.errors.notes" />
        </div>

        <div class="flex items-center justify-end">
          <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
            Update Task
          </PrimaryButton>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
