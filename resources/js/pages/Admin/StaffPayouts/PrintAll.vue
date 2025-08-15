<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { computed } from 'vue'
import { format } from 'date-fns'

// Import main CSS to match other modules' print styling
import '../../../../css/app.css'

// Disable default Inertia layout for a clean print/preview page
defineOptions({ layout: null })

const props = defineProps({
  payouts: {
    type: [Object, Array],
    default: () => []
  },
});

const allPayouts = computed(() => Array.isArray(props.payouts) ? props.payouts : [props.payouts])

const doPrint = () => window.print()

const formattedGeneratedDate = computed(() => {
  return format(new Date(), 'PPP p')
})

const formatCurrency = (value: string | null) => {
  const amount = parseFloat(value || '0');
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(amount);
};

</script>

<template>
  <Head title="All Staff Payouts Print View" />

  <div class="print-container">
    <!-- Preview toolbar (hidden in print) -->
    <div class="no-print flex items-center justify-between mb-4">
      <div class="text-sm text-gray-600">Preview • {{ allPayouts.length }} payouts</div>
      <div class="flex gap-2">
        <button @click="doPrint" class="btn btn-dark">Print</button>
        <a :href="route('admin.staff-payouts.index')" class="btn btn-outline">Back</a>
      </div>
    </div>
    <div class="print-header-content">
      <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
      <h1 class="print-clinic-name">Geraye Home Care Services</h1>
      <p class="print-document-title">All Staff Payouts</p>
      <hr class="my-3 border-gray-300 print:my-2">
    </div>

    <div class="overflow-x-auto print-table-container">
      <table class="w-full text-left text-sm text-gray-800">
        <thead class="bg-gray-100 text-xs uppercase text-muted-foreground print-table-header">
          <tr>
            <th class="px-6 py-3">#</th>
            <th class="px-6 py-3">Staff Member</th>
            <th class="px-6 py-3">Total Amount</th>
            <th class="px-6 py-3">Payout Date</th>
            <th class="px-6 py-3">Notes</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="allPayouts.length === 0">
            <td colspan="5" class="text-center px-6 py-4 text-gray-400">No staff payouts found.</td>
          </tr>
          <tr v-for="(payout, idx) in allPayouts" :key="payout.id" class="border-b print-table-row">
            <td class="px-6 py-4">{{ idx + 1 }}</td>
            <td class="px-6 py-4">{{ payout.staff.first_name }} {{ payout.staff.last_name }}</td>
            <td class="px-6 py-4">{{ new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(parseFloat(payout.total_amount || '0')) }}</td>
            <td class="px-6 py-4">{{ payout.payout_date ? format(new Date(payout.payout_date), 'yyyy-MM-dd') : '-' }}</td>
            <td class="px-6 py-4">{{ payout.notes ?? '-' }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="text-center mt-4 text-sm text-gray-500 print-footer">
      <hr class="my-2 border-gray-300">
      <p>Document Generated: {{ formattedGeneratedDate }}</p>
    </div>
  </div>
</template>

<style>
/* Print-specific styles for PrintAll.vue */
@media print {
  @page { size: A4 landscape; margin: 0.5cm; }
  body { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; color: #000 !important; margin: 0 !important; padding: 0 !important; overflow: visible !important; }
  .print-container { padding: 1cm; transform: scale(0.95); transform-origin: top left; width: 100%; height: auto; }
  .print-header-content { text-align: center; margin-bottom: 0.8cm; }
  .print-logo { max-width: 150px; max-height: 50px; margin-bottom: 0.5rem; display: block; margin-left: auto; margin-right: auto; }
  .print-clinic-name { font-size: 1.6rem !important; margin-bottom: 0.2rem !important; line-height: 1.2 !important; font-weight: bold; }
  .print-document-title { font-size: 0.85rem !important; color: #555 !important; }
  hr { border-color: #ccc !important; }
  .print-table-container { box-shadow: none !important; border-radius: 0 !important; overflow: visible !important; }
  table { width: 100% !important; border-collapse: collapse !important; font-size: 0.8rem !important; }
  thead { background-color: #f0f0f0 !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
  th, td { border: 1px solid #ddd !important; padding: 0.5rem 0.75rem !important; color: #000 !important; }
  th { font-weight: bold !important; text-transform: uppercase !important; font-size: 0.75rem !important; }
  .print-table-row:nth-child(even) { background-color: #f9f9f9 !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
  .print-table-row:last-child { border-bottom: 1px solid #ddd !important; }
}
</style>