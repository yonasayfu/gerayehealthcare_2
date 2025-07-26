<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Printer, Edit3, Trash2 } from 'lucide-vue-next';
import { format } from 'date-fns';
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

</script>

<template>
  <Head :title="`Inventory Item: ${props.inventoryItem.name}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4 print:hidden">
        <div>
          <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Inventory Item: {{ inventoryItem.name }}</h1>
          <p class="text-sm text-muted-foreground">Detailed view of the inventory item.</p>
        </div>
        <div class="flex flex-wrap gap-2">
          <button @click="printPage" class="inline-flex items-center gap-1 text-sm px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200">
            <Printer class="h-4 w-4" /> Print Document
          </button>
          <Link :href="route('admin.inventory-items.edit', props.inventoryItem.id)"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-md transition">
            <Edit3 class="w-4 h-4" /> Edit Item
          </Link>
          <!-- Delete button can be added here -->
        </div>
      </div>

      <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-8 space-y-8 print:shadow-none print:rounded-none print:p-0 print:m-0 print:w-auto print:h-auto print:flex-shrink-0">

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
