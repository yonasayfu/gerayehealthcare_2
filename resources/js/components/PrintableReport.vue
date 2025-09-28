<script setup lang="ts">
import { computed } from 'vue';
import { format } from 'date-fns';

interface Column {
  key: string;
  label: string;
  format?: (value: any) => string; // Optional formatter function for cell values
  printWidth?: string; // Optional width for print layout (e.g., '20%')
}

const props = defineProps<{
  title: string;
  data: Array<Record<string, any>>;
  columns: Column[];
  headerInfo?: {
    logoSrc?: string;
    clinicName?: string;
    documentTitle?: string;
  };
  footerInfo?: {
    generatedDate?: boolean;
  };
}>();

const formattedGeneratedDate = computed(() => {
  return format(new Date(), 'PPP p');
});

// Default header info if not provided
const defaultHeaderInfo = {
  logoSrc: '/images/geraye_logo.jpeg',
  clinicName: 'Geraye Home Care Services',
  documentTitle: props.title,
};

const mergedHeaderInfo = computed(() => ({
  ...defaultHeaderInfo,
  ...props.headerInfo,
}));

</script>

<template>
  <div class="print-container">
    <!-- Print Header -->
    <div class="print-header-content">
      <img v-if="mergedHeaderInfo.logoSrc" :src="mergedHeaderInfo.logoSrc" alt="Logo" class="print-logo">
      <h1 v-if="mergedHeaderInfo.clinicName" class="print-clinic-name">{{ mergedHeaderInfo.clinicName }}</h1>
      <p v-if="mergedHeaderInfo.documentTitle" class="print-document-title">{{ mergedHeaderInfo.documentTitle }}</p>
      <hr class="my-3 border-gray-300 print:my-2">
    </div>

    <!-- Printable Table -->
    <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow rounded-lg print:shadow-none print:rounded-none print:bg-transparent">
      <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200 print-table">
        <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground print-table-header">
          <tr>
            <th v-for="column in columns" :key="column.key" class="px-6 py-3" :style="{ width: column.printWidth }">
              {{ column.label }}
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(row, rowIndex) in data" :key="rowIndex" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 print-table-row">
            <td v-for="column in columns" :key="column.key" class="px-6 py-4">
              <template v-if="column.format">
                {{ column.format(row[column.key]) }}
              </template>
              <template v-else>
                {{ row[column.key] ?? '-' }}
              </template>
            </td>
          </tr>
          <tr v-if="data.length === 0">
            <td :colspan="columns.length" class="text-center px-6 py-4 text-gray-400">No records found.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Print Footer -->
    <div v-if="footerInfo?.generatedDate" class="print-footer">
      <hr class="my-2 border-gray-300">
      <p>Document Generated: {{ formattedGeneratedDate }}</p>
    </div>
  </div>
</template>

<style scoped>
/* No scoped styles here, all print styles are in resources/css/print.css */
</style>
