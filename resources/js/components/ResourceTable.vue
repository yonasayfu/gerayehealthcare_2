<script setup lang="ts">
import { ArrowUpDown } from 'lucide-vue-next';

interface Column {
  key: string;
  label: string;
  sortable?: boolean;
  class?: string;
}

interface Props {
  columns: Column[];
  data: any[];
  sortField?: string;
  sortDirection?: 'asc' | 'desc';
  printTitle?: string;
  showPrintHeader?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  sortField: '',
  sortDirection: 'asc',
  printTitle: 'Records List',
  showPrintHeader: true,
});

const emit = defineEmits<{
  sort: [field: string];
}>();

const handleSort = (field: string) => {
  emit('sort', field);
};
</script>

<template>
  <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow rounded-lg print:shadow-none print:rounded-none print:bg-transparent">
    <!-- Print Header -->
    <div v-if="showPrintHeader" class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
      <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
      <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
      <p class="text-gray-600 dark:text-gray-400 print-document-title">{{ printTitle }} (Current View)</p>
      <hr class="my-3 border-gray-300 print:my-2">
    </div>
    
    <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200 print-table">
      <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground print-table-header">
        <tr>
          <th class="px-6 py-3">#</th>
          <th 
            v-for="column in columns" 
            :key="column.key"
            :class="[
              'px-6 py-3',
              column.sortable ? 'cursor-pointer' : '',
              column.class || ''
            ]"
            @click="column.sortable ? handleSort(column.key) : null"
          >
            {{ column.label }}
            <ArrowUpDown 
              v-if="column.sortable" 
              class="inline w-4 h-4 ml-1 print:hidden" 
            />
          </th>
          <th class="px-6 py-3 print:hidden">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
        <slot name="rows" :data="data" />
      </tbody>
    </table>

    <!-- Empty State -->
    <div v-if="data.length === 0" class="text-center py-12">
      <div class="text-gray-500 dark:text-gray-400">
        <slot name="empty-state">
          <p class="text-lg font-medium">No records found</p>
          <p class="text-sm">Try adjusting your search criteria</p>
        </slot>
      </div>
    </div>
  </div>
</template>
