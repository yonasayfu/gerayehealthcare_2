<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import InputLabel from '@/components/ui/label/Label.vue'
import TextInput from '@/components/ui/input/Input.vue'
import InputError from '@/components/InputError.vue'
import PrimaryButton from '@/components/ui/button/Button.vue'

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Campaign Contents', href: route('admin.campaign-contents.index') },
  { title: 'Create', href: route('admin.campaign-contents.create') },
]

const form = useForm({
  campaign_id: '',
  platform_id: '',
  content_type: '',
  title: '',
  description: '',
  media_url: '',
  scheduled_post_date: '',
  actual_post_date: '',
  status: 'Draft',
  engagement_metrics: '',
});

const submit = () => {
  form.post(route('admin.campaign-contents.store'));
};

// Dummy data for select options (replace with actual data from props if available)
const campaigns = [
  { id: 1, campaign_name: 'Summer Sale 2024' },
  { id: 2, campaign_name: 'New Patient Drive' },
];

const platforms = [
  { id: 1, name: 'TikTok' },
  { id: 2, name: 'Meta' },
  { id: 3, name: 'Google' },
];

const contentTypes = [
  'Post',
  'Video',
  'Ad Creative',
  'Blog Post',
  'Infographic',
];

const statuses = [
  'Draft',
  'Scheduled',
  'Posted',
  'Archived',
];
</script>

<template>
  <Head title="Create Campaign Content" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Create Campaign Content</h1>
        <p class="text-sm text-muted-foreground">Fill in the details to create new campaign content.</p>
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
          <InputLabel for="platform_id" value="Platform" />
          <select
            id="platform_id"
            v-model="form.platform_id"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
          >
            <option value="">Select a Platform</option>
            <option v-for="platform in platforms" :key="platform.id" :value="platform.id">{{ platform.name }}</option>
          </select>
          <InputError class="mt-2" :message="form.errors.platform_id" />
        </div>

        <div>
          <InputLabel for="content_type" value="Content Type" />
          <select
            id="content_type"
            v-model="form.content_type"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
            required
          >
            <option value="">Select a Type</option>
            <option v-for="type in contentTypes" :key="type" :value="type">{{ type }}</option>
          </select>
          <InputError class="mt-2" :message="form.errors.content_type" />
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
          <InputLabel for="media_url" value="Media URL" />
          <TextInput
            id="media_url"
            type="url"
            class="mt-1 block w-full"
            v-model="form.media_url"
          />
          <InputError class="mt-2" :message="form.errors.media_url" />
        </div>

        <div>
          <InputLabel for="scheduled_post_date" value="Scheduled Post Date" />
          <TextInput
            id="scheduled_post_date"
            type="datetime-local"
            class="mt-1 block w-full"
            v-model="form.scheduled_post_date"
          />
          <InputError class="mt-2" :message="form.errors.scheduled_post_date" />
        </div>

        <div>
          <InputLabel for="actual_post_date" value="Actual Post Date" />
          <TextInput
            id="actual_post_date"
            type="datetime-local"
            class="mt-1 block w-full"
            v-model="form.actual_post_date"
          />
          <InputError class="mt-2" :message="form.errors.actual_post_date" />
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
          <InputLabel for="engagement_metrics" value="Engagement Metrics (JSON)" />
          <textarea
            id="engagement_metrics"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
            v-model="form.engagement_metrics"
          ></textarea>
          <InputError class="mt-2" :message="form.errors.engagement_metrics" />
        </div>

        <div class="flex items-center justify-end">
          <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
            Create Content
          </PrimaryButton>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
