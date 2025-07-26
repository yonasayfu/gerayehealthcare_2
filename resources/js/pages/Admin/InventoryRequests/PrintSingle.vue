<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { format } from 'date-fns';

const props = defineProps({
  inventoryRequest: Object, // The single inventory request to print
});

</script>

<template>
  <Head :title="`Print Inventory Request: ${props.inventoryRequest.id}`" />

  <div class="print-document">
    <div class="print-header">
      <h1>Inventory Request Details</h1>
      <p>Generated on: {{ format(new Date(), 'PPP p') }}</p>
    </div>

    <div class="details-section">
      <h2>Request Information</h2>
      <p><strong>Requester:</strong> {{ inventoryRequest.requester.first_name }} {{ inventoryRequest.requester.last_name }}</p>
      <p><strong>Item:</strong> {{ inventoryRequest.item.name }}</p>
      <p><strong>Quantity Requested:</strong> {{ inventoryRequest.quantity_requested }}</p>
      <p><strong>Quantity Approved:</strong> {{ inventoryRequest.quantity_approved ?? 'N/A' }}</p>
      <p><strong>Status:</strong> {{ inventoryRequest.status }}</p>
      <p><strong>Priority:</strong> {{ inventoryRequest.priority }}</p>
      <p><strong>Needed By Date:</strong> {{ inventoryRequest.needed_by_date ? format(new Date(inventoryRequest.needed_by_date), 'PPP') : '-' }}</p>
      <p><strong>Reason:</strong> {{ inventoryRequest.reason ?? '-' }}</p>
    </div>

    <div class="details-section" v-if="inventoryRequest.approver">
      <h2>Approval Information</h2>
      <p><strong>Approver:</strong> {{ inventoryRequest.approver.first_name }} {{ inventoryRequest.approver.last_name }}</p>
      <p><strong>Approved At:</strong> {{ inventoryRequest.approved_at ? format(new Date(inventoryRequest.approved_at), 'PPP p') : '-' }}</p>
    </div>

    <div class="details-section" v-if="inventoryRequest.fulfilled_at">
      <h2>Fulfillment Information</h2>
      <p><strong>Fulfilled At:</strong> {{ inventoryRequest.fulfilled_at ? format(new Date(inventoryRequest.fulfilled_at), 'PPP p') : '-' }}</p>
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
