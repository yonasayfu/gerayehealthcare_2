<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { format } from 'date-fns';

const props = defineProps({
  inventoryRequests: Object, // List of inventory requests to print
});

</script>

<template>
  <Head title="Print All Inventory Requests" />

  <div class="print-document">
    <div class="print-header">
      <h1>All Inventory Requests</h1>
      <p>Generated on: {{ format(new Date(), 'PPP p') }}</p>
    </div>

    <table class="print-table">
      <thead>
        <tr>
          <th>Requester</th>
          <th>Item</th>
          <th>Quantity Requested</th>
          <th>Status</th>
          <th>Priority</th>
          <th>Needed By Date</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="request in inventoryRequests" :key="request.id">
          <td>{{ request.requester.first_name }} {{ request.requester.last_name }}</td>
          <td>{{ request.item.name }}</td>
          <td>{{ request.quantity_requested }}</td>
          <td>{{ request.status }}</td>
          <td>{{ request.priority }}</td>
          <td>{{ request.needed_by_date ? format(new Date(request.needed_by_date), 'PPP') : '-' }}</td>
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
