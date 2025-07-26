<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { format } from 'date-fns';

const props = defineProps({
  inventoryTransactions: Object, // List of inventory transactions to print
});

</script>

<template>
  <Head title="Print All Inventory Transactions" />

  <div class="print-document">
    <div class="print-header">
      <h1>All Inventory Transactions</h1>
      <p>Generated on: {{ format(new Date(), 'PPP p') }}</p>
    </div>

    <table class="print-table">
      <thead>
        <tr>
          <th>Item</th>
          <th>Type</th>
          <th>Quantity</th>
          <th>From</th>
          <th>To</th>
          <th>Performed By</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="transaction in inventoryTransactions" :key="transaction.id">
          <td>{{ transaction.item.name }}</td>
          <td>{{ transaction.transaction_type }}</td>
          <td>{{ transaction.quantity }}</td>
          <td>{{ transaction.from_location }}</td>
          <td>{{ transaction.to_location }}</td>
          <td>{{ transaction.performed_by.first_name }} {{ transaction.performed_by.last_name }}</td>
          <td>{{ transaction.created_at ? format(new Date(transaction.created_at), 'PPP') : '-' }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<style>
/* Basic Print Styles */
body { font-family: sans-serif; margin: 0; padding: 0; }
.print-document { width: 210mm; min-height: 297mm; margin: 0 auto; padding: 15mm; box-sizing: border-box; }
.print-header { text-align: center; margin-bottom: 20mm; }
.print-header h1 { font-size: 24pt; margin-bottom: 5mm; }
.print-header p { font-size: 10pt; color: #555; }
.print-table { width: 100%; border-collapse: collapse; margin-bottom: 10mm; }
.print-table th, .print-table td { border: 1px solid #ccc; padding: 8pt; text-align: left; font-size: 9pt; }
.print-table th { background-color: #f0f0f0; }

@media print {
  body { margin: 0; padding: 0; }
  .print-document { width: auto; min-height: auto; margin: 0; padding: 0; }
}
</style>
