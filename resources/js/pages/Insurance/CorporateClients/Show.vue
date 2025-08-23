<template>
  <Head :title="`Corporate Client: ${corporateClient.organization_name}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6 print:p-0 print:space-y-0">
      <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
        <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
        <h1 class="font-bold text-gray-800 print-clinic-name">Geraye Home Care Services</h1>
        <p class="text-gray-600 print-document-title">Corporate Client Record</p>
        <hr class="my-3 border-gray-300 print:my-2">
      </div>

      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div>
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Corporate Client: {{ corporateClient.organization_name }}</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Record details</p>
          </div>
          <!-- intentionally no Back button here -->
        </div>
      </div>

      <div class="bg-white dark:bg-gray-900 overflow-hidden shadow rounded-lg">
        <div class="p-6 space-y-6">
          <div class="overflow-x-auto bg-white dark:bg-gray-900 rounded-lg p-8 space-y-8 print:shadow-none print:rounded-none print:p-0 print:m-0 print:w-auto print:h-auto print:flex-shrink-0">
            <div class="border-b pb-4 mb-4 print:pb-2 print:mb-2 avoid-break">
              <h2 class="text-lg font-semibold text-gray-800 mb-4 print:mb-2">Client Details</h2>
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
                <div>
                  <p class="text-sm text-muted-foreground">Organization Name:</p>
                  <p class="font-medium text-gray-900">{{ corporateClient.organization_name ?? '-' }}</p>
                </div>
                <div>
                  <p class="text-sm text-muted-foreground">Contact Person:</p>
                  <p class="font-medium text-gray-900">{{ corporateClient.contact_person ?? '-' }}</p>
                </div>
                <div>
                  <p class="text-sm text-muted-foreground">Contact Email:</p>
                  <p class="font-medium text-gray-900">{{ corporateClient.contact_email ?? '-' }}</p>
                </div>
                <div>
                  <p class="text-sm text-muted-foreground">Contact Phone:</p>
                  <p class="font-medium text-gray-900">{{ corporateClient.contact_phone ?? '-' }}</p>
                </div>
                <div>
                  <p class="text-sm text-muted-foreground">TIN Number:</p>
                  <p class="font-medium text-gray-900">{{ corporateClient.tin_number ?? '-' }}</p>
                </div>
                <div>
                  <p class="text-sm text-muted-foreground">Trade License Number:</p>
                  <p class="font-medium text-gray-900">{{ corporateClient.trade_license_number ?? '-' }}</p>
                </div>
                <div class="lg:col-span-3">
                  <p class="text-sm text-muted-foreground">Address:</p>
                  <p class="font-medium text-gray-900">{{ corporateClient.address ?? '-' }}</p>
                </div>
              </div>
            </div>

            <div class="avoid-break">
              <h2 class="text-lg font-semibold text-gray-800 mb-4 print:mb-2">System Information</h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
                <div>
                  <p class="text-sm text-muted-foreground">Created At:</p>
                  <p class="font-medium text-gray-900">{{ corporateClient.created_at ? new Date(corporateClient.created_at).toLocaleString() : '-' }}</p>
                </div>
                <div>
                  <p class="text-sm text-muted-foreground">Last Updated:</p>
                  <p class="font-medium text-gray-900">{{ corporateClient.updated_at ? new Date(corporateClient.updated_at).toLocaleString() : '-' }}</p>
                </div>
              </div>
            </div>

          </div>
        </div>

        <div class="p-6 border-t border-gray-200 rounded-b print:hidden">
          <div class="flex flex-wrap gap-2">
            <Link :href="route('admin.corporate-clients.index')" class="btn-glass btn-glass-sm">
              Back to List
            </Link>
            <button @click="printCurrent" class="btn-glass btn-glass-sm">
              <Printer class="h-4 w-4" /> Print Current
            </button>
            <Link :href="route('admin.corporate-clients.edit', corporateClient.id)" class="btn-glass btn-glass-sm">
              Edit Client
            </Link>
          </div>
        </div>
      </div>

      <div class="hidden print:block text-center mt-4 text-sm text-gray-500 print-footer">
        <hr class="my-2 border-gray-300">
        <p>Printed on: {{ new Date().toLocaleString() }}</p>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Printer } from 'lucide-vue-next';
import { defineProps } from 'vue';

const props = defineProps({
  corporateClient: Object,
});

const printCurrent = () => {
  window.print();
};

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Corporate Clients', href: route('admin.corporate-clients.index') },
  { title: props.corporateClient.organization_name, href: route('admin.corporate-clients.show', props.corporateClient.id) },
];
</script>

<style>
@media print {
  .avoid-break {
    break-inside: avoid;
    page-break-inside: avoid;
  }
  table, thead, tbody, tr, td, th {
    break-inside: avoid;
    page-break-inside: avoid;
  }
  img { page-break-inside: avoid; }
}
</style>
