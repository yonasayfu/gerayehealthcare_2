<script setup>
import { onMounted } from 'vue';
import { format } from 'date-fns';

const props = defineProps({
    marketingBudgets: Array,
});

const formattedGeneratedDate = format(new Date(), 'PPP p');

onMounted(() => {
    // Removed window.print() as PDF generation is handled by the controller
});
</script>

<template>
    <div class="print-container">
        <div class="print-header-content">
            <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
            <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Hospital</h1>
            <p class="text-gray-600 dark:text-gray-400 print-document-title">Marketing Budgets List (All)</p>
            <hr class="my-3 border-gray-300 print:my-2">
        </div>

        <table class="print-table">
            <thead>
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
                <tr v-for="budget in marketingBudgets" :key="budget.id">
                    <td>{{ budget.budget_name }}</td>
                    <td>{{ budget.campaign?.campaign_name ?? '-' }}</td>
                    <td>{{ budget.platform?.name ?? '-' }}</td>
                    <td>{{ budget.allocated_amount }}</td>
                    <td>{{ budget.spent_amount }}</td>
                    <td>{{ budget.period_start ? format(new Date(budget.period_start), 'PPP') : '-' }}</td>
                    <td>{{ budget.period_end ? format(new Date(budget.period_end), 'PPP') : '-' }}</td>
                    <td>{{ budget.status }}</td>
                </tr>
            </tbody>
        </table>

        <div class="print-footer">
            <hr class="my-2 border-gray-300">
            <p>Document Generated: {{ formattedGeneratedDate }}</p>
        </div>
    </div>
</template>

<style>
/* Basic styling for print */
.print-container {
    font-family: Arial, sans-serif;
    margin: 0.5cm;
}

.print-header-content {
    text-align: center;
    padding-top: 0.5cm;
    padding-bottom: 0.5cm;
    margin-bottom: 0.8cm;
}

.print-logo {
    max-width: 150px;
    max-height: 50px;
    margin-bottom: 0.5rem;
    display: block;
    margin-left: auto;
    margin-right: auto;
}

.print-clinic-name {
    font-size: 1.6rem;
    margin-bottom: 0.2rem;
    line-height: 1.2;
    font-weight: bold;
}

.print-document-title {
    font-size: 0.85rem;
    color: #555;
}

hr {
    border-color: #ccc;
}

.print-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.8rem;
    table-layout: fixed;
}

.print-table-header {
    background-color: #f0f0f0;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
    text-transform: uppercase;
}

.print-table th, .print-table td {
    border: 1px solid #ddd;
    padding: 0.4rem 0.6rem;
    color: #000;
    vertical-align: top;
    word-break: break-word;
}

.print-table th {
    font-weight: bold;
    font-size: 0.7rem;
    white-space: nowrap;
}

.print-table tbody tr:nth-child(even) {
    background-color: #f9f9f9;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
}

.print-table-row {
    page-break-inside: avoid !important;
    break-inside: avoid !important;
}

.print-footer {
    display: block;
    text-align: center;
    margin-top: 1cm;
    font-size: 0.75rem;
    color: #666;
}

.print-footer hr {
    border-color: #ccc;
}

@media print {
  @page {
    size: A4 landscape;
    margin: 0.5cm;
  }

  body {
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
    color: #000 !important;
    margin: 0 !important;
    padding: 0 !important;
    padding-bottom: 2cm !important;
    overflow: visible !important;
  }
}
</style>
