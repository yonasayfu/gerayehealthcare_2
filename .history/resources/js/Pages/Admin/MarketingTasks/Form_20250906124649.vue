<script setup lang="ts">
import TextInput from '@/components/ui/input/Input.vue'
import InputError from '@/components/InputError.vue'
import { computed } from 'vue'
import { format } from 'date-fns'

const props = defineProps({
  form: Object,
  campaigns: Array,
  staffs: Array,
  campaignContents: Array,
  taskTypes: Array,
  statuses: Array,
});

const formattedScheduledAt = computed({
  get: () => {
    return props.form.scheduled_at ? format(new Date(props.form.scheduled_at), "yyyy-MM-dd'T'HH:mm") : '';
  },
  set: (value) => {
    props.form.scheduled_at = value;
  },
});

const formattedCompletedAt = computed({
  get: () => {
    return props.form.completed_at ? format(new Date(props.form.completed_at), "yyyy-MM-dd'T'HH:mm") : '';
  },
  set: (value) => {
    props.form.completed_at = value;
  },
});
</script>

<template>
  <div class="grid grid-cols-6 gap-6">
    <div class="col-span-6 sm:col-span-3">
      <label for="title" class="block text-sm font-medium text-gray-900 dark:text-white">Title</label>
      <TextInput
        id="title"
        type="text"
        class="mt-1 block w-full shadow-sm bg-white dark:bg-gray-800 border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 p-2.5"
        v-model="props.form.title"
        required
        autofocus
      />
      <InputError class="mt-2" :message="props.form.errors.title" />
    </div>

    <div class="col-span-6 sm:col-span-3">
      <label for="task_code" class="block text-sm font-medium text-gray-900 dark:text-white">Task Code</label>
      <TextInput
        id="task_code"
        type="text"
        class="mt-1 block w-full shadow-sm bg-white dark:bg-gray-800 border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 p-2.5"
        v-model="props.form.task_code"
        disabled
      />
      <InputError class="mt-2" :message="props.form.errors.task_code" />
    </div>

    <div class="col-span-6 sm:col-span-3">
      <label for="campaign_id" class="block text-sm font-medium text-gray-900 dark:text-white">Campaign</label>
      <select
        id="campaign_id"
        v-model="props.form.campaign_id"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
      >
        <option value="">Select a Campaign</option>
        <option v-for="campaign in props.campaigns" :key="campaign.id" :value="Number(campaign.id)">{{ campaign.campaign_name }}</option>
      </select>
      <InputError class="mt-2" :message="props.form.errors.campaign_id" />
    </div>

    <div class="col-span-6 sm:col-span-3">
      <label for="assigned_to_staff_id" class="block text-sm font-medium text-gray-900 dark:text-white">Assigned To Staff</label>
      <select
        id="assigned_to_staff_id"
        v-model="props.form.assigned_to_staff_id"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
      >
        <option value="">Select Staff</option>
        <option v-for="staff in props.staffs" :key="staff.id" :value="Number(staff.id)">{{ staff.user?.name ?? '-' }}</option>
      </select>
      <InputError class="mt-2" :message="props.form.errors.assigned_to_staff_id" />
    </div>

    <div class="col-span-6 sm:col-span-3">
      <label for="related_content_id" class="block text-sm font-medium text-gray-900 dark:text-white">Related Content</label>
      <select
        id="related_content_id"
        v-model="props.form.related_content_id"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
      >
        <option :value="null">Select Content</option>
        <option v-for="content in props.campaignContents" :key="content.id" :value="Number(content.id)">{{ content.title ?? '-' }}</option>
      </select>
      <InputError class="mt-2" :message="props.form.errors.related_content_id" />
    </div>

    <div class="col-span-6 sm:col-span-3">
      <label for="doctor_id" class="block text-sm font-medium text-gray-900 dark:text-white">Doctor</label>
      <select
        id="doctor_id"
        v-model="props.form.doctor_id"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
      >
        <option :value="null">Select Doctor</option>
        <option v-for="staff in props.staffs" :key="staff.id" :value="Number(staff.id)">{{ staff.user?.name ?? '-' }}</option>
      </select>
      <InputError class="mt-2" :message="props.form.errors.doctor_id" />
    </div>

    <div class="col-span-6 sm:col-span-3">
      <label for="task_type" class="block text-sm font-medium text-gray-900 dark:text-white">Task Type</label>
      <select
        id="task_type"
        v-model="props.form.task_type"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
        required
      >
        <option value="">Select Type</option>
        <option v-for="type in props.taskTypes" :key="type" :value="type">{{ type }}</option>
      </select>
      <InputError class="mt-2" :message="props.form.errors.task_type" />
    </div>

    <div class="col-span-6 sm:col-span-3">
      <label for="status" class="block text-sm font-medium text-gray-900 dark:text-white">Status</label>
      <select
        id="status"
        v-model="props.form.status"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
        required
      >
        <option v-for="s in props.statuses" :key="s" :value="s">{{ s }}</option>
      </select>
      <InputError class="mt-2" :message="props.form.errors.status" />
    </div>

    <div class="col-span-6 sm:col-span-3">
      <label for="scheduled_at" class="block text-sm font-medium text-gray-900 dark:text-white">Scheduled At</label>
      <TextInput
        id="scheduled_at"
        type="datetime-local"
        class="mt-1 block w-full shadow-sm bg-white dark:bg-gray-800 border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 p-2.5"
        v-model="formattedScheduledAt"
      />
      <InputError class="mt-2" :message="props.form.errors.scheduled_at" />
    </div>

    <div class="col-span-6 sm:col-span-3">
      <label for="completed_at" class="block text-sm font-medium text-gray-900 dark:text-white">Completed At</label>
      <TextInput
        id="completed_at"
        type="datetime-local"
        class="mt-1 block w-full shadow-sm bg-white dark:bg-gray-800 border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 p-2.5"
        v-model="formattedCompletedAt"
      />
      <InputError class="mt-2" :message="props.form.errors.completed_at" />
    </div>

    <div class="col-span-full">
      <label for="description" class="block text-sm font-medium text-gray-900 dark:text-white">Description</label>
      <textarea
        id="description"
        rows="3"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white bg-gray-50 p-2.5"
        v-model="props.form.description"
      ></textarea>
      <InputError class="mt-2" :message="props.form.errors.description" />
    </div>

    <div class="col-span-full">
      <label for="expected_results" class="block text-sm font-medium text-gray-900 dark:text-white">Expected Results</label>
      <textarea
        id="expected_results"
        rows="3"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white bg-gray-50 p-2.5"
        v-model="props.form.expected_results"
      ></textarea>
      <InputError class="mt-2" :message="props.form.errors.expected_results" />
    </div>

    <div class="col-span-full">
      <label for="notes" class="block text-sm font-medium text-gray-900 dark:text-white">Notes</label>
      <textarea
        id="notes"
        rows="3"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white bg-gray-50 p-2.5"
        v-model="props.form.notes"
      ></textarea>
      <InputError class="mt-2" :message="props.form.errors.notes" />
    </div>
  </div>
</template>