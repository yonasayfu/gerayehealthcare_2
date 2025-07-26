<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Printer, Edit3, Trash2 } from 'lucide-vue-next';
import { format } from 'date-fns';

const props = defineProps({
  supplier: Object, // The supplier to display
});

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Suppliers', href: route('admin.suppliers.index') },
  { title: props.supplier.name, href: route('admin.suppliers.show', props.supplier.id) },
];

function printPage() {
  window.print();
}

</script>

<template>
  <Head :title="`Supplier: ${props.supplier.name}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4 print:hidden">
        <div>
          <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Supplier: {{ supplier.name }}</h1>
          <p class="text-sm text-muted-foreground">Detailed view of the supplier.</p>
        </div>
        <div class="flex flex-wrap gap-2">
          <button @click="printPage" class="inline-flex items-center gap-1 text-sm px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200">
            <Printer class="h-4 w-4" /> Print Document
          </button>
          <Link :href="route('admin.suppliers.edit', props.supplier.id)"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-md transition">
            <Edit3 class="w-4 h-4" /> Edit Supplier
          </Link>
          <!-- Delete button can be added here -->
        </div>
      </div>

      <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-8 space-y-8 print:shadow-none print:rounded-none print:p-0 print:m-0 print:w-auto print:h-auto print:flex-shrink-0">
        <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
          <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
          <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Hospital</h1>
          <p class="text-gray-600 dark:text-gray-400 print-document-title">Supplier Details</p>
          <hr class="my-3 border-gray-300 print:my-2">
        </div>

        <div class="border-b pb-4 mb-4 print:pb-2 print:mb-2">
          <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 print:mb-2">Supplier Information</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
            <div>
              <p class="text-sm text-muted-foreground">Name:</p>
              <p class="font-medium text-gray-900 dark:text-white">{{ supplier.name }}</p>
            </div>
            <div>
              <p class="text-sm text-muted-foreground">Contact Person:</p>
              <p class="font-medium text-gray-900 dark:text-white">{{ supplier.contact_person }}</p>
            </div>
            <div>
              <p class="text-sm text-muted-foreground">Email:</p>
              <p class="font-medium text-gray-900 dark:text-white">{{ supplier.email }}</p>
            </div>
            <div>
              <p class="text-sm text-muted-foreground">Phone:</p>
              <p class="font-medium text-gray-900 dark:text-white">{{ supplier.phone }}</p>
            </div>
            <div class="md:col-span-2 lg:col-span-2">
              <p class="text-sm text-muted-foreground">Address:</p>
              <p class="font-medium text-gray-900 dark:text-white">{{ supplier.address }}</p>
            </div>
          </div>
        </div>

        <div class="col-span-full">
          <p class="text-sm text-muted-foreground">Notes:</p>
          <p class="font-medium text-gray-900 dark:text-white">{{ supplier.notes ?? '-' }}</p>
        </div>
        <div class="hidden print:block text-center mt-4 text-sm text-gray-500 print-footer">
          <hr class="my-2 border-gray-300">
          <p>Document Generated: {{ format(new Date(), 'PPP p') }}</p>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style>
/* Add print styles here if needed */
</style>
