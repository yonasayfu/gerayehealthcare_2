<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { computed, onMounted, ref } from 'vue';

const props = defineProps({
  supplier: {
    type: [Object, Array],
    default: () => []
  }
});

const suppliers = computed(() => {
  return Array.isArray(props.supplier) ? props.supplier : [props.supplier];
});

const isLoading = ref(true);

onMounted(() => {
  // Simulate a small delay to allow data to be available
  setTimeout(() => {
    isLoading.value = false;
    if (suppliers.value.length > 0) {
      window.print();
    }
  }, 500);
});

</script>

<template>
  <Head :title="Array.isArray(props.supplier) ? 'Print All Suppliers' : `Print Supplier: ${!Array.isArray(props.supplier) && props.supplier ? props.supplier.name : ''}`" />

  <div class="print-document">
    <div v-if="isLoading" class="loading-indicator">
      <p>Loading print preview...</p>
    </div>

    <div v-else-if="suppliers.length === 0" class="no-data-message">
      <p>No suppliers found to print.</p>
    </div>

    <div v-else>
      <div class="print-header">
        <img src="/images/geraye_logo.jpeg" alt="Geraye Healthcare Logo" style="max-width: 150px; margin-bottom: 10px;">
        <h1>{{ Array.isArray(props.supplier) ? 'All Suppliers' : `Supplier: ${!Array.isArray(props.supplier) && props.supplier ? props.supplier.name : ''}` }}</h1>
        <p>Generated on: {{ format(new Date(), 'PPP p') }}</p>
      </div>

      <table class="print-table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Contact Person</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Address</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="s in suppliers" :key="s.id">
            <td>{{ s.name }}</td>
            <td>{{ s.contact_person }}</td>
            <td>{{ s.email }}</td>
            <td>{{ s.phone }}</td>
            <td>{{ s.address }}</td>
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