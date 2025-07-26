<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { computed } from 'vue';

const props = defineProps({
  inventoryItem: {
    type: [Object, Array],
    required: true
  }
});

const inventoryItems = computed(() => {
  return Array.isArray(props.inventoryItem) ? props.inventoryItem : [props.inventoryItem];
});

</script>

<template>
  <Head :title="Array.isArray(props.inventoryItem) ? 'Print All Inventory Items' : `Print Inventory Item: ${!Array.isArray(props.inventoryItem) ? props.inventoryItem.name : ''}`" />

  <div class="print-document">
    <div class="print-header">
      <img src="/images/geraye_logo.jpeg" alt="Geraye Healthcare Logo" style="max-width: 150px; margin-bottom: 10px;">
          <th>Category</th>
          <th>Type</th>
          <th>Serial Number</th>
          <th>Status</th>
          <th>Purchase Date</th>
          <th>Warranty Expiry</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>{{ props.inventoryItem.name }}</td>
          <td>{{ props.inventoryItem.item_category }}</td>
          <td>{{ props.inventoryItem.item_type }}</td>
          <td>{{ props.inventoryItem.serial_number }}</td>
          <td>{{ props.inventoryItem.status }}</td>
          <td>{{ props.inventoryItem.purchase_date ? format(new Date(props.inventoryItem.purchase_date), 'PPP') : '-' }}</td>
          <td>{{ props.inventoryItem.warranty_expiry ? format(new Date(props.inventoryItem.warranty_expiry), 'PPP') : '-' }}</td>
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
