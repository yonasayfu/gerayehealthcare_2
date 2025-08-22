<template>
  <Head :title="`Employee Insurance Record: ${employeeInsuranceRecord.patient?.full_name || 'Record'}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="bg-white border border-4 rounded-lg shadow relative m-10">

      <div class="flex items-start justify-between p-5 border-b rounded-t print:hidden">
        <h3 class="text-xl font-semibold">
          Employee Insurance Record Details: {{ employeeInsuranceRecord.patient?.full_name || 'Record' }}
        </h3>
        <Link :href="route('admin.employee-insurance-records.index')" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
        </Link>
      </div>

      <div class="p-6 space-y-6">
        <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-8 space-y-8 print:shadow-none print:rounded-none print:p-0 print:m-0 print:w-auto print:h-auto print:flex-shrink-0">

          <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
            <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
            <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
            <p class="text-gray-600 dark:text-gray-400 print-document-title">Employee Insurance Record Document</p>
            <hr class="my-3 border-gray-300 print:my-2">
          </div>

          <div class="border-b pb-4 mb-4 print:pb-2 print:mb-2">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 print:mb-2">Record Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
              <div>
                <p class="text-sm text-muted-foreground">Patient:</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ employeeInsuranceRecord.patient?.full_name ?? '-' }}</p>
              </div>
              <div>
                <p class="text-sm text-muted-foreground">Policy:</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ employeeInsuranceRecord.policy?.service_type }}<span v-if="employeeInsuranceRecord.policy?.coverage_percentage"> ({{ employeeInsuranceRecord.policy?.coverage_percentage }}%)</span></p>
              </div>
              <div>
                <p class="text-sm text-muted-foreground">Kebele ID:</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ employeeInsuranceRecord.kebele_id ?? '-' }}</p>
              </div>
              <div>
                <p class="text-sm text-muted-foreground">Woreda:</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ employeeInsuranceRecord.woreda ?? '-' }}</p>
              </div>
              <div>
                <p class="text-sm text-muted-foreground">Region:</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ employeeInsuranceRecord.region ?? '-' }}</p>
              </div>
              <div>
                <p class="text-sm text-muted-foreground">Fayda ID (Federal ID):</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ employeeInsuranceRecord.federal_id ?? '-' }}</p>
              </div>
              <div>
                <p class="text-sm text-muted-foreground">Ministry / Department:</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ employeeInsuranceRecord.ministry_department ?? '-' }}</p>
              </div>
              <div>
                <p class="text-sm text-muted-foreground">Employee ID Number:</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ employeeInsuranceRecord.employee_id_number ?? '-' }}</p>
              </div>
              <div>
                <p class="text-sm text-muted-foreground">Verified:</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ employeeInsuranceRecord.verified ? 'Yes' : 'No' }}</p>
              </div>
              <div>
                <p class="text-sm text-muted-foreground">Verified At:</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ employeeInsuranceRecord.verified_at ? new Date(employeeInsuranceRecord.verified_at).toLocaleString() : 'N/A' }}</p>
              </div>
              <div>
                <p class="text-sm text-muted-foreground">Created At:</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ employeeInsuranceRecord.created_at ? new Date(employeeInsuranceRecord.created_at).toLocaleString() : '-' }}</p>
              </div>
              <div>
                <p class="text-sm text-muted-foreground">Updated At:</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ employeeInsuranceRecord.updated_at ? new Date(employeeInsuranceRecord.updated_at).toLocaleString() : '-' }}</p>
              </div>
            </div>
          </div>

        </div>
      </div>

      <div class="p-6 border-t border-gray-200 rounded-b print:hidden">
        <div class="flex flex-wrap gap-2">
          <Link :href="route('admin.employee-insurance-records.index')" class="btn btn-outline">
            Back to List
          </Link>
          <Link :href="route('admin.employee-insurance-records.edit', employeeInsuranceRecord.id)" class="btn btn-primary">
            Edit Record
          </Link>
        </div>
      </div>

      <div class="hidden print:block text-center mt-4 text-sm text-gray-500">
        <hr class="my-2 border-gray-300">
        <p>Printed on: {{ format(new Date(), 'PPP p') }}</p>
      </div>

    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import type { BreadcrumbItemType, EmployeeInsuranceRecord } from '@/types';
