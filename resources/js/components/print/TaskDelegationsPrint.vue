<template>
  <div class="print-only-content">
    <div class="print-header-content">
      <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
      <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Hospital</h1>
      <p class="text-gray-600 dark:text-gray-400 print-document-title">Task Delegations Report</p>
      <hr class="my-3 border-gray-300 print:my-2">
    </div>

    <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200 print-table">
      <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground print-table-header">
        <tr>
          <th class="px-6 py-3">Title</th>
          <th class="px-6 py-3">Assigned To</th>
          <th class="px-6 py-3">Due Date</th>
          <th class="px-6 py-3">Status</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="task in tasks" :key="task.id" class="border-b dark:border-gray-700 print-table-row">
          <td class="px-6 py-4">{{ task.title }}</td>
          <td class="px-6 py-4">{{ task.assignee.first_name }} {{ task.assignee.last_name }}</td>
          <td class="px-6 py-4">{{ new Date(task.due_date).toLocaleDateString() }}</td>
          <td class="px-6 py-4">{{ task.status }}</td>
        </tr>
        <tr v-if="tasks.length === 0">
          <td colspan="4" class="text-center px-6 py-4 text-gray-400">No tasks found.</td>
        </tr>
      </tbody>
    </table>

    <div class="print-footer">
      <hr class="my-2 border-gray-300">
      <p>Document Generated: {{ generatedDate }}</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { defineProps } from 'vue';

const props = defineProps<{
  tasks: Array<any>;
  generatedDate: string;
}>();
</script>

<style>
/* Print-specific styles for TaskDelegationsPrint.vue */
@media print {
  @page {
    size: A4 landscape; /* Landscape is often better for tables */
    margin: 0.5cm;
  }

  body {
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
    color: #000 !important;
    margin: 0 !important;
    padding: 0 !important;
    overflow: visible !important;
  }

  /* Hide everything by default, then show only print-only content */
  body * {
    visibility: hidden;
  }

  .print-only-content, .print-only-content * {
    visibility: visible;
  }

  .print-only-content {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    padding: 1cm; /* Add some padding to the print content */
  }

  /* Specific styles for the print header content (logo and clinic name) */
  .print-header-content {
      display: block !important; /* Show header */
      text-align: center;
      padding-top: 0.5cm;
      padding-bottom: 0.5cm;
      margin-bottom: 0.8cm;
  }
  .print-logo {
      max-width: 150px; /* Adjust as needed */
      max-height: 50px; /* Adjust as needed */
      margin-bottom: 0.5rem;
      display: block;
      margin-left: auto;
      margin-right: auto;
  }
  .print-clinic-name {
      font-size: 1.6rem !important; /* Slightly smaller than show view */
      margin-bottom: 0.2rem !important;
      line-height: 1.2 !important;
      font-weight: bold;
  }
  .print-document-title {
      font-size: 0.85rem !important;
      color: #555 !important;
  }
  hr { border-color: #ccc !important; }

  /* Table specific print styles */
  .print-table {
    width: 100% !important;
    border-collapse: collapse !important;
    font-size: 0.8rem !important; /* Adjust table body font size */
    table-layout: fixed; /* Helps with column width distribution */
  }

  .print-table-header {
    background-color: #f0f0f0 !important; /* Light grey header background */
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
    text-transform: uppercase !important;
  }

  .print-table th, .print-table td {
    border: 1px solid #ddd !important; /* Subtle borders for all cells */
    padding: 0.4rem 0.6rem !important; /* Adjust cell padding */
    color: #000 !important;
    vertical-align: top !important; /* Align content to top of cell */
    word-break: break-word; /* Allow long words to break */
  }

  .print-table th {
    font-weight: bold !important;
    font-size: 0.7rem !important; /* Header font size */
    white-space: nowrap; /* Keep header text on one line if possible */
  }

  .print-table tbody tr:nth-child(even) {
    background-color: #f9f9f9 !important; /* Subtle zebra striping */
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }
  .print-table tbody tr:last-child {
    border-bottom: 1px solid #ddd !important;
  }

  /* Print Footer */
  .print-footer {
    display: block !important;
    text-align: center;
    margin-top: 1cm;
    font-size: 0.75rem !important;
    color: #666 !important;
  }
  .print-footer hr {
    border-color: #ccc !important;
  }
}
</style>