<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { computed, onMounted, ref } from 'vue';

const props = defineProps({
  payouts: {
    type: [Object, Array],
    default: () => []
  },
});

const allPayouts = computed(() => {
  return Array.isArray(props.payouts) ? props.payouts : [props.payouts];
});

const isLoading = ref(true);

onMounted(() => {
  setTimeout(() => {
    isLoading.value = false;
    if (allPayouts.value.length > 0) {
      window.print();
    }
  }, 500);

  window.onafterprint = () => {
    setTimeout(() => {
      window.close();
    }, 50);
  };
});

const formatCurrency = (value: string | null) => {
  const amount = parseFloat(value || '0');
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(amount);
};

</script>

<template>
  <Head title="Print All Staff Payouts" />

  <div class="print-document">
    <div v-if="isLoading" class="loading-indicator">
      <p>Loading print preview...</p>
    </div>

    <div v-else-if="allPayouts.length === 0" class="no-data-message">
      <p>No staff payouts found to print.</p>
    </div>

    <div v-else>
      <div class="print-header">
        <img src="/images/geraye_logo.jpeg" alt="Geraye Home Care Services Logo" style="max-width: 150px; margin-bottom: 10px;">
        <h1>All Staff Payouts</h1>
        <p>Generated on: {{ format(new Date(), 'PPP p') }}</p>
      </div>

      <table class="print-table">
        <thead>
          <tr>
            <th>Staff Member</th>
            <th>Total Amount</th>
            <th>Payout Date</th>
            <th>Notes</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="payout in allPayouts" :key="payout.id">
            <td>{{ payout.staff.first_name }} {{ payout.staff.last_name }}</td>
            <td>{{ formatCurrency(payout.total_amount) }}</td>
            <td>{{ payout.payout_date ? format(new Date(payout.payout_date), 'PPP') : '-' }}</td>
            <td>{{ payout.notes ?? '-' }}</td>
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