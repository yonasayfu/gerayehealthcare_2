<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { ArrowLeft, Printer, Download } from 'lucide-vue-next'

const props = defineProps<{
  medicalDocument: {
    id: number
    title: string | null
    document_type: string
    document_date: string | null
    summary: string | null
    file_path?: string | null
    patient?: { id: number; full_name?: string }
  }
}>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Medical Documents', href: route('admin.medical-documents.index') },
  { title: 'Details', href: null },
]
</script>

<template>
  <Head title="Medical Document Details" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <div class="liquidGlass-wrapper">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content p-4 space-y-4">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Medical Document</h1>
              <p class="text-sm text-gray-600 dark:text-gray-300">Details and attached file</p>
            </div>
            <div class="flex items-center gap-2">
              <Link :href="route('admin.medical-documents.index')" class="btn-glass btn-glass-sm">
                <ArrowLeft class="icon" />
                <span class="hidden sm:inline">Back</span>
              </Link>
              <Link :href="route('admin.medical-documents.printSingle', props.medicalDocument.id)" class="btn-glass btn-glass-sm" target="_blank">
                <Printer class="icon" />
                <span class="hidden sm:inline">Print</span>
              </Link>
              <Link :href="route('admin.medical-documents.export')" class="btn-glass btn-glass-sm" target="_blank">
                <Download class="icon" />
                <span class="hidden sm:inline">Export All</span>
              </Link>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <div class="text-sm text-gray-500">Title</div>
              <div class="font-medium">{{ props.medicalDocument.title || '-' }}</div>
            </div>
            <div>
              <div class="text-sm text-gray-500">Type</div>
              <div class="font-medium">{{ props.medicalDocument.document_type }}</div>
            </div>
            <div>
              <div class="text-sm text-gray-500">Date</div>
              <div class="font-medium">{{ props.medicalDocument.document_date || '-' }}</div>
            </div>
            <div>
              <div class="text-sm text-gray-500">Patient</div>
              <div class="font-medium">{{ props.medicalDocument.patient?.full_name || props.medicalDocument.patient?.id || '-' }}</div>
            </div>
          </div>

          <div>
            <div class="text-sm text-gray-500">Summary</div>
            <div class="whitespace-pre-line">{{ props.medicalDocument.summary || '-' }}</div>
          </div>

          <div class="pt-2">
            <div class="text-sm text-gray-500">Attached File</div>
            <div v-if="props.medicalDocument.file_path">
              <a :href="`/storage/${props.medicalDocument.file_path}`" target="_blank" class="underline text-primary-600">View / Download</a>
            </div>
            <div v-else class="text-gray-500">No file uploaded</div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
