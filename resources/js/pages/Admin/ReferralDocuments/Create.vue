<template>
  <Head title="Create Referral Document" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6">
      <div class="max-w-7xl mx-auto">
        <div class="bg-white dark:bg-gray-900 overflow-hidden shadow-sm rounded-lg">
          <div class="p-4">
            <ReferralDocumentForm :referrals="referrals" :staff="staff" />
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
  referrals: Array,
  staff: Array,
})

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Referral Documents', href: route('admin.referral-documents.index') },
  { title: 'Create', href: '#' },
]
</script>
