<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import type { InvoiceFormType } from '@/types/invoice';

const props = defineProps<{
  form: InvoiceFormType;
  patients: Array<{ id: number; full_name: string }>;
  selectedPatientId: string | null;
  billableVisits: Array<any>;
  isEdit?: boolean;
}>();

const emit = defineEmits(['patient-selected']);

const localSelectedPatient = ref(props.selectedPatientId);

watch(localSelectedPatient, (newPatientId) => {
  emit('patient-selected', newPatientId);
});

// If in edit mode, ensure the patient is pre-selected and not changeable
const patientSelectDisabled = computed(() => props.isEdit && props.selectedPatientId !== null);

</script>

<template>
  <div class="p-4 bg-white rounded-lg shadow space-y-4">
    <div v-if="!isEdit">
      <label for="patient" class="block text-sm font-medium text-gray-700">Step 1: Select a Patient</label>
      <select id="patient" v-model="localSelectedPatient" :disabled="patientSelectDisabled" class="mt-1 block w-full md:w-1/3 rounded-md border-gray-300 shadow-sm">
        <option disabled value="">-- Select a Patient --</option>
        <option v-for="patient in patients" :key="patient.id" :value="patient.id">{{ patient.full_name }}</option>
      </select>
    </div>

    <div v-if="localSelectedPatient || isEdit">
      <h2 class="text-lg font-semibold" :class="{ 'pt-4 border-t': !isEdit }">Step 2: Select Billable Visits</h2>
      
      <div v-if="billableVisits.length === 0" class="text-center text-gray-500 py-4">
        No billable visits found for this patient.
      </div>
      
      <div v-else class="space-y-2 border rounded-md p-2">
        <label v-for="visit in billableVisits" :key="visit.id" class="flex items-center gap-4 p-2 hover:bg-gray-50 rounded-md">
          <input type="checkbox" :value="visit.id" v-model="form.visit_ids" class="rounded" />
          <span class="flex-1">{{ visit.service_description || 'Standard Visit' }} on {{ new Date(visit.scheduled_at).toLocaleDateString() }}</span>
          <span class="font-mono">${{ visit.cost }}</span>
        </label>
      </div>

      <div class="grid md:grid-cols-2 gap-4 pt-4 border-t">
          <div>
              <label for="invoice_date" class="block text-sm font-medium">Invoice Date</label>
              <input type="date" v-model="form.invoice_date" id="invoice_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
          </div>
          <div>
              <label for="due_date" class="block text-sm font-medium">Due Date</label>
              <input type="date" v-model="form.due_date" id="due_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
          </div>
      </div>
    </div>
  </div>
</template>
