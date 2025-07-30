<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Printer, Edit3, Trash2 } from 'lucide-vue-next';
import { format } from 'date-fns';

const props = defineProps({
  inventoryItem: Object, // The inventory item to display
});

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Inventory Items', href: route('admin.inventory-items.index') },
  { title: props.inventoryItem.name, href: route('admin.inventory-items.show', props.inventoryItem.id) },
];

function printPage() {
  window.print();
}

function downloadPdf() {
  window.open(route('admin.inventory-items.generateSinglePdf', props.inventoryItem.id), '_blank');
}

</script>

<template>
  <Head :title="`Inventory Item: ${props.inventoryItem.name}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="bg-white border border-4 rounded-lg shadow relative m-10">

        <div class="flex items-start justify-between p-5 border-b rounded-t">
            <h3 class="text-xl font-semibold">
                Inventory Item: {{ inventoryItem.name }}
            </h3>
            <Link :href="route('admin.inventory-items.index')" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
               <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </Link>
        </div>

        <div class="p-6 space-y-6">
            <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-8 space-y-8 print:shadow-none print:rounded-none print:p-0 print:m-0 print:w-auto print:h-auto print:flex-shrink-0">
                <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
                  <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
                  <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
                  <p class="text-gray-600 dark:text-gray-400 print-document-title">Inventory Item Details</p>
                  <hr class="my-2 border-gray-300 print:my-2">
                </div>

                <div class="border-b pb-4 mb-4 print:pb-2 print:mb-2">
                  <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 print:mb-2">Basic Information</h2>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
                    <div>
                      <p class="text-sm text-muted-foreground">Name:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ inventoryItem.name }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Category:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ inventoryItem.item_category }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Type:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ inventoryItem.item_type }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Serial Number:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ inventoryItem.serial_number }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Status:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ inventoryItem.status }}</p>
                    </div>
                    <div class="md:col-span-2 lg:col-span-1">
                      <p class="text-sm text-muted-foreground">Description:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ inventoryItem.description }}</p>
                    </div>
                  </div>
                </div>

                <div class="border-b pb-4 mb-4 print:pb-2 print:mb-2">
                  <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 print:mb-2">Acquisition & Assignment</h2>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
                    <div>
                      <p class="text-sm text-muted-foreground">Purchase Date:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ inventoryItem.purchase_date ? format(new Date(inventoryItem.purchase_date), 'PPP') : '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Warranty Expiry:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ inventoryItem.warranty_expiry ? format(new Date(inventoryItem.warranty_expiry), 'PPP') : '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Supplier:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ inventoryItem.supplier?.name ?? '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Assigned To Type:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ inventoryItem.assigned_to_type ?? '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Assigned To ID:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ inventoryItem.assigned_to_id ?? '-' }}</p>
                    </div>
                  </div>
                </div>

                <div>
                  <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 print:mb-2">Maintenance Details</h2>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
                    <div>
                      <p class="text-sm text-muted-foreground">Last Maintenance Date:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ inventoryItem.last_maintenance_date ? format(new Date(inventoryItem.last_maintenance_date), 'PPP') : '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Next Maintenance Due:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ inventoryItem.next_maintenance_due ? format(new Date(inventoryItem.next_maintenance_due), 'PPP') : '-' }}</p>
                    </div>
                    <div class="md:col-span-2 lg:col-span-1">
                      <p class="text-sm text-muted-foreground">Maintenance Schedule:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ inventoryItem.maintenance_schedule ?? '-' }}</p>
                    </div>
                    <div class="col-span-full">
                      <p class="text-sm text-muted-foreground">Notes:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ inventoryItem.notes ?? '-' }}</p>
                    </div>
                  </div>
                </div>
                <div class="hidden print:block text-center mt-4 text-sm text-gray-500 print-footer">
                  <hr class="my-2 border-gray-300">
                  <p>Document Generated: {{ format(new Date(), 'PPP p') }}</p>
                </div>
            </div>
        </div>

        <div class="p-6 border-t border-gray-200 rounded-b">
            <div class="flex flex-wrap gap-2">
              <button @click="downloadPdf()" class="inline-flex items-center gap-1 text-sm px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200">
                <Printer class="h-4 w-4" /> Download PDF
              </button>
              <Link :href="route('admin.inventory-items.edit', props.inventoryItem.id)"
                class="text-white bg-cyan-600 hover:bg-cyan-700 focus:ring-4 focus:ring-cyan-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                Edit Item
              </Link>
            </div>
        </div>

    </div>
  </AppLayout>
</template>

<style>
/* Add print styles here if needed */
</style>
