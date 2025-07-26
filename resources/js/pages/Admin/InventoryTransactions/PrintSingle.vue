<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { format } from 'date-fns';

const props = defineProps({
  inventoryTransaction: Object, // The single inventory transaction to print
});

</script>

<template>
  <Head :title="`Print Inventory Transaction: ${props.inventoryTransaction.id}`" />

  <div class="print-document">
    <div class="print-header">
      <h1>Inventory Transaction Details</h1>
      <p>Generated on: {{ format(new Date(), 'PPP p') }}</p>
    </div>

    <div class="details-section">
      <h2>Transaction Information</h2>
      <p><strong>Item:</strong> {{ inventoryTransaction.item.name }}</p>
      <p><strong>Type:</strong> {{ inventoryTransaction.transaction_type }}</p>
      <p><strong>Quantity:</strong> {{ inventoryTransaction.quantity }}</p>
      <p><strong>From Location:</strong> {{ inventoryTransaction.from_location ?? '-' }}</p>
      <p><strong>To Location:</strong> {{ inventoryTransaction.to_location ?? '-' }}</p>
      <p><strong>Performed By:</strong> {{ inventoryTransaction.performed_by.first_name }} {{ inventoryTransaction.performed_by.last_name }}</p>
      <p><strong>Date:</strong> {{ inventoryTransaction.created_at ? format(new Date(inventoryTransaction.created_at), 'PPP p') : '-' }}</p>
      <p><strong>Notes:</strong> {{ inventoryTransaction.notes ?? '-' }}</p>
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
