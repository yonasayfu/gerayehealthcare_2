<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { format } from 'date-fns'

interface MarketingBudget {
  id: number;
  budget_name: string;
  allocated_amount: number;
  spent_amount: number;
  period_start: string;
  period_end: string;
  status: string;
  campaign: { campaign_name: string };
  platform: { name: string };
}

const props = defineProps<{
  marketingBudgets: MarketingBudget[];
}>()

const formattedGeneratedDate = format(new Date(), 'PPP p');

// Automatically trigger print when the component is mounted

</script>

<template>
  <Head title="Print All Marketing Budgets" />

  <div class="print-container">
    <div class="print-header-content">
      <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
      <h1 class="print-clinic-name">Geraye Home Care Services</h1>
      <p class="print-document-title">Marketing Budgets List (All)</p>
      <hr class="my-3 border-gray-300 print:my-2">
    </div>

    <div class="overflow-x-auto">
      <table class="print-table">
        <thead class="print-table-header">
          <tr>
            <th>Budget Name</th>
            <th>Campaign</th>
            <th>Platform</th>
            <th>Allocated</th>
            <th>Spent</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="budget in marketingBudgets" :key="budget.id" class="print-table-row">
            <td>{{ budget.budget_name }}</td>
            <td>{{ budget.campaign?.campaign_name ?? '-' }}</td>
            <td>{{ budget.platform?.name ?? '-' }}</td>
            <td>{{ budget.allocated_amount }}</td>
            <td>{{ budget.spent_amount }}</td>
            <td>{{ budget.period_start ? format(new Date(budget.period_start), 'PPP') : '-' }}</td>
            <td>{{ budget.period_end ? format(new Date(budget.period_end), 'PPP') : '-' }}</td>
            <td>{{ budget.status }}</td>
          </tr>
          <tr v-if="marketingBudgets.length === 0">
            <td colspan="8" class="text-center">No marketing budgets found.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="print-footer">
      <hr class="my-2 border-gray-300">
      <p>Document Generated: {{ formattedGeneratedDate }}</p>
    </div>
  </div>
</template>

<style>
/* Base styles for print */
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
    font-family: sans-serif;
  }

  /* Hide elements that should not be printed */
  .print\:hidden, .no-print {
    display: none !important;
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

  /* Main content container adjustments */
  .print-container {
    padding: 0 !important;
    margin: 0 !important;
    height: auto !important;
    min-height: auto !important;
  }

  /* Table specific print styles */
  .overflow-x-auto {
    box-shadow: none !important;
    border-radius: 0 !important;
    background-color: transparent !important; /* No background color */
    overflow: visible !important; /* Essential to prevent clipping */
    padding: 1cm; /* Inner padding for the table */
    page-break-after: auto !important;
  }

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

  /* Adjust column widths if needed, target by nth-child or specific content */
  .print-table th:nth-child(1), .print-table td:nth-child(1) { width: 15%; } /* Budget Name */
  .print-table th:nth-child(2), .print-table td:nth-child(2) { width: 15%; } /* Campaign */
  .print-table th:nth-child(3), .print-table td:nth-child(3) { width: 10%; } /* Platform */
  .print-table th:nth-child(4), .print-table td:nth-child(4) { width: 10%; }  /* Allocated */
  .print-table th:nth-child(5), .print-table td:nth-child(5) { width: 10%; } /* Spent */
  .print-table th:nth-child(6), .print-table td:nth-child(6) { width: 10%; } /* Start Date */
  .print-table th:nth-child(7), .print-table td:nth-child(7) { width: 10%; } /* End Date */
  .print-table th:nth-child(8), .print-table td:nth-child(8) { width: 10%; } /* Status */


  .print-table tbody tr:nth-child(even) {
    background-color: #f9f9f9 !important; /* Subtle zebra striping */
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }
  .print-table tbody tr:last-child {
    border-bottom: 1px solid #ddd !important;
  }
  .print-table-row {
    page-break-inside: avoid !important;
    break-inside: avoid !important;
  }

  /* Print Footer */
  .print-footer {
    display: block !important;
    text-align: center;
    position: relative;
    margin-top: 1cm;
    font-size: 0.75rem !important;
    color: #666 !important;
  }
  .print-footer hr {
    border-color: #ccc !important;
  }
}
</style>