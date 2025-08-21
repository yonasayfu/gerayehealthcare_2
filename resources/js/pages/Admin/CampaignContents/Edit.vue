<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Form from './Form.vue' // Import the new Form component

interface CampaignContent {
  id: number;
  title: string;
  description: string;
  media_url: string;
  content_type: string;
  status: string;
  scheduled_post_date: string;
  actual_post_date: string;
  engagement_metrics: Record<string, any>;
  campaign_id: number;
  platform_id: number;
}

const props = defineProps<{
  campaignContent: CampaignContent;
  campaigns: Array<any>;
  platforms: Array<any>;
  contentTypes: Array<string>;
  statuses: Array<string>;
}>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Campaign Contents', href: route('admin.campaign-contents.index') },
  { title: props.campaignContent.title, href: route('admin.campaign-contents.show', props.campaignContent.id) },
  { title: 'Edit', href: route('admin.campaign-contents.edit', props.campaignContent.id) },
]

const form = useForm({
  campaign_id: props.campaignContent.campaign_id,
  platform_id: props.campaignContent.platform_id,
  content_type: props.campaignContent.content_type,
  title: props.campaignContent.title,
  description: props.campaignContent.description,
  media_url: props.campaignContent.media_url,
  scheduled_post_date: props.campaignContent.scheduled_post_date,
  actual_post_date: props.campaignContent.actual_post_date,
  status: props.campaignContent.status,
  engagement_metrics: JSON.stringify(props.campaignContent.engagement_metrics, null, 2),
});

const submit = () => {
  form.put(route('admin.campaign-contents.update', props.campaignContent.id));
};
</script>

<template>
  <Head title="Edit Campaign Content" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header: Back button removed -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div>
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Campaign Content</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Update campaign content information below.</p>
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
              :href="route('admin.campaign-contents.index')"
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