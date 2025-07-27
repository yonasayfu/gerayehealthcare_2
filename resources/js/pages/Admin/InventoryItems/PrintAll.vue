<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { computed, onMounted, ref } from 'vue';

const props = defineProps({
  inventoryItems: {
    type: Array,
    required: true
  },
  filters: {
    type: Object,
    default: () => ({})
  }
});

const isLoading = ref(true);

onMounted(() => {
  isLoading.value = false;
});

</script>

<template>
  <Head title="Print All Inventory Items" />

  <div class="print-document">
    <div class="print-header">
      <img src="/images/geraye_logo.jpeg" alt="Geraye Healthcare Logo" style="max-width: 150px; margin-bottom: 10px;">
      <h1>All Inventory Items</h1>
      <p>Generated on: {{ format(new Date(), 'PPP p') }}</p>
    </div>

    <table class="print-table">
      <thead>
        <tr>
          <th>Name</th>
          <th>Category</th>
          <th>Type</th>
          <th>Serial Number</th>
          <th>Status</th>
          <th>Purchase Date</th>
          <th>Warranty Expiry</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in inventoryItems" :key="item.id">
          <td>{{ item.name }}</td>
          <td>{{ item.item_category }}</td>
          <td>{{ item.item_type }}</td>
          <td>{{ item.serial_number }}</td>
          <td>{{ item.status }}</td>
          <td>{{ item.purchase_date ? format(new Date(item.purchase_date), 'PPP') : '-' }}</td>
          <td>{{ item.warranty_expiry ? format(new Date(item.warranty_expiry), 'PPP') : '-' }}</td>
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
