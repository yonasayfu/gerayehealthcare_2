<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Form from './Form.vue'
import type { BreadcrumbItemType } from '@/types'

const props = defineProps<{
  campaigns: any[];
}>()

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Landing Pages', href: route('admin.landing-pages.index') },
  { title: 'Create', href: route('admin.landing-pages.create') },
]

const form = useForm<any>({
  page_title: null,
  page_url: null,
  template_used: null,
  language: null,
  conversion_goal: null,
  views: 0,
  submissions: 0,
  conversion_rate: 0,
  is_active: true,
  campaign_id: null,
  form_fields: {},
  notes: null,
})

function submit() {
  form.post(route('admin.landing-pages.store'))
}
</script>

<template>
  <Head title="Create New Landing Page" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header (no Back button here) -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div class="print:hidden">
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Create New Landing Page</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Fill required information to create a landing page.</p>
          </div>
        </div>
      </div>

      <!-- Form card -->
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <Form :form="form" v-bind="$props" />
          <!-- Footer actions: Cancel + Create (right aligned, no logic change) -->
          <div class="flex justify-end gap-2 pt-2">
            <Link :href="route('admin.landing-pages.index')" class="btn-glass btn-glass-sm">Cancel</Link>

            <button
              type="submit"
              :disabled="form.processing"
              class="btn-glass btn-glass-sm"
            >
              {{ form.processing ? 'Creating...' : 'Create Landing Page' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>