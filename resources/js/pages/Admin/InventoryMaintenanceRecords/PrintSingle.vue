<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { format } from 'date-fns';

const props = defineProps({
  maintenanceRecord: Object, // The single maintenance record to print
});

</script>

<template>
  <Head :title="`Print Maintenance Record: ${props.maintenanceRecord.id}`" />

  <div class="print-document">
    <div class="print-header">
      <h1>Maintenance Record Details</h1>
      <p>Generated on: {{ format(new Date(), 'PPP p') }}</p>
    </div>

    <div class="details-section">
      <h2>Record Information</h2>
      <p><strong>Item:</strong> {{ maintenanceRecord.item.name }}</p>
      <p><strong>Scheduled Date:</strong> {{ maintenanceRecord.scheduled_date ? format(new Date(maintenanceRecord.scheduled_date), 'PPP') : '-' }}</p>
      <p><strong>Actual Date:</strong> {{ maintenanceRecord.actual_date ? format(new Date(maintenanceRecord.actual_date), 'PPP') : '-' }}</p>
      <p><strong>Performed By:</strong> {{ maintenanceRecord.performed_by }}</p>
      <p><strong>Cost:</strong> {{ maintenanceRecord.cost }}</p>
      <p><strong>Description:</strong> {{ maintenanceRecord.description }}</p>
      <p><strong>Next Due Date:</strong> {{ maintenanceRecord.next_due_date ? format(new Date(maintenanceRecord.next_due_date), 'PPP') : '-' }}</p>
      <p><strong>Status:</strong> {{ maintenanceRecord.status }}</p>
    </div>
  </div>
</template>

<style>
/* Basic Print Styles */
body { font-family: sans-serif; margin: 0; padding: 0; }
.print-document { width: 210mm; min-height: 297mm; margin: 0 auto; padding: 15mm; box-sizing: border-box; }
.print-header { text-align: center; margin-bottom: 15mm; }
.print-header h1 { font-size: 20pt; margin-bottom: 5mm; }
.print-header p { font-size: 9pt; color: #555; }
.details-section { margin-bottom: 10mm; border-bottom: 1px solid #eee; padding-bottom: 5mm; }
.details-section h2 { font-size: 14pt; margin-bottom: 5mm; color: #333; }
.details-section p { font-size: 10pt; line-height: 1.6; }
.details-section p strong { display: inline-block; width: 120px; color: #000; }

@media print {
  body { margin: 0; padding: 0; }
  .print-document { width: auto; min-height: auto; margin: 0; padding: 0; }
}
</style>
