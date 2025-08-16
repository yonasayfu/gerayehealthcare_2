<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Printer, Edit3, Trash2 } from 'lucide-vue-next' // Import icons
import type { BreadcrumbItemType, InventoryMaintenanceRecord } from '@/types' // Import InventoryMaintenanceRecord type
import { format } from 'date-fns' // For date formatting

const props = defineProps<{
  maintenanceRecord: InventoryMaintenanceRecord; // Use InventoryMaintenanceRecord type for type safety
}>()

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Maintenance Records', href: route('admin.inventory-maintenance-records.index') },
  { title: `Record for ${props.maintenanceRecord.item.name}`, href: route('admin.inventory-maintenance-records.show', props.maintenanceRecord.id) },
]

function printPage() {
  setTimeout(() => {
    try {
      window.print();
    } catch (error) {
      console.error('Print failed:', error);
      alert('Failed to open print dialog. Please check your browser settings or try again.');
    }
  }, 100); // 100ms delay
}
</script>

<template>
  <Head :title="`Maintenance Record: ${props.maintenanceRecord.item.name}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="bg-white border border-4 rounded-lg shadow relative m-10">

        <div class="flex items-center justify-between p-5 border-b rounded-t">
            <h3 class="text-xl font-semibold">
                Maintenance Record Details: {{ maintenanceRecord.item.name }}
            </h3>
            <div class="flex items-center gap-3">
              <Link :href="route('admin.inventory-maintenance-records.index')" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 inline-flex items-center print:hidden" title="Close">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
              </Link>
            </div>
        </div>

        <div class="p-6 space-y-6">
            <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-8 space-y-8 print:shadow-none print:rounded-none print:p-0 print:m-0 print:w-auto print:h-auto print:flex-shrink-0">

                <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
                    <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
                    <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
                    <p class="text-gray-600 dark:text-gray-400 print-document-title">Inventory Maintenance Record Document</p>
                    <hr class="my-3 border-gray-300 print:my-2">
                </div>

                <div class="border-b pb-4 mb-4 print:pb-2 print:mb-2">
                  <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 print:mb-2">Maintenance Details</h2>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
                    <div>
                      <p class="text-sm text-muted-foreground">Item:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ maintenanceRecord.item.name ?? '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Scheduled Date:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ maintenanceRecord.scheduled_date ? format(new Date(maintenanceRecord.scheduled_date), 'PPP') : '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Actual Date:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ maintenanceRecord.actual_date ? format(new Date(maintenanceRecord.actual_date), 'PPP') : '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Performed By:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ maintenanceRecord.performed_by ?? '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Cost:</p>
                      <p class="font-medium text-gray-900 dark:text-white">
                        {{ maintenanceRecord.cost !== null && maintenanceRecord.cost !== undefined && !isNaN(Number(maintenanceRecord.cost))
                          ? Number(maintenanceRecord.cost).toFixed(2)
                          : '-' }}
                      </p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Next Due Date:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ maintenanceRecord.next_due_date ? format(new Date(maintenanceRecord.next_due_date), 'PPP') : '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Status:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ maintenanceRecord.status ?? '-' }}</p>
                    </div>
                  </div>
                </div>

                <div class="border-b pb-4 mb-4 print:pb-2 print:mb-2">
