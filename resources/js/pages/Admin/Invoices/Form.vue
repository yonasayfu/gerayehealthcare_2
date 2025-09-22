<script setup lang="ts">
import { computed } from 'vue';
import { CheckCircle, XCircle } from 'lucide-vue-next';

const props = defineProps<{
  form: any;
  patients: Array<{ id: number; full_name: string }>;
  billableVisits: Array<any>;
  selectedPatientId?: string | null;
}>();

const emit = defineEmits<{
  (e: 'submit'): void;
}>();

const selectedPatient = computed(() => {
  return props.patients.find(p => p.id === Number(props.form.patient_id));
});

const filteredVisits = computed(() => {
  if (!props.form.patient_id) return [];
  return props.billableVisits.filter(visit => 
    visit.patient_id === Number(props.form.patient_id)
  );
});

const toggleVisitSelection = (visitId: number) => {
  const index = props.form.visit_ids.indexOf(visitId);
  if (index > -1) {
    props.form.visit_ids.splice(index, 1);
  } else {
    props.form.visit_ids.push(visitId);
  }
};

const selectAllVisits = () => {
  props.form.visit_ids = filteredVisits.value.map(visit => visit.id);
};

const deselectAllVisits = () => {
  props.form.visit_ids = [];
};

const totalAmount = computed(() => {
  return filteredVisits.value
    .filter(visit => props.form.visit_ids.includes(visit.id))
    .reduce((sum, visit) => sum + (visit.total_cost || 0), 0);
});
</script>

<template>
  <div class="space-y-6">
    <!-- Patient Selection -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
          Patient *
        </label>
        <select
          v-model="form.patient_id"
          class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white"
          :class="{ 'border-red-500': form.errors.patient_id }"
        >
          <option value="">Select a patient</option>
          <option
            v-for="patient in patients"
            :key="patient.id"
            :value="patient.id"
          >
            {{ patient.full_name }}
          </option>
        </select>
        <div v-if="form.errors.patient_id" class="mt-1 text-sm text-red-600">
          {{ form.errors.patient_id }}
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
          Invoice Date *
        </label>
        <input
          v-model="form.invoice_date"
          type="date"
          class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white"
          :class="{ 'border-red-500': form.errors.invoice_date }"
        />
        <div v-if="form.errors.invoice_date" class="mt-1 text-sm text-red-600">
          {{ form.errors.invoice_date }}
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
          Due Date *
        </label>
        <input
          v-model="form.due_date"
          type="date"
          class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white"
          :class="{ 'border-red-500': form.errors.due_date }"
        />
        <div v-if="form.errors.due_date" class="mt-1 text-sm text-red-600">
          {{ form.errors.due_date }}
        </div>
      </div>
    </div>

    <!-- Visit Selection -->
    <div v-if="form.patient_id" class="border rounded-lg p-4 dark:border-gray-700">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
          Select Visits
        </h3>
        <div class="flex space-x-2">
          <button
            type="button"
            @click="selectAllVisits"
            class="text-sm text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
          >
            Select All
          </button>
          <button
            type="button"
            @click="deselectAllVisits"
            class="text-sm text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
          >
            Deselect All
          </button>
        </div>
      </div>

      <div v-if="filteredVisits.length === 0" class="text-center py-4 text-gray-500">
        No billable visits found for this patient.
      </div>

      <div v-else class="space-y-3">
        <div
          v-for="visit in filteredVisits"
          :key="visit.id"
          class="flex items-center justify-between p-3 border rounded-md dark:border-gray-700"
          :class="{
            'bg-indigo-50 border-indigo-200 dark:bg-indigo-900/20 dark:border-indigo-700': form.visit_ids.includes(visit.id)
          }"
        >
          <div class="flex items-center">
            <input
              type="checkbox"
              :id="`visit-${visit.id}`"
              :checked="form.visit_ids.includes(visit.id)"
              @change="() => toggleVisitSelection(visit.id)"
              class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
            />
            <label :for="`visit-${visit.id}`" class="ml-3 flex flex-col">
              <span class="text-sm font-medium text-gray-900 dark:text-white">
                {{ visit.service?.name || 'Unknown Service' }}
              </span>
              <span class="text-sm text-gray-500 dark:text-gray-400">
                {{ new Date(visit.visit_date).toLocaleDateString() }} • 
                {{ visit.staff?.full_name || 'Unknown Provider' }}
              </span>
            </label>
          </div>
          <div class="text-right">
            <div class="text-sm font-medium text-gray-900 dark:text-white">
              ${{ visit.total_cost?.toFixed(2) || '0.00' }}
            </div>
          </div>
        </div>
      </div>

      <!-- Total Amount -->
      <div v-if="filteredVisits.length > 0" class="mt-4 pt-4 border-t dark:border-gray-700">
        <div class="flex justify-between items-center">
          <span class="text-lg font-medium text-gray-900 dark:text-white">Total Amount:</span>
          <span class="text-xl font-bold text-indigo-600 dark:text-indigo-400">
            ${{ totalAmount.toFixed(2) }}
          </span>
        </div>
      </div>
    </div>

    <!-- Notes -->
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
        Notes
      </label>
      <textarea
        v-model="form.notes"
        rows="3"
        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white"
        placeholder="Add any additional notes for this invoice..."
      ></textarea>
    </div>
  </div>
</template>