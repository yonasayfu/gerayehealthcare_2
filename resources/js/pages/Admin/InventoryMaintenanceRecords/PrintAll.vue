<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { format } from 'date-fns';

const props = defineProps({
  maintenanceRecords: Object, // List of maintenance records to print
});

</script>

<template>
  <Head title="Print All Inventory Maintenance Records" />

  <div class="print-document">
    <div class="print-header">
      <h1>All Inventory Maintenance Records</h1>
      <p>Generated on: {{ format(new Date(), 'PPP p') }}</p>
    </div>

    <table class="print-table">
      <thead>
        <tr>
          <th>Item</th>
          <th>Scheduled Date</th>
          <th>Actual Date</th>
          <th>Performed By</th>
          <th>Cost</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="record in maintenanceRecords" :key="record.id">
          <td>{{ record.item.name }}</td>
          <td>{{ record.scheduled_date ? format(new Date(record.scheduled_date), 'PPP') : '-' }}</td>
          <td>{{ record.actual_date ? format(new Date(record.actual_date), 'PPP') : '-' }}</td>
          <td>{{ record.performed_by }}</td>
          <td>{{ record.cost }}</td>
          <td>{{ record.status }}</td>
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
