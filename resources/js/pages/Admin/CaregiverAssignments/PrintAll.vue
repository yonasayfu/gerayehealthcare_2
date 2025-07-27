<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { computed, onMounted, ref } from 'vue';

const props = defineProps({
  assignments: {
    type: [Object, Array],
    default: () => []
  },
});

const assignments = computed(() => {
  return Array.isArray(props.assignments) ? props.assignments : [props.assignments];
});

const isLoading = ref(true);

onMounted(() => {
  setTimeout(() => {
    isLoading.value = false;
    if (assignments.value.length > 0) {
      window.print();
    }
  }, 500);

  window.onafterprint = () => {
    setTimeout(() => {
      window.close();
    }, 50);
  };
});

const getStaffFullName = (staff) => {
    if (!staff) return 'N/A';
    return `${staff.first_name || ''} ${staff.last_name || ''}`.trim();
}

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleString();
}

</script>

<template>
  <Head title="Print All Caregiver Assignments" />

  <div class="print-document">
    <div v-if="isLoading" class="loading-indicator">
      <p>Loading print preview...</p>
    </div>

    <div v-else-if="assignments.length === 0" class="no-data-message">
      <p>No caregiver assignments found to print.</p>
    </div>

    <div v-else>
      <div class="print-header">
        <img src="/images/geraye_logo.jpeg" alt="Geraye Healthcare Logo" style="max-width: 150px; margin-bottom: 10px;">
        <h1>All Caregiver Assignments</h1>
        <p>Generated on: {{ format(new Date(), 'PPP p') }}</p>
      </div>

      <table class="print-table">
        <thead>
          <tr>
            <th>Patient</th>
            <th>Staff Member</th>
            <th>Shift Start</th>
            <th>Shift End</th>
            <th>Status</th>
            <th>Date Created</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="assignment in assignments" :key="assignment.id">
            <td>{{ assignment.patient?.full_name ?? 'N/A' }}</td>
            <td>{{ getStaffFullName(assignment.staff) }}</td>
            <td>{{ formatDate(assignment.shift_start) }}</td>
            <td>{{ formatDate(assignment.shift_end) }}</td>
            <td>{{ assignment.status }}</td>
            <td>{{ formatDate(assignment.created_at) }}</td>
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