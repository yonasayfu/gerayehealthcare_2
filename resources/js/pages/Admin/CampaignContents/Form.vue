<script setup lang="ts">
import InputLabel from '@/components/ui/label/Label.vue'
import TextInput from '@/components/ui/input/Input.vue'
import InputError from '@/components/InputError.vue'
import { computed } from 'vue'
import { format } from 'date-fns'

const props = defineProps({
  form: Object, // This form object will be passed from Create.vue or Edit.vue
  campaigns: Array, // Prop for campaigns data
  platforms: Array, // Prop for platforms data
  contentTypes: Array, // Prop for contentTypes data
  statuses: Array, // Prop for statuses data
});

const formattedScheduledPostDate = computed({
  get: () => {
    return props.form.scheduled_post_date ? format(new Date(props.form.scheduled_post_date), "yyyy-MM-dd'T'HH:mm") : '';
  },
  set: (value) => {
    props.form.scheduled_post_date = value;
  },
});

const formattedActualPostDate = computed({
  get: () => {
    return props.form.actual_post_date ? format(new Date(props.form.actual_post_date), "yyyy-MM-dd'T'HH:mm") : '';
  },
  set: (value) => {
    props.form.actual_post_date = value;
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
        class="mt-1 block w-full shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 p-2.5"
        v-model="props.form.title"
        required
        autofocus
      />
      <InputError class="mt-2" :message="props.form.errors.title" />
    </div>

    <div class="col-span-6 sm:col-span-3">
      <label for="campaign_id" class="block text-sm font-medium text-gray-900 dark:text-white">Campaign</label>
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

    <div class="col-span-6 sm:col-span-3">
      <label for="platform_id" class="block text-sm font-medium text-gray-900 dark:text-white">Platform</label>
      <select
        id="platform_id"
        v-model="props.form.platform_id"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
      >
        <option value="">Select a Platform</option>
        <option v-for="platform in props.platforms" :key="platform.id" :value="platform.id">{{ platform.name }}</option>
      </select>
      <InputError class="mt-2" :message="props.form.errors.platform_id" />
    </div>

    <div class="col-span-6 sm:col-span-3">
      <label for="content_type" class="block text-sm font-medium text-gray-900 dark:text-white">Content Type</label>
      <select
        id="content_type"
        v-model="props.form.content_type"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
        required
      >
        <option value="">Select a Type</option>
        <option v-for="type in props.contentTypes" :key="type" :value="type">{{ type }}</option>
      </select>
      <InputError class="mt-2" :message="props.form.errors.content_type" />
    </div>

    <div class="col-span-6 sm:col-span-3">
      <label for="media_url" class="block text-sm font-medium text-gray-900 dark:text-white">Media URL</label>
      <TextInput
        id="media_url"
        type="url"
        class="mt-1 block w-full shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 p-2.5"
        v-model="props.form.media_url"
      />
      <InputError class="mt-2" :message="props.form.errors.media_url" />
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
      <label for="scheduled_post_date" class="block text-sm font-medium text-gray-900 dark:text-white">Scheduled Post Date</label>
      <TextInput
        id="scheduled_post_date"
        type="datetime-local"
        class="mt-1 block w-full shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 p-2.5"
        v-model="formattedScheduledPostDate"
      />
      <InputError class="mt-2" :message="props.form.errors.scheduled_post_date" />
    </div>

    <div class="col-span-6 sm:col-span-3">
      <label for="actual_post_date" class="block text-sm font-medium text-gray-900 dark:text-white">Actual Post Date</label>
      <TextInput
        id="actual_post_date"
        type="datetime-local"
        class="mt-1 block w-full shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 p-2.5"
        v-model="formattedActualPostDate"
      />
      <InputError class="mt-2" :message="props.form.errors.actual_post_date" />
    </div>

    <div class="col-span-full">
      <label for="description" class="block text-sm font-medium text-gray-900 dark:text-white">Description</label>
      <textarea
        id="description"
        rows="6"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white bg-gray-50 p-2.5"
        v-model="props.form.description"
      ></textarea>
      <InputError class="mt-2" :message="props.form.errors.description" />
    </div>

    <div class="col-span-full">
      <label for="engagement_metrics" class="block text-sm font-medium text-gray-900 dark:text-white">Engagement Metrics (JSON)</label>
      <textarea
        id="engagement_metrics"
        rows="6"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white bg-gray-50 p-2.5"
        v-model="props.form.engagement_metrics"
      ></textarea>
      <InputError class="mt-2" :message="props.form.errors.engagement_metrics" />
    </div>
  </div>
</template>