import { format } from 'date-fns';
import { confirmDialog } from '@/lib/confirm';

const props = defineProps<{ employeeInsuranceRecord: EmployeeInsuranceRecord }>();

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Employee Insurance Records', href: route('admin.employee-insurance-records.index') },
  { title: props.employeeInsuranceRecord.patient?.full_name || 'Record', href: route('admin.employee-insurance-records.show', props.employeeInsuranceRecord.id) },
];

// print functionality removed for Employee Insurance Records module

async function destroy(id: number) {
  const ok = await confirmDialog({
    title: 'Delete Employee Insurance Record',
    message: 'Are you sure you want to delete this record?',
    confirmText: 'Delete',
    cancelText: 'Cancel',
  })
  if (!ok) return
  router.delete(route('admin.employee-insurance-records.destroy', id));
}

</script>

<style>
/* Optimized Print Styles for A4 */
@media print {
  @page {
    size: A4;
    margin: 0.5cm;
  }

  body {
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
    color: #000 !important;
    margin: 0 !important;
    padding: 0 !important;
    overflow: hidden !important;
  }

  .print\:hidden { display: none !important; }
  .app-sidebar-header, .app-sidebar { display: none !important; }
  body > header, body > nav, [role="banner"], [role="navigation"] { display: none !important; }

  .hidden.print\:block { display: block !important; }

  .print-header-content {
    padding-top: 0.5cm !important;
    padding-bottom: 0.5cm !important;
    margin-bottom: 0.8cm !important;
  }
  .print-logo {
    max-width: 150px;
    max-height: 50px;
    margin-bottom: 0.5rem;
    display: block;
    margin-left: auto;
    margin-right: auto;
  }
  .print-clinic-name { font-size: 1.8rem !important; margin-bottom: 0.2rem !important; line-height: 1.2 !important; }
  .print-document-title { font-size: 0.9rem !important; color: #555 !important; }

  .bg-white.dark\:bg-gray-900.shadow.rounded-lg {
    box-shadow: none !important;
    border-radius: 0 !important;
    border: none !important;
    padding: 1cm !important;
    margin: 0 !important;
    width: 100% !important;
    height: auto !important;
    overflow: visible !important;
    transform: scale(0.98);
    transform-origin: top left;
  }

  .p-6.space-y-6, .p-6 { padding: 0 !important; margin: 0 !important; }

  h2 { font-size: 1.3rem !important; margin-bottom: 0.6rem !important; }
  p { font-size: 0.85rem !important; line-height: 1.4 !important; }
  .text-sm { font-size: 0.8rem !important; }
  .text-xs { font-size: 0.75rem !important; }
  .font-medium { font-weight: 600 !important; }

  .border-b { border-bottom: 1px solid #ddd !important; padding-bottom: 0.7rem !important; margin-bottom: 0.7rem !important; }

  .grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; gap: 0.8rem 0 !important; page-break-inside: avoid !important; }
  .grid > div { display: flex !important; flex-direction: row !important; align-items: baseline !important; gap: 0.4rem !important; padding: 0.1rem 0 !important; }
  .grid > div:nth-child(odd) { border-right: 1px dashed #eee !important; padding-right: 1.5rem !important; }
  .grid > div:nth-child(even) { padding-left: 1.5rem !important; }
  .grid > div p:first-child { font-weight: 600 !important; color: #000 !important; flex-shrink: 0 !important; }
  .grid > div p:last-child { flex-grow: 1 !important; white-space: normal !important; word-break: break-word !important; color: #333 !important; }
}
</style>
