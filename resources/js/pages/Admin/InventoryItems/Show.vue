<script setup lang="ts">
import ShowHeader from '@/components/ShowHeader.vue'
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

function printItem() {
  window.open(route('admin.inventory-items.printSingle', props.inventoryItem.id), '_blank');
}

</script>

<template>
  <Head :title="`Inventory Item: ${props.inventoryItem.name}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow relative m-10">

      <ShowHeader title="Inventory Item Details" :subtitle="`Inventory Item: ${inventoryItem.name}`">
        <template #actions>
          <Link :href="route('admin.inventory-items.index')" class="btn-glass btn-glass-sm">Back</Link>
        </template>
      </ShowHeader>

        <div class="p-6 space-y-6">
            <div class="print-document bg-card text-card-foreground shadow rounded-lg p-8 space-y-8 print:shadow-none print:rounded-none print:p-0 print:m-0 print:w-auto print:h-auto print:flex-shrink-0">
                <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
                  <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
                  <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
                  <p class="text-gray-600 dark:text-gray-400 print-document-title">Inventory Item Details</p>
                  <hr class="my-2 border-gray-300 print:my-2">
                </div>

                <div class="border-b pb-4 mb-4 print:pb-2 print:mb-2">
                  <h2 class="text-lg font-semibold text-foreground mb-4 print:mb-2">Basic Information</h2>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
                    <div>
                      <p class="text-sm text-muted-foreground">Name:</p>
                      <p class="font-medium text-foreground">{{ inventoryItem.name }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Category:</p>
                      <p class="font-medium text-foreground">{{ inventoryItem.item_category }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Type:</p>
                      <p class="font-medium text-foreground">{{ inventoryItem.item_type }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Serial Number:</p>
                      <p class="font-medium text-foreground">{{ inventoryItem.serial_number }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Status:</p>
                      <p class="font-medium text-foreground">{{ inventoryItem.status }}</p>
                    </div>
                    <div class="md:col-span-2 lg:col-span-1">
                      <p class="text-sm text-muted-foreground">Description:</p>
                      <p class="font-medium text-foreground">{{ inventoryItem.description }}</p>
                    </div>
                  </div>
                </div>

                <div class="border-b pb-4 mb-4 print:pb-2 print:mb-2">
                  <h2 class="text-lg font-semibold text-foreground mb-4 print:mb-2">Acquisition & Assignment</h2>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
                    <div>
                      <p class="text-sm text-muted-foreground">Purchase Date:</p>
                      <p class="font-medium text-foreground">{{ inventoryItem.purchase_date ? format(new Date(inventoryItem.purchase_date), 'PPP') : '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Warranty Expiry:</p>
                      <p class="font-medium text-foreground">{{ inventoryItem.warranty_expiry ? format(new Date(inventoryItem.warranty_expiry), 'PPP') : '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Supplier:</p>
                      <p class="font-medium text-foreground">{{ inventoryItem.supplier?.name ?? '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Assigned To Type:</p>
                      <p class="font-medium text-foreground">{{ inventoryItem.assigned_to_type ?? '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Assigned To ID:</p>
                      <p class="font-medium text-foreground">{{ inventoryItem.assigned_to_id ?? '-' }}</p>
                    </div>
                  </div>
                </div>

                <div>
                  <h2 class="text-lg font-semibold text-foreground mb-4 print:mb-2">Maintenance Details</h2>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
                    <div>
                      <p class="text-sm text-muted-foreground">Last Maintenance Date:</p>
                      <p class="font-medium text-foreground">{{ inventoryItem.last_maintenance_date ? format(new Date(inventoryItem.last_maintenance_date), 'PPP') : '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Next Maintenance Due:</p>
                      <p class="font-medium text-foreground">{{ inventoryItem.next_maintenance_due ? format(new Date(inventoryItem.next_maintenance_due), 'PPP') : '-' }}</p>
                    </div>
                    <div class="md:col-span-2 lg:col-span-1">
                      <p class="text-sm text-muted-foreground">Maintenance Schedule:</p>
                      <p class="font-medium text-foreground">{{ inventoryItem.maintenance_schedule ?? '-' }}</p>
                    </div>
                    <div class="col-span-full">
                      <p class="text-sm text-muted-foreground">Notes:</p>
                      <p class="font-medium text-foreground">{{ inventoryItem.notes ?? '-' }}</p>
                    </div>
                  </div>
                </div>
                <div class="hidden print:block text-center mt-4 text-sm text-gray-500 print-footer">
                  <hr class="my-2 border-gray-300">
                  <p>Document Generated: {{ format(new Date(), 'PPP p') }}</p>
                </div>
            </div>
        </div>

              <!-- footer actions (single source of actions, right aligned) -->
      <div class="p-6 border-t border-gray-200 dark:border-gray-700 rounded-b print:hidden">
        <div class="flex justify-end gap-2">
          <Link :href="route('admin.inventory-items.index')" class="btn-glass btn-glass-sm">Back to List</Link>
          <button @click="printPage" class="btn-glass btn-glass-sm">Print Current</button>
          <Link :href="route('admin.inventory-items.edit', inventoryItem.id)" class="btn-glass btn-glass-sm">Edit</Link>
        </div>
      </div>

    </div>
  </AppLayout>
</template>

<style>
/* Add print styles here if needed */
</style>
