<template>
  <Head title="Edit Referral Document" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header: Back button removed -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div>
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Referral Document</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Update referral document information below.</p>
          </div>
          <!-- intentionally no Back button here -->
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <ReferralDocumentForm
          :referralDocument="props.referralDocument"
          :referrals="props.referrals"
          :staff="props.staff"
          :editMode="true"
        />
        <div class="mt-4 flex justify-end">
          <Link :href="route('admin.referral-documents.index')" class="btn-glass btn-glass-sm">Cancel</Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'
import ReferralDocumentForm from '@/components/ReferralDocumentForm.vue'

const props = defineProps({
  referralDocument: Object,
  referrals: Array,
  staff: Array,
})

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Referral Documents', href: route('admin.referral-documents.index') },
  { title: `Edit: ${props.referralDocument?.document_name ?? ''}`, href: '#' },
]
</script>
