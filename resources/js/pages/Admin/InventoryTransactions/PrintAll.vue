<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { computed, onMounted, ref } from 'vue';

const props = defineProps({
  inventoryTransactions: {
    type: [Object, Array],
    default: () => []
  },
});

const transactions = computed(() => {
  return Array.isArray(props.inventoryTransactions) ? props.inventoryTransactions : [props.inventoryTransactions];
});

const isLoading = ref(true);

onMounted(() => {
  setTimeout(() => {
    isLoading.value = false;
    if (transactions.value.length > 0) {
      window.print();
    }
  }, 500);

  window.onafterprint = () => {
    setTimeout(() => {
      window.close();
    }, 50);
  };
});

</script>

<template>
  <Head title="Print All Inventory Transactions" />

  <div class="print-document">
    <div v-if="isLoading" class="loading-indicator">
      <p>Loading print preview...</p>
    </div>

    <div v-else-if="transactions.length === 0" class="no-data-message">
      <p>No inventory transactions found to print.</p>
    </div>

    <div v-else>
      <div class="print-header">
        <img src="/images/geraye_logo.jpeg" alt="Geraye Healthcare Logo" style="max-width: 150px; margin-bottom: 10px;">
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
          <tr v-for="transaction in transactions" :key="transaction.id">
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
      <div class="print-footer">
        <hr class="my-2 border-gray-300">
        <p>Document Generated: {{ format(new Date(), 'PPP p') }}</p>
      </div>
    </div>
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
