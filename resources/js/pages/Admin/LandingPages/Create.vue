<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import InputLabel from '@/components/ui/label/Label.vue'
import TextInput from '@/components/ui/input/Input.vue'
import InputError from '@/components/InputError.vue'
import PrimaryButton from '@/components/ui/button/Button.vue'

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Landing Pages', href: route('admin.landing-pages.index') },
  { title: 'Create', href: route('admin.landing-pages.create') },
]

const form = useForm({
  page_title: '',
  page_url: '',
  campaign_id: '',
  template_used: '',
  language: 'en',
  form_fields: '',
  conversion_goal: '',
  views: 0,
  submissions: 0,
  conversion_rate: 0,
  is_active: true,
});

const submit = () => {
  form.post(route('admin.landing-pages.store'));
};

// Dummy data for select options (replace with actual data from props if available)
const campaigns = [
  { id: 1, campaign_name: 'Summer Sale 2024' },
  { id: 2, campaign_name: 'New Patient Drive' },
];

const languages = [
  'en',
  'es',
  'fr',
  'am',
];

const conversionGoals = [
  'Form Submission',
  'Call',
  'Download',
  'Purchase',
];
</script>

<template>
  <Head title="Create Landing Page" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Create Landing Page</h1>
        <p class="text-sm text-muted-foreground">Fill in the details to create a new landing page.</p>
      </div>

      <form @submit.prevent="submit" class="space-y-6 bg-white dark:bg-gray-900 p-6 rounded-lg shadow">
        <div>
          <InputLabel for="page_title" value="Page Title" />
          <TextInput
            id="page_title"
            type="text"
            class="mt-1 block w-full"
            v-model="form.page_title"
            required
            autofocus
          />
          <InputError class="mt-2" :message="form.errors.page_title" />
        </div>

        <div>
          <InputLabel for="page_url" value="Page URL" />
          <TextInput
            id="page_url"
            type="url"
            class="mt-1 block w-full"
            v-model="form.page_url"
            required
          />
          <InputError class="mt-2" :message="form.errors.page_url" />
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
          <InputLabel for="template_used" value="Template Used" />
          <TextInput
            id="template_used"
            type="text"
            class="mt-1 block w-full"
            v-model="form.template_used"
          />
          <InputError class="mt-2" :message="form.errors.template_used" />
        </div>

        <div>
          <InputLabel for="language" value="Language" />
          <select
            id="language"
            v-model="form.language"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
            required
          >
            <option v-for="lang in languages" :key="lang" :value="lang">{{ lang }}</option>
          </select>
          <InputError class="mt-2" :message="form.errors.language" />
        </div>

        <div>
          <InputLabel for="form_fields" value="Form Fields (JSON)" />
          <textarea
            id="form_fields"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
            v-model="form.form_fields"
          ></textarea>
          <InputError class="mt-2" :message="form.errors.form_fields" />
        </div>

        <div>
          <InputLabel for="conversion_goal" value="Conversion Goal" />
          <select
            id="conversion_goal"
            v-model="form.conversion_goal"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
          >
            <option value="">Select a Goal</option>
            <option v-for="goal in conversionGoals" :key="goal" :value="goal">{{ goal }}</option>
          </select>
          <InputError class="mt-2" :message="form.errors.conversion_goal" />
        </div>

        <div>
          <InputLabel for="views" value="Views" />
          <TextInput
            id="views"
            type="number"
            class="mt-1 block w-full"
            v-model="form.views"
          />
          <InputError class="mt-2" :message="form.errors.views" />
        </div>

        <div>
          <InputLabel for="submissions" value="Submissions" />
          <TextInput
            id="submissions"
            type="number"
            class="mt-1 block w-full"
            v-model="form.submissions"
          />
          <InputError class="mt-2" :message="form.errors.submissions" />
        </div>

        <div>
          <InputLabel for="conversion_rate" value="Conversion Rate" />
          <TextInput
            id="conversion_rate"
            type="number"
            step="0.01"
            class="mt-1 block w-full"
            v-model="form.conversion_rate"
          />
          <InputError class="mt-2" :message="form.errors.conversion_rate" />
        </div>

        <div class="flex items-center gap-2">
          <input type="checkbox" id="is_active" v-model="form.is_active" class="rounded border-gray-300 text-cyan-600 shadow-sm focus:ring-cyan-500" />
          <InputLabel for="is_active" value="Is Active" />
          <InputError class="mt-2" :message="form.errors.is_active" />
        </div>

        <div class="flex items-center justify-end">
          <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
            Create Landing Page
          </PrimaryButton>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
