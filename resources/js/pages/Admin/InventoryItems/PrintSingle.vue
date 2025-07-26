<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { format } from 'date-fns';

const props = defineProps({
  inventoryItem: Object, // The single inventory item to print
});

</script>

<template>
  <Head :title="`Print Inventory Item: ${props.inventoryItem.name}`" />

  <div class="print-document">
    <div class="print-header">
      <h1>Inventory Item Details</h1>
      <p>Generated on: {{ format(new Date(), 'PPP p') }}</p>
    </div>

    <div class="details-section">
      <h2>Basic Information</h2>
      <p><strong>Name:</strong> {{ inventoryItem.name }}</p>
      <p><strong>Category:</strong> {{ inventoryItem.item_category }}</p>
      <p><strong>Type:</strong> {{ inventoryItem.item_type }}</p>
      <p><strong>Serial Number:</strong> {{ inventoryItem.serial_number }}</p>
      <p><strong>Status:</strong> {{ inventoryItem.status }}</p>
      <p><strong>Description:</strong> {{ inventoryItem.description }}</p>
    </div>

    <div class="details-section">
      <h2>Acquisition & Assignment</h2>
      <p><strong>Purchase Date:</strong> {{ inventoryItem.purchase_date ? format(new Date(inventoryItem.purchase_date), 'PPP') : '-' }}</p>
      <p><strong>Warranty Expiry:</strong> {{ inventoryItem.warranty_expiry ? format(new Date(inventoryItem.warranty_expiry), 'PPP') : '-' }}</p>
      <p><strong>Supplier ID:</strong> {{ inventoryItem.supplier_id ?? '-' }}</p>
      <p><strong>Assigned To Type:</strong> {{ inventoryItem.assigned_to_type ?? '-' }}</p>
      <p><strong>Assigned To ID:</strong> {{ inventoryItem.assigned_to_id ?? '-' }}</p>
    </div>

    <div class="details-section">
      <h2>Maintenance Details</h2>
      <p><strong>Last Maintenance Date:</strong> {{ inventoryItem.last_maintenance_date ? format(new Date(inventoryItem.last_maintenance_date), 'PPP') : '-' }}</p>
      <p><strong>Next Maintenance Due:</strong> {{ inventoryItem.next_maintenance_due ? format(new Date(inventoryItem.next_maintenance_due), 'PPP') : '-' }}</p>
      <p><strong>Maintenance Schedule:</strong> {{ inventoryItem.maintenance_schedule ?? '-' }}</p>
      <p><strong>Notes:</strong> {{ inventoryItem.notes ?? '-' }}</p>
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
