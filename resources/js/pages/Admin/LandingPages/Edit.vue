<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Form from './Form.vue'
import FormActions from '@/components/FormActions.vue'
import type { BreadcrumbItemType, LandingPage } from '@/types'

const props = defineProps<{
  landingPage: LandingPage;
  campaigns: any[];
}>()

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Landing Pages', href: route('admin.landing-pages.index') },
  { title: 'Edit', href: route('admin.landing-pages.edit', props.landingPage.id) },
]

const form = useForm<any>({
  page_title: props.landingPage.page_title,
  page_url: props.landingPage.page_url,
  template_used: props.landingPage.template_used,
  language: props.landingPage.language,
  conversion_goal: props.landingPage.conversion_goal,
  views: props.landingPage.views,
  submissions: props.landingPage.submissions,
  conversion_rate: props.landingPage.conversion_rate,
  is_active: props.landingPage.is_active,
  campaign_id: props.landingPage.campaign_id,
  form_fields: props.landingPage.form_fields || {},
  notes: props.landingPage.notes,
})

function submit() {
  form.put(route('admin.landing-pages.update', props.landingPage.id))
}
</script>

<template>
  <Head title="Edit Landing Page" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header: Back button removed -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div>
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Landing Page</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Update landing page information below.</p>
          </div>
          <!-- intentionally no Back button here -->
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <Form :form="form" v-bind="$props" />

          <FormActions :cancel-href="route('admin.landing-pages.index')" submit-text="Save Changes" :processing="form.processing" />
        </form>
      </div>
    </div>
  </AppLayout>
</template>
