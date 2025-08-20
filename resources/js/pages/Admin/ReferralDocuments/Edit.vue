<template>
  <Head title="Edit Referral Document" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6">
      <div class="max-w-7xl mx-auto">
        <div class="bg-white dark:bg-gray-900 overflow-hidden shadow-sm rounded-lg">
          <div class="p-4">
            <ReferralDocumentForm
              :referralDocument="referralDocument"
              :referrals="referrals"
              :staff="staff"
              editMode
            />
          </div>
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
