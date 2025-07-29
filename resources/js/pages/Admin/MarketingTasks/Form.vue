<script setup lang="ts">
import InputLabel from '@/components/ui/label/Label.vue'
import TextInput from '@/components/ui/input/Input.vue'
import InputError from '@/components/InputError.vue'

const props = defineProps({
  form: Object, // This form object will be passed from Create.vue or Edit.vue
  campaigns: Array, // Prop for campaigns data
  staffMembers: Array, // Prop for staffMembers data
  contents: Array, // Prop for contents data
  taskTypes: Array, // Prop for taskTypes data
  statuses: Array, // Prop for statuses data
});
</script>

<template>
  <div>
    <InputLabel for="title" value="Title" />
    <TextInput
      id="title"
      type="text"
      class="mt-1 block w-full shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 p-2.5"
      v-model="props.form.title"
      required
      autofocus
    />
    <InputError class="mt-2" :message="props.form.errors.title" />
  </div>

  <div>
    <InputLabel for="campaign_id" value="Campaign" />
    <select
      id="campaign_id"
      v-model="props.form.campaign_id"
      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
    >
      <option value="">Select a Campaign</option>
      <option v-for="campaign in props.campaigns" :key="campaign.id" :value="campaign.id">{{ campaign.campaign_name }}</option>
    </select>
    <InputError class="mt-2" :message="props.form.errors.campaign_id" />
  </div>

  <div>
    <InputLabel for="assigned_to_staff_id" value="Assigned To Staff" />
    <select
      id="assigned_to_staff_id"
      v-model="props.form.assigned_to_staff_id"
      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
    >
      <option value="">Select Staff</option>
      <option v-for="staff in props.staffMembers" :key="staff.id" :value="staff.id">{{ staff.full_name }}</option>
    </select>
    <InputError class="mt-2" :message="props.form.errors.assigned_to_staff_id" />
  </div>

  <div>
    <InputLabel for="related_content_id" value="Related Content" />
    <select
      id="related_content_id"
      v-model="props.form.related_content_id"
      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
    >
      <option value="">Select Content</option>
      <option v-for="content in props.contents" :key="content.id" :value="content.id">{{ content.title }}</option>
    </select>
    <InputError class="mt-2" :message="props.form.errors.related_content_id" />
  </div>

  <div>
    <InputLabel for="doctor_id" value="Doctor (if filming)" />
    <select
      id="doctor_id"
      v-model="props.form.doctor_id"
      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
    >
      <option value="">Select Doctor</option>
      <option v-for="staff in props.staffMembers" :key="staff.id" :value="staff.id">{{ staff.full_name }}</option>
    </select>
    <InputError class="mt-2" :message="props.form.errors.doctor_id" />
  </div>

  <div>
    <InputLabel for="task_type" value="Task Type" />
    <select
      id="task_type"
      v-model="props.form.task_type"
      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
      required
    >
      <option value="">Select a Type</option>
      <option v-for="type in props.taskTypes" :key="type" :value="type">{{ type }}</option>
    </select>
    <InputError class="mt-2" :message="props.form.errors.task_type" />
  </div>

  <div>
    <InputLabel for="description" value="Description" />
    <textarea
      id="description"
      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white bg-gray-50 p-2.5"
      v-model="props.form.description"
    ></textarea>
    <InputError class="mt-2" :message="props.form.errors.description" />
  </div>

  <div>
    <InputLabel for="scheduled_at" value="Scheduled At" />
    <TextInput
      id="scheduled_at"
      type="datetime-local"
      class="mt-1 block w-full shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 p-2.5"
      v-model="props.form.scheduled_at"
      required
    />
    <InputError class="mt-2" :message="props.form.errors.scheduled_at" />
  </div>

  <div>
    <InputLabel for="completed_at" value="Completed At" />
    <TextInput
      id="completed_at"
      type="datetime-local"
      class="mt-1 block w-full shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 p-2.5"
      v-model="props.form.completed_at"
    />
    <InputError class="mt-2" :message="props.form.errors.completed_at" />
  </div>

  <div>
    <InputLabel for="status" value="Status" />
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

  <div>
    <InputLabel for="notes" value="Notes" />
    <textarea
      id="notes"
      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white bg-gray-50 p-2.5"
      v-model="props.form.notes"
    ></textarea>
    <InputError class="mt-2" :message="props.form.errors.notes" />
  </div>
</template>
