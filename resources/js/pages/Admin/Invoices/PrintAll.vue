<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { computed, onMounted, ref } from 'vue';

const props = defineProps({
  invoices: {
    type: [Object, Array],
    default: () => []
  },
});

const allInvoices = computed(() => {
  return Array.isArray(props.invoices) ? props.invoices : [props.invoices];
});

const isLoading = ref(true);

onMounted(() => {
  setTimeout(() => {
    isLoading.value = false;
    if (allInvoices.value.length > 0) {
      window.print();
    }
  }, 500);

  window.onafterprint = () => {
    setTimeout(() => {
      window.close();
    }, 50);
  };
});

const formatCurrency = (value: number | string) => {
  const amount = typeof value === 'string' ? parseFloat(value) : value;
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(amount);
};

const formatDate = (dateString: string) => {
  return format(new Date(dateString), 'MMM dd, yyyy');
};

</script>

<template>
  <Head title="Print All Invoices" />

  <div class="print-document">
    <div v-if="isLoading" class="loading-indicator">
      <p>Loading print preview...</p>
    </div>

    <div v-else-if="allInvoices.length === 0" class="no-data-message">
      <p>No invoices found to print.</p>
    </div>

    <div v-else>
      <div class="print-header">
        <img src="/images/geraye_logo.jpeg" alt="Geraye Healthcare Logo" style="max-width: 150px; margin-bottom: 10px;">
        <h1>All Invoices</h1>
        <p>Generated on: {{ format(new Date(), 'PPP p') }}</p>
      </div>

      <table class="print-table">
        <thead>
          <tr>
            <th>Invoice #</th>
            <th>Patient</th>
            <th>Invoice Date</th>
            <th>Due Date</th>
            <th>Amount</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="invoice in allInvoices" :key="invoice.id">
            <td>{{ invoice.invoice_number }}</td>
            <td>{{ invoice.patient.full_name }}</td>
            <td>{{ formatDate(invoice.invoice_date) }}</td>
            <td>{{ formatDate(invoice.due_date) }}</td>
            <td>{{ formatCurrency(invoice.grand_total) }}</td>
            <td>{{ invoice.status }}</td>
          </tr>
        </tbody>
      </table>
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