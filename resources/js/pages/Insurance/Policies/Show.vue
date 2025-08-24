<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Printer, Edit3 } from 'lucide-vue-next'
import type { BreadcrumbItemType } from '@/types'
import { format } from 'date-fns'

const props = defineProps<{
  insurancePolicy: any,
}>()

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Insurance', href: route('admin.insurance-policies.index') },
  { title: 'Policies', href: route('admin.insurance-policies.index') },
  { title: 'Details', href: route('admin.insurance-policies.show', props.insurancePolicy.id) },
]

function printSinglePolicy() {
  window.print()
}
</script>

<template>
  <Head :title="`Insurance Policy`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6 print:p-0 print:space-y-0">

      <div class="liquidGlass-wrapper relative overflow-hidden rounded-xl p-5 print:hidden">
        <div class="absolute inset-0 pointer-events-none bg-gradient-to-br from-white/10 to-white/5"></div>
        <div class="relative flex items-center justify-between">
          <div>
            <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Insurance Policy Details</h1>
            <p class="text-sm text-muted-foreground">Full coverage and meta information</p>
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-8 space-y-8 print:shadow-none print:rounded-none print:p-0 print:m-0 print:w-auto print:h-auto print:flex-shrink-0">

        <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
          <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
          <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
          <p class="text-gray-600 dark:text-gray-400 print-document-title">Insurance Policy Document</p>
        </div>

        <div class="border-b pb-4 mb-4 print:pb-2 print:mb-2">
          <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 print:mb-2">Associations</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
            <div>
              <p class="text-sm text-muted-foreground">Insurance Company:</p>
              <p class="font-medium text-gray-900 dark:text-white">{{ insurancePolicy.insurance_company?.name || '-' }}</p>
            </div>
            <div>
              <p class="text-sm text-muted-foreground">Corporate Client:</p>
              <p class="font-medium text-gray-900 dark:text-white">{{ insurancePolicy.corporate_client?.name ?? insurancePolicy.corporate_client?.organization_name ?? '-' }}</p>
            </div>
          </div>
        </div>

        <div class="border-b pb-4 mb-4 print:pb-2 print:mb-2">
          <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 print:mb-2">Coverage Details</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
            <div>
              <p class="text-sm text-muted-foreground">Service Type:</p>
              <p class="font-medium text-gray-900 dark:text-white">{{ insurancePolicy.service_type || '-' }}</p>
            </div>
            <div>
              <p class="text-sm text-muted-foreground">Coverage Percentage:</p>
              <p class="font-medium text-gray-900 dark:text-white">{{ insurancePolicy.coverage_percentage ?? '-' }}%</p>
            </div>
            <div>
              <p class="text-sm text-muted-foreground">Coverage Type:</p>
              <p class="font-medium text-gray-900 dark:text-white">{{ insurancePolicy.coverage_type || '-' }}</p>
            </div>
            <div>
              <p class="text-sm text-muted-foreground">Active:</p>
              <p class="font-medium text-gray-900 dark:text-white">{{ insurancePolicy.is_active ? 'Yes' : 'No' }}</p>
            </div>
          </div>
        </div>

        <div>
          <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 print:mb-2">Meta</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
            <div>
              <p class="text-sm text-muted-foreground">Notes:</p>
              <p class="font-medium text-gray-900 dark:text-white">{{ insurancePolicy.notes || '-' }}</p>
            </div>
            <div>
              <p class="text-sm text-muted-foreground">Created At:</p>
              <p class="font-medium text-gray-900 dark:text-white">{{ insurancePolicy.created_at ? format(new Date(insurancePolicy.created_at), 'PPP p') : '-' }}</p>
            </div>
            <div>
              <p class="text-sm text-muted-foreground">Updated At:</p>
              <p class="font-medium text-gray-900 dark:text-white">{{ insurancePolicy.updated_at ? format(new Date(insurancePolicy.updated_at), 'PPP p') : '-' }}</p>
            </div>
          </div>
        </div>

      </div>

      <div class="p-6 border-t border-gray-200 rounded-b print:hidden">
        <div class="flex items-center justify-end flex-wrap gap-2">
          <Link :href="route('admin.insurance-policies.index')" class="btn-glass btn-glass-sm">Back to List</Link>
          <Link :href="route('admin.insurance-policies.edit', insurancePolicy.id)" class="btn-glass btn-glass-sm">
            <Edit3 class="h-4 w-4" /> Edit Policy
          </Link>
          <button @click="printSinglePolicy" class="btn-glass btn-glass-sm">
            <Printer class="h-4 w-4" /> Print Current
          </button>
        </div>
      </div>

      <div class="hidden print:block text-center mt-4 text-sm text-gray-500 print-footer">
        <p>Printed on: {{ format(new Date(), 'PPP p') }}</p>
      </div>
    </div>

  </AppLayout>
</template>

<style>
@page { size: A4 portrait; margin: 12mm; }
@media print {
  html, body { background: #fff !important; }
  .print-header-content { page-break-inside: avoid; }
  .print-logo { display: inline-block; margin: 0 auto 6px auto; max-width: 100%; height: auto; }
  .print-clinic-name { font-size: 16px; margin: 0; }
  .print-document-title { font-size: 12px; margin: 2px 0 0 0; }
  table { border-collapse: collapse; }
  hr { display: none !important; }
  .print-footer { position: fixed; bottom: 0; left: 0; right: 0; background: #fff; box-shadow: none !important; }
}
</style>
