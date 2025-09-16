<template>
  <Head :title="`Referral Document: ${referralDocument.document_name}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow relative">
        <ShowHeader title="Referral Document" :subtitle="`Details for ${referralDocument.document_name}`">
          <template #actions>
            <Link :href="route('admin.referral-documents.index')" class="btn-glass btn-glass-sm">Back</Link>
          </template>
        </ShowHeader>
      </div>

    
      <!-- Print-only Footer (Patient module style) -->
      <div class="hidden print:block text-center mt-4 text-sm text-gray-500">
        <hr class="my-2 border-gray-300">
        <p>Printed on: {{ format(new Date(), 'PPP p') }}</p>
      </div>

      <!-- Print-only Header -->
      <div class="hidden print:block print-header-content">
        <img :src="getClinicLogo()" :alt="getClinicName()" class="print-logo" />
        <div class="print-clinic-name">{{ getClinicName() }}</div>
        <div class="print-document-title">Referral Document: {{ referralDocument.document_name }}</div>
        <hr />
      </div>

      <!-- Content Card -->
      <div class="bg-white dark:bg-gray-900 overflow-hidden shadow rounded-lg print:shadow-none print:rounded-none print:bg-white print:p-8">
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <div class="text-xs text-muted-foreground uppercase">Referral</div>
            <div class="mt-1 font-medium">{{ referralDocument.referral ? referralDocument.referral.referred_patient_id : '—' }}</div>
          </div>
          <div>
            <div class="text-xs text-muted-foreground uppercase">Document Name</div>
            <div class="mt-1 font-medium">{{ referralDocument.document_name }}</div>
          </div>
          <div>
            <div class="text-xs text-muted-foreground uppercase">Document Type</div>
            <div class="mt-1">
              <span :class="['inline-flex items-center rounded-full px-2 py-0.5 text-xs', typeBadgeClass(referralDocument.document_type)]">
                {{ referralDocument.document_type || '—' }}
              </span>
            </div>
          </div>
          <div>
            <div class="text-xs text-muted-foreground uppercase">Status</div>
            <div class="mt-1">
              <span :class="['inline-flex items-center rounded-full px-2 py-0.5 text-xs', statusBadgeClass(referralDocument.status)]">{{ referralDocument.status }}</span>
            </div>
          </div>
          <div>
            <div class="text-xs text-muted-foreground uppercase">Uploaded By</div>
            <div class="mt-1 font-medium">{{ referralDocument.uploaded_by?.full_name || '—' }}</div>
          </div>
          <div>
            <div class="text-xs text-muted-foreground uppercase">Uploaded At</div>
            <div class="mt-1 font-medium">{{ new Date(referralDocument.created_at).toLocaleString() }}</div>
          </div>
          <div class="md:col-span-2">
            <div class="text-xs text-muted-foreground uppercase">Document</div>
            <div class="mt-1">
              <a :href="`/storage/${referralDocument.document_path}`" target="_blank" class="text-blue-600 hover:underline">
                View attached file
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
      <!-- Footer Actions (below content, Patient module style) -->
            <!-- footer actions (single source of actions, right aligned) -->
      <div class="p-6 border-t border-gray-200 dark:border-gray-700 rounded-b print:hidden">
        <div class="flex justify-end gap-2">
          
          <button @click="printSingleReferralDocument" class="btn-glass btn-glass-sm">Print Current</button>
          <Link :href="route('admin.referral-documents.edit', item.id)" class="btn-glass btn-glass-sm">Edit</Link>
        </div>
      </div>

  </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { confirmDialog } from '@/lib/confirm'
import { useClinicInfo } from '@/composables/useClinicInfo'
import { Printer } from 'lucide-vue-next'
import { format } from 'date-fns'
import ShowHeader from '@/components/ShowHeader.vue'

const props = defineProps({
  referralDocument: Object,
})

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Referral Documents', href: route('admin.referral-documents.index') },
  { title: `Show: ${props.referralDocument?.document_name ?? ''}`, href: '#' },
]

const form = useForm({})

const { getClinicName, getClinicLogo, getPrintFooterText } = useClinicInfo()

const deleteDocument = async () => {
  const ok = await confirmDialog({
    title: 'Delete Referral Document',
    message: 'Are you sure you want to delete this referral document?',
    confirmText: 'Delete',
    cancelText: 'Cancel',
  })
  if (!ok) return
  form.delete(route('admin.referral-documents.destroy', props.referralDocument.id))
}

const printCurrent = () => {
  window.print()
}

const statusBadgeClass = (status) => {
  switch ((status || '').toLowerCase()) {
    case 'uploaded':
      return 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'
    case 'sent':
      return 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'
    case 'received':
      return 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300'
    case 'reviewed':
      return 'bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-300'
    default:
      return 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'
  }
}

const typeBadgeClass = (type) => {
  const t = (type || '').toLowerCase()
  if (t.includes('prescription')) return 'bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300'
  if (t.includes('lab')) return 'bg-cyan-50 text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-300'
  if (t.includes('imaging')) return 'bg-pink-50 text-pink-700 dark:bg-pink-900/30 dark:text-pink-300'
  if (t.includes('clinical')) return 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'
  return 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'
}
</script>
